#!/usr/bin/env sh

set -eu

NAMESPACE="${NAMESPACE:-event-booking}"
SERVICE="${SERVICE:-db}"
TARGET="${TARGET:-pod}"
POD_SELECTOR="${POD_SELECTOR:-app=db}"
POD="${POD:-}"
POD_IP="${POD_IP:-}"
NODE_NAME="${NODE_NAME:-}"
TUNNEL_MODE="${TUNNEL_MODE:-auto}"
SSH_HOST="${SSH_HOST:-}"
SSH_USER="${SSH_USER:-}"
SECRET="${SECRET:-event-booking-secrets}"
READ_SECRET="${READ_SECRET:-false}"
READ_CONFIG="${READ_CONFIG:-false}"
LOCAL_ADDRESS="${LOCAL_ADDRESS:-127.0.0.1}"
LOCAL_PORT="${LOCAL_PORT:-15432}"
REMOTE_PORT="${REMOTE_PORT:-5432}"
SHOW_PASSWORD="${SHOW_PASSWORD:-false}"
READY_TIMEOUT="${READY_TIMEOUT:-15}"
KUBECTL_REQUEST_TIMEOUT="${KUBECTL_REQUEST_TIMEOUT:-10s}"
CHECK_DATABASE="${CHECK_DATABASE:-false}"
PORT_FORWARD_PID=""
TUNNEL_PROCESS_LABEL="tunnel process"

usage() {
    cat <<'EOF'
Usage: bin/k8s-db-tunnel.sh [options]

Opens a local-only kubectl tunnel from your workstation/IDE to the Kubernetes
PostgreSQL database. The database service stays internal to the cluster.

Options:
  -n, --namespace <name>       Kubernetes namespace. Default: event-booking
  -p, --local-port <port>      Local port for your IDE. Default: 15432
  -a, --address <address>      Local bind address. Default: 127.0.0.1
      --mode <auto|kubectl|ssh>
                              Tunnel implementation. Default: auto
      --target <pod|service>   kubectl target type. Default: pod
      --pod <name>             Explicit pod name when --target=pod
      --pod-ip <ip>            Explicit pod IP for --mode=ssh
      --ssh-host <host>        SSH host for --mode=ssh. Defaults to the pod node name
      --ssh-user <user>        Optional SSH user for --mode=ssh
      --selector <selector>    Pod selector when --target=pod. Default: app=db
  -s, --service <name>         Kubernetes service name when --target=service. Default: db
  -r, --remote-port <port>     Kubernetes service port. Default: 5432
      --ready-timeout <sec>    Seconds to wait for the local port. Default: 15
      --kubectl-timeout <dur>  Timeout for Kubernetes API lookups. Default: 10s
      --check                  Run pg_isready through the tunnel when available
      --secret <name>          Secret containing POSTGRES_* values. Default: event-booking-secrets
      --read-config            Try to read POSTGRES_* values from the pod environment
      --read-secret            Fall back to reading POSTGRES_* values from the Kubernetes secret
      --show-password          Print the database password for IDE setup
  -h, --help                   Show this help

Environment overrides:
  NAMESPACE, SERVICE, TARGET, TUNNEL_MODE, POD_SELECTOR, POD, POD_IP, NODE_NAME,
  SSH_HOST, SSH_USER, SECRET, LOCAL_ADDRESS, LOCAL_PORT, REMOTE_PORT, READY_TIMEOUT,
  KUBECTL_REQUEST_TIMEOUT, READ_CONFIG, READ_SECRET, SHOW_PASSWORD, CHECK_DATABASE
EOF
}

require_value() {
    if [ "$#" -lt 2 ] || [ -z "$2" ]; then
        echo "Missing value for $1" >&2
        usage >&2
        exit 1
    fi
}

cleanup() {
    if [ -n "$PORT_FORWARD_PID" ] && kill -0 "$PORT_FORWARD_PID" 2>/dev/null; then
        echo
        echo "Closing database tunnel..."
        kill "$PORT_FORWARD_PID" 2>/dev/null || true
        sleep 1
        if kill -0 "$PORT_FORWARD_PID" 2>/dev/null; then
            kill -9 "$PORT_FORWARD_PID" 2>/dev/null || true
        fi
        wait "$PORT_FORWARD_PID" 2>/dev/null || true
    fi
}

port_is_open() {
    if command -v nc >/dev/null 2>&1; then
        nc -z "$LOCAL_ADDRESS" "$LOCAL_PORT" >/dev/null 2>&1

        return $?
    fi

    if command -v lsof >/dev/null 2>&1; then
        lsof -nP -iTCP@"$LOCAL_ADDRESS":"$LOCAL_PORT" -sTCP:LISTEN >/dev/null 2>&1

        return $?
    fi

    return 1
}

wait_for_tunnel() {
    remaining="$READY_TIMEOUT"
    while [ "$remaining" -gt 0 ]; do
        if ! kill -0 "$PORT_FORWARD_PID" 2>/dev/null; then
            echo "$TUNNEL_PROCESS_LABEL exited before the tunnel became ready." >&2
            wait "$PORT_FORWARD_PID" 2>/dev/null || true

            return 1
        fi

        if port_is_open; then
            return 0
        fi

        sleep 1
        remaining=$((remaining - 1))
    done

    echo "Timed out waiting for $LOCAL_ADDRESS:$LOCAL_PORT to accept connections." >&2
    echo "Check that the db pod is ready and that local port $LOCAL_PORT is not already in use." >&2

    return 1
}

resolve_port_forward_target() {
    case "$TARGET" in
        pod)
            resolve_pod_details
            printf 'pod/%s' "$POD"
            ;;
        service)
            printf 'service/%s' "$SERVICE"
            ;;
        *)
            echo "--target must be either pod or service." >&2
            exit 1
            ;;
    esac
}

resolve_pod_details() {
    if [ -n "$POD" ] && [ -n "$POD_IP" ] && [ -n "$NODE_NAME" ]; then
        return 0
    fi

    if [ -z "$POD" ]; then
        details="$(kubectl get pods \
                    -n "$NAMESPACE" \
                    -l "$POD_SELECTOR" \
                    --field-selector=status.phase=Running \
                    --request-timeout="$KUBECTL_REQUEST_TIMEOUT" \
                    -o 'jsonpath={.items[0].metadata.name}{" "}{.items[0].status.podIP}{" "}{.items[0].spec.nodeName}')"
        set -- $details
        POD="${1:-}"
        POD_IP="${2:-}"
        NODE_NAME="${3:-}"
    else
        details="$(kubectl get pod "$POD" \
                    -n "$NAMESPACE" \
                    --request-timeout="$KUBECTL_REQUEST_TIMEOUT" \
                    -o 'jsonpath={.status.podIP}{" "}{.spec.nodeName}')"
        set -- $details
        POD_IP="${POD_IP:-${1:-}}"
        NODE_NAME="${NODE_NAME:-${2:-}}"
    fi

    if [ -z "$POD" ]; then
        echo "Could not find a running db pod in namespace $NAMESPACE with selector $POD_SELECTOR." >&2
        exit 1
    fi
}

start_kubectl_tunnel() {
    if ! KUBE_TARGET="$(resolve_port_forward_target)"; then
        return 1
    fi
    echo "Starting kubectl tunnel to $KUBE_TARGET.$NAMESPACE:$REMOTE_PORT..."

    KUBECTL_PORT_FORWARD_WEBSOCKETS="${KUBECTL_PORT_FORWARD_WEBSOCKETS:-false}" \
        kubectl port-forward \
            -n "$NAMESPACE" \
            --address "$LOCAL_ADDRESS" \
            "$KUBE_TARGET" \
            "$LOCAL_PORT:$REMOTE_PORT" &

    PORT_FORWARD_PID="$!"
    TUNNEL_PROCESS_LABEL="kubectl port-forward"
}

start_ssh_tunnel() {
    if [ -z "$POD_IP" ] || { [ -z "$SSH_HOST" ] && [ -z "$NODE_NAME" ]; }; then
        resolve_pod_details
    fi
    if [ -z "$POD_IP" ]; then
        echo "Could not resolve the DB pod IP for SSH tunnelling." >&2
        exit 1
    fi

    SSH_HOST="${SSH_HOST:-$NODE_NAME}"
    if [ -z "$SSH_HOST" ]; then
        echo "Could not resolve the DB node name. Pass --ssh-host." >&2
        exit 1
    fi

    if [ -n "$SSH_USER" ]; then
        SSH_DESTINATION="$SSH_USER@$SSH_HOST"
    else
        SSH_DESTINATION="$SSH_HOST"
    fi

    if ! command -v ssh >/dev/null 2>&1; then
        echo "ssh is required for --mode=ssh but was not found on PATH." >&2
        exit 1
    fi

    echo "Starting SSH tunnel via $SSH_DESTINATION to $POD_IP:$REMOTE_PORT..."
    ssh \
        -N \
        -L "$LOCAL_ADDRESS:$LOCAL_PORT:$POD_IP:$REMOTE_PORT" \
        "$SSH_DESTINATION" &

    PORT_FORWARD_PID="$!"
    TUNNEL_PROCESS_LABEL="ssh tunnel"
}

start_tunnel() {
    case "$TUNNEL_MODE" in
        auto|kubectl)
            start_kubectl_tunnel
            if wait_for_tunnel; then
                return 0
            fi

            cleanup
            PORT_FORWARD_PID=""
            if [ "$TUNNEL_MODE" = "kubectl" ]; then
                return 1
            fi

            echo "kubectl port-forward did not become ready; trying SSH fallback."
            start_ssh_tunnel
            wait_for_tunnel
            ;;
        ssh)
            start_ssh_tunnel
            wait_for_tunnel
            ;;
        *)
            echo "--mode must be auto, kubectl, or ssh." >&2
            exit 1
            ;;
    esac
}

get_secret_value() {
    kubectl get secret "$SECRET" \
        -n "$NAMESPACE" \
        --request-timeout="$KUBECTL_REQUEST_TIMEOUT" \
        -o "go-template={{ index .data \"$1\" | base64decode }}"
}

get_pod_env_value() {
    if [ -z "$POD" ]; then
        return 1
    fi

    kubectl exec \
        -n "$NAMESPACE" \
        "$POD" \
        --request-timeout="$KUBECTL_REQUEST_TIMEOUT" \
        -- printenv "$1"
}

read_config_value() {
    key="$1"
    if value="$(get_pod_env_value "$key" 2>/dev/null)" && [ -n "$value" ]; then
        printf '%s' "$value"

        return 0
    fi

    if [ "$READ_SECRET" = "true" ] && value="$(get_secret_value "$key" 2>/dev/null)" && [ -n "$value" ]; then
        printf '%s' "$value"

        return 0
    fi

    return 1
}

trap 'cleanup; exit 130' INT
trap 'cleanup; exit 143' TERM
trap 'cleanup' EXIT

while [ "$#" -gt 0 ]; do
    case "$1" in
        -n|--namespace)
            require_value "$1" "${2:-}"
            NAMESPACE="$2"
            shift
            ;;
        -p|--local-port)
            require_value "$1" "${2:-}"
            LOCAL_PORT="$2"
            shift
            ;;
        -a|--address)
            require_value "$1" "${2:-}"
            LOCAL_ADDRESS="$2"
            shift
            ;;
        --mode)
            require_value "$1" "${2:-}"
            TUNNEL_MODE="$2"
            shift
            ;;
        --target)
            require_value "$1" "${2:-}"
            TARGET="$2"
            shift
            ;;
        --pod)
            require_value "$1" "${2:-}"
            POD="$2"
            shift
            ;;
        --pod-ip)
            require_value "$1" "${2:-}"
            POD_IP="$2"
            shift
            ;;
        --ssh-host)
            require_value "$1" "${2:-}"
            SSH_HOST="$2"
            shift
            ;;
        --ssh-user)
            require_value "$1" "${2:-}"
            SSH_USER="$2"
            shift
            ;;
        --selector)
            require_value "$1" "${2:-}"
            POD_SELECTOR="$2"
            shift
            ;;
        -s|--service)
            require_value "$1" "${2:-}"
            SERVICE="$2"
            shift
            ;;
        -r|--remote-port)
            require_value "$1" "${2:-}"
            REMOTE_PORT="$2"
            shift
            ;;
        --ready-timeout)
            require_value "$1" "${2:-}"
            READY_TIMEOUT="$2"
            shift
            ;;
        --kubectl-timeout)
            require_value "$1" "${2:-}"
            KUBECTL_REQUEST_TIMEOUT="$2"
            shift
            ;;
        --check)
            CHECK_DATABASE=true
            READ_CONFIG=true
            ;;
        --secret)
            require_value "$1" "${2:-}"
            SECRET="$2"
            shift
            ;;
        --read-secret)
            READ_SECRET=true
            ;;
        --read-config)
            READ_CONFIG=true
            ;;
        --show-password)
            SHOW_PASSWORD=true
            READ_CONFIG=true
            ;;
        -h|--help)
            usage
            exit 0
            ;;
        *)
            echo "Unknown argument: $1" >&2
            usage >&2
            exit 1
            ;;
    esac
    shift
done

if ! command -v kubectl >/dev/null 2>&1; then
    echo "kubectl is required but was not found on PATH." >&2
    exit 1
fi

if [ "$TUNNEL_MODE" = "ssh" ] && [ -n "$SSH_HOST" ] && [ -n "$POD_IP" ]; then
    KUBE_TARGET="ssh/$SSH_HOST"
elif ! KUBE_TARGET="$(resolve_port_forward_target)"; then
    exit 1
fi

POSTGRES_DB=""
POSTGRES_USER=""
POSTGRES_PASSWORD=""
if [ "$READ_CONFIG" = "true" ]; then
    POSTGRES_DB="$(read_config_value POSTGRES_DB || true)"
    POSTGRES_USER="$(read_config_value POSTGRES_USER || true)"
    POSTGRES_PASSWORD="$(read_config_value POSTGRES_PASSWORD || true)"
fi

if [ "$SHOW_PASSWORD" = "true" ] && [ -z "$POSTGRES_PASSWORD" ]; then
    echo "Could not read POSTGRES_PASSWORD from the pod environment." >&2
    echo "Run with --read-secret if you want to fall back to secret/$SECRET." >&2
    echo "Current kubectl context: $(kubectl config current-context 2>/dev/null || echo unknown)" >&2
fi

echo "Opening secure local database tunnel..."
echo "  Kubernetes: $KUBE_TARGET.$NAMESPACE:$REMOTE_PORT"
echo "  Local:      $LOCAL_ADDRESS:$LOCAL_PORT"
echo
echo "IDE connection settings:"
echo "  Type:       PostgreSQL"
echo "  Host:       $LOCAL_ADDRESS"
echo "  Port:       $LOCAL_PORT"
echo "  Database:   ${POSTGRES_DB:-not available; check POSTGRES_DB in Kubernetes}"
echo "  User:       ${POSTGRES_USER:-not available; check POSTGRES_USER in Kubernetes}"
if [ "$SHOW_PASSWORD" = "true" ]; then
    echo "  Password:   ${POSTGRES_PASSWORD:-not available; check POSTGRES_PASSWORD in Kubernetes}"
else
    echo "  Password:   run with --show-password to try to print it"
fi
echo "  Schema:     data"
if [ -n "$POSTGRES_DB" ]; then
    echo "  JDBC URL:   jdbc:postgresql://$LOCAL_ADDRESS:$LOCAL_PORT/$POSTGRES_DB?currentSchema=data"
else
    echo "  JDBC URL:   jdbc:postgresql://$LOCAL_ADDRESS:$LOCAL_PORT/<database>?currentSchema=data"
fi
echo

if ! start_tunnel; then
    exit 1
fi

if [ "$CHECK_DATABASE" = "true" ]; then
    if command -v pg_isready >/dev/null 2>&1; then
        if [ -n "$POSTGRES_DB" ] && [ -n "$POSTGRES_USER" ]; then
            echo "Checking PostgreSQL readiness through the tunnel..."
            PGPASSWORD="$POSTGRES_PASSWORD" pg_isready \
                -h "$LOCAL_ADDRESS" \
                -p "$LOCAL_PORT" \
                -U "$POSTGRES_USER" \
                -d "$POSTGRES_DB"
        else
            echo "Skipping pg_isready because database/user values were not available."
        fi
    else
        echo "pg_isready is not installed; TCP tunnel is open but database readiness was not checked."
    fi
fi

echo
echo "Tunnel is ready. Keep this process running while your IDE is connected."
echo "Press Ctrl+C to close the tunnel."
echo

wait "$PORT_FORWARD_PID"

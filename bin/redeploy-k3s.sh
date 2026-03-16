#!/usr/bin/env sh

set -eu

NAMESPACE="${NAMESPACE:-event-booking}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-false}"
WAIT_TIMEOUT="${WAIT_TIMEOUT:-180s}"

usage() {
    cat <<'EOF'
Usage: bin/redeploy-k3s.sh [--with-migrations]

Restarts the Kubernetes workloads for the local k3s deployment so they pull the
latest GHCR image without recreating secrets, PVCs, or the namespace.

Environment overrides:
  NAMESPACE=<namespace>       Kubernetes namespace to use. Default: event-booking
  WAIT_TIMEOUT=<duration>     Rollout wait timeout. Default: 180s
  RUN_MIGRATIONS=true         Also rerun the deploy job after restarting
EOF
}

while [ $# -gt 0 ]; do
    case "$1" in
        --with-migrations)
            RUN_MIGRATIONS=true
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

echo "Restarting app deployment in namespace '$NAMESPACE'..."
kubectl rollout restart deployment/event-booking -n "$NAMESPACE"

echo "Restarting worker deployment in namespace '$NAMESPACE'..."
kubectl rollout restart deployment/worker -n "$NAMESPACE"

echo "Waiting for app rollout..."
kubectl rollout status deployment/event-booking -n "$NAMESPACE" --timeout="$WAIT_TIMEOUT"

echo "Waiting for worker rollout..."
kubectl rollout status deployment/worker -n "$NAMESPACE" --timeout="$WAIT_TIMEOUT"

if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "Recreating deploy job..."
    kubectl delete job event-booking-deploy -n "$NAMESPACE" --ignore-not-found
    kubectl apply -f k8s/deploy-job.yaml
    echo "Streaming deploy job logs..."
    kubectl logs -n "$NAMESPACE" job/event-booking-deploy -f
fi

echo "Current pod status:"
kubectl get pods -n "$NAMESPACE"

#!/usr/bin/env sh

set -eu

NAMESPACE="${NAMESPACE:-event-booking}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-false}"
WAIT_TIMEOUT="${WAIT_TIMEOUT:-180s}"

usage() {
    cat <<'EOF'
Usage: bin/redeploy-k3s.sh [--with-migrations]

Restarts the Kubernetes workloads for the local k3s deployment so they pull the
latest GHCR image and rerun the app Deployment init container that copies code
into the shared webroot, without recreating secrets, PVCs, or the namespace.

Environment overrides:
  NAMESPACE=<namespace>       Kubernetes namespace to use. Default: event-booking
  WAIT_TIMEOUT=<duration>     Rollout wait timeout. Default: 180s
  RUN_MIGRATIONS=true         Also run the deploy job before restarting workloads
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

if [ "$RUN_MIGRATIONS" = "true" ]; then
    DEPLOY_JOB_NAME="event-booking-deploy-manual-$(date +%Y%m%d%H%M%S)"

    echo "Applying deploy CronJob manifest..."
    kubectl apply -f k8s/operations/deploy-job.yaml
    echo "Creating one-shot deploy job '$DEPLOY_JOB_NAME' from CronJob..."
    kubectl create job "$DEPLOY_JOB_NAME" -n "$NAMESPACE" --from=cronjob/event-booking-deploy
    echo "Streaming deploy job logs..."
    kubectl logs -n "$NAMESPACE" "job/$DEPLOY_JOB_NAME" -f --pod-running-timeout="$WAIT_TIMEOUT"
    echo "Waiting for deploy job completion..."
    kubectl wait --for=condition=complete "job/$DEPLOY_JOB_NAME" -n "$NAMESPACE" --timeout="$WAIT_TIMEOUT"
fi

echo "Restarting app deployment in namespace '$NAMESPACE' to trigger copy-app-code..."
kubectl rollout restart deployment/event-booking -n "$NAMESPACE"

echo "Restarting worker deployment in namespace '$NAMESPACE'..."
kubectl rollout restart deployment/worker -n "$NAMESPACE"

echo "Waiting for app rollout..."
kubectl rollout status deployment/event-booking -n "$NAMESPACE" --timeout="$WAIT_TIMEOUT"

echo "Waiting for worker rollout..."
kubectl rollout status deployment/worker -n "$NAMESPACE" --timeout="$WAIT_TIMEOUT"

echo "Current pod status:"
kubectl get pods -n "$NAMESPACE"

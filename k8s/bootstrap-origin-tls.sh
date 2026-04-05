#!/usr/bin/env bash
set -euo pipefail

HOSTNAME="event-backend.jacobagtyler.com"
NAMESPACE="event-booking"
SECRET_NAME="event-booking-backend-tls"
MANIFEST_DIR="$(cd -- "$(dirname -- "$0")" && pwd)"

tmpdir="$(mktemp -d)"
trap 'rm -rf "$tmpdir"' EXIT

openssl req -x509 -nodes -newkey rsa:2048 \
  -keyout "$tmpdir/tls.key" \
  -out "$tmpdir/tls.crt" \
  -days 7 \
  -subj "/CN=${HOSTNAME}" \
  -addext "subjectAltName=DNS:${HOSTNAME}" >/dev/null 2>&1

kubectl -n "$NAMESPACE" create secret tls "$SECRET_NAME" \
  --cert="$tmpdir/tls.crt" \
  --key="$tmpdir/tls.key" \
  --dry-run=client -o yaml | kubectl apply -f -

kubectl apply -k "$MANIFEST_DIR"

echo
echo "Bootstrap complete. Nginx can now serve HTTPS while cert-manager replaces the temporary certificate."

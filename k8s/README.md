# Event Booking K3s Notes

`kustomization.yaml` is the entrypoint for the steady-state Event Booking manifest on K3s. It currently renders the grouped resources under `base/`.

## HTTPS Bootstrap

The web service serves HTTPS directly on the `LoadBalancer` IP using the Kubernetes secret `event-booking-backend-tls`.

On a brand new cluster, nginx cannot start its `443` listener until that secret exists. The fastest bootstrap is:

```bash
./bootstrap-origin-tls.sh
```

That script:
- creates a short-lived self-signed TLS secret for `event-backend.jacobagtyler.com`
- applies `kubectl apply -k k8s`
- leaves the cert-manager `Certificate` in place so Let's Encrypt can replace the temporary certificate

## DNS

Point local DNS for `event-backend.jacobagtyler.com` to the Event Booking `LoadBalancer` IP.

## Verify

```bash
kubectl -n event-booking get svc,certificate,secret
curl -kI https://event-backend.jacobagtyler.com
```

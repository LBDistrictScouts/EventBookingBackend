# Event Booking K3s Notes

`local-k3s.yaml` is the desired steady-state manifest for the Event Booking stack on K3s.

## HTTPS Bootstrap

The web service serves HTTPS directly on the `LoadBalancer` IP using the Kubernetes secret `event-booking-backend-tls`.

On a brand new cluster, nginx cannot start its `443` listener until that secret exists. The fastest bootstrap is:

```bash
./bootstrap-origin-tls.sh
```

That script:
- creates a short-lived self-signed TLS secret for `event-backend.jacobagtyler.com`
- applies `local-k3s.yaml`
- leaves the cert-manager `Certificate` in place so Let's Encrypt can replace the temporary certificate

## DNS

Point local DNS for `event-backend.jacobagtyler.com` to the Event Booking `LoadBalancer` IP.

## Verify

```bash
kubectl -n event-booking get svc,certificate,secret
curl -kI https://event-backend.jacobagtyler.com
```

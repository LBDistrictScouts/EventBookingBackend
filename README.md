# Event Booking Backend

Backend application for event bookings, check-ins, participant management, and booking confirmation mailers. The app is built on CakePHP 5 and integrates with AWS Cognito for authentication and SQS-backed queue processing.

## Stack

- PHP 8.4+
- CakePHP 5
- PostgreSQL
- PHPUnit 13
- PHPStan 2
- PHP_CodeSniffer
- AWS Cognito
- AWS SQS

## Main Features

- Event, section, group, entry, participant, checkpoint, and check-in management
- Public booking and entry lookup endpoints
- Cognito-based authentication flow
- Booking confirmation mailer
- Queue worker command for asynchronous check-in processing

## Local Setup

1. Install dependencies:

```bash
composer install
```

2. Create local config:

```bash
cp config/app_local.example.php config/app_local.php
```

3. Set application secrets and service configuration.

Minimum environment/config values you need:

- `SECURITY_SALT`
- `DATABASE_URL`
- `DATABASE_TEST_URL`
- `AWS_REGION`
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_SQS_QUEUE_URL`
- `AWS_SQS_QUEUE_NAME`
- `COGNITO_DOMAIN`
- `COGNITO_CLIENT_ID`
- `COGNITO_CLIENT_SECRET`
- `COGNITO_USER_POOL_ID`
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`

Optional runtime values:

- `APP_FULL_BASE_URL` for generating absolute links in emails and CLI-triggered notifications
- `APP_FRONTEND_BASE_URL` for frontend confirmation email links such as `/edit/{id}`; falls back to `APP_FULL_BASE_URL` when unset
- `AWS_SESSION_TOKEN`
- `AWS_PROFILE` for non-container local CLI development when you want to use a named AWS profile instead of explicit credentials
- `SMTP_HOST`
- `SMTP_PORT`
- `SMTP_TIMEOUT`
- `SMTP_TLS`
- `SMTP_LOG`
- `SMTP_USERNAME`
- `SMTP_PASSWORD`
- `SMTP_CLIENT`
- `EMAIL_TRANSPORT_SMTP_URL`

For containerized `app` and `worker` services, Compose reads runtime secrets from `.env.compose` instead of `.env`, so `AWS_PROFILE` is not injected into containers.

4. Create the Postgres schemas used by the app and tests:

```sql
CREATE SCHEMA IF NOT EXISTS data;
CREATE SCHEMA IF NOT EXISTS test;
```

The app expects PostgreSQL schema-separated connections. In CI the main connection uses `schema=data` and the test connection uses `schema=test`.
When using the bundled Docker Compose database, the `app` and `worker` containers derive their internal `DATABASE_URL` values from `POSTGRES_DB`, `POSTGRES_USER`, and `POSTGRES_PASSWORD`, so there is only one DB source of truth inside Compose.

5. Run migrations as needed:

```bash
bin/cake migrations migrate
```

For containerized deployments, use the one-shot deploy service to run the Composer post-install hook and database migrations:

```bash
docker compose --profile deploy run --rm deploy
```

Set `CREATE_TEST_SCHEMA=true` in `.env.compose` if you also want the deploy container to create the `test` schema.

If you need to publish your shared theme package assets into the checked-out `webroot` during local container development, run the one-shot assets service:

```bash
docker compose --profile assets run --rm assets
```

Frontend assets are installed through the `DistrictUI` CakePHP plugin command:

```bash
bin/cake district_ui install --overwrite
```

Docker image builds run that command inside the image build, so the published
container includes the generated [`webroot/district_u_i`](/Users/jacob/Development/EventBookingBackend/webroot/district_u_i)
assets without relying on a separate host-side theme build step.

6. Start the local server:

```bash
bin/cake server -p 8765
```

## Testing And Quality

Run the test suite:

```bash
composer test
```

Run static analysis:

```bash
composer stan
```

Run coding standards:

```bash
composer cs-check
```

Run the full local quality gate:

```bash
composer check
```

Current local `composer check` is expected to pass cleanly.

## Queue Worker

The project includes a queue worker command:

```bash
bin/cake QueueWorker
```

The CLI bootstrap expects an AWS profile named `lbd` unless the relevant environment/config overrides are provided. CI creates that profile explicitly.

## Docker

A `docker-compose.yml` file is included with these services:

- `app`: PHP application container
- `web`: nginx frontend on `https://localhost:8443`
- `db`: PostgreSQL
- `worker`: background queue worker running `bin/cake QueueWorker`

Containers now read runtime configuration from environment variables rather than files under `config/DockerSecrets/`.
Provide those variables through your shell, a local uncommitted `.env`, your deployment platform's secret store, or GitHub Actions environment/repository secrets.
Use [.env.example](/Users/jacob/Development/EventBookingBackend/.env.example) as the canonical starting point for both local and Docker environments.
Inside Docker Compose, the app connection strings are derived from `POSTGRES_DB`, `POSTGRES_USER`, and `POSTGRES_PASSWORD`. The standalone `DATABASE_URL` values in `.env` are for non-Docker local runs on the host.

## Project Structure

- [src/Controller](/Users/jacob/Development/EventBookingBackend/src/Controller)
- [src/Model](/Users/jacob/Development/EventBookingBackend/src/Model)
- [src/Mailer](/Users/jacob/Development/EventBookingBackend/src/Mailer)
- [src/Queue](/Users/jacob/Development/EventBookingBackend/src/Queue)
- [config/Migrations](/Users/jacob/Development/EventBookingBackend/config/Migrations)
- [tests/TestCase](/Users/jacob/Development/EventBookingBackend/tests/TestCase)

## CI Notes

GitHub Actions runs:

- PHPUnit against PostgreSQL
- PHP_CodeSniffer
- PHPStan
- Docker image builds for pull requests and pushes to `ghcr.io/<owner>/<repo>` from `main`, version tags like `v1.2.3`, or manual workflow dispatch

The workflow is configured for Node 24-compatible GitHub Actions runtimes and uses explicit Cognito/AWS test values so CI does not depend on local machine configuration.

## Container Publishing

The repository includes a `Docker Publish` workflow that:

- builds the Docker image on every pull request
- pushes container images to GitHub Container Registry on `main`
- pushes versioned images when you create tags matching `v*`
- supports manual publishing from the Actions tab
- uses a GitHub Environment for protected releases on `main` and manual dispatches

Published image names default to:

```text
ghcr.io/<github-owner>/<repository>
```

If your package visibility is private, consumers will need appropriate GitHub package permissions to pull it.

## Kubernetes

Kubernetes manifests for a local k3s deployment live under [`k8s/base/`](/Users/jacob/Development/EventBookingBackend/k8s/base), with [`k8s/kustomization.yaml`](/Users/jacob/Development/EventBookingBackend/k8s/kustomization.yaml) as the steady-state entrypoint. Operational resources remain separate in [`k8s/operations/deploy-job.yaml`](/Users/jacob/Development/EventBookingBackend/k8s/operations/deploy-job.yaml) and [`k8s/secrets/onepassword-item.yaml`](/Users/jacob/Development/EventBookingBackend/k8s/secrets/onepassword-item.yaml).

For direct HTTPS on the Event Booking `LoadBalancer` origin, see [`k8s/README.md`](/Users/jacob/Development/EventBookingBackend/k8s/README.md). On a fresh cluster, run [`k8s/bootstrap-origin-tls.sh`](/Users/jacob/Development/EventBookingBackend/k8s/bootstrap-origin-tls.sh) once to seed the temporary TLS secret before applying the steady-state manifest.

The runtime image is the same one published by GitHub Actions:

```text
ghcr.io/lbdistrictscouts/eventbookingbackend:<tag>
```

Use `:latest` for the current `main` build, a Git tag such as `:v1.2.3` for releases, or a SHA tag published by the workflow when you want an immutable rollout target.

The main manifest deploys:

- a combined `event-booking` pod containing PHP-FPM and nginx
- a `worker` deployment for `QueueWorker`
- a `db` deployment backed by a persistent volume claim
- config objects matching the existing runtime environment variable contract

The nginx and PHP containers run in the same pod because the published GHCR image contains the application code, while nginx still needs access to the same files for `webroot` and PHP script resolution.

Do not commit live Kubernetes secrets to the repository. When using the 1Password Kubernetes Operator, the repo only needs a `OnePasswordItem` custom resource that points at the 1Password item. The operator then creates and maintains the Kubernetes `Secret` for you.

### 1Password Operator

Store the Kubernetes secret values in a single 1Password item, with field labels that exactly match the environment variable names used by the application:

- `SECURITY_SALT`
- `POSTGRES_DB`
- `POSTGRES_USER`
- `POSTGRES_PASSWORD`
- `DATABASE_URL`
- `DATABASE_TEST_URL`
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_SESSION_TOKEN`
- `AWS_SQS_QUEUE_URL`
- `AWS_SQS_QUEUE_NAME`
- `COGNITO_DOMAIN`
- `COGNITO_CLIENT_ID`
- `COGNITO_CLIENT_SECRET`
- `COGNITO_USER_POOL_ID`
- `SMTP_USERNAME`
- `SMTP_PASSWORD`
- `SMTP_CLIENT`
- `EMAIL_TRANSPORT_SMTP_URL`

The operator flow in this repo assumes:

- the `OnePasswordItem` resource is named `event-booking-secrets`
- the operator creates a Kubernetes `Secret` with the same name
- the application workloads consume that generated `Secret` via `secretRef`

Update [`k8s/secrets/onepassword-item.yaml`](/Users/jacob/Development/EventBookingBackend/k8s/secrets/onepassword-item.yaml) so `spec.itemPath` points to your real 1Password item:

```text
vaults/<vault-id-or-title>/items/<item-id-or-title>
```

The installed CRD in your cluster exposes exactly one required spec field, `itemPath`, which matches the current 1Password operator documentation and CRD schema.

Basic local k3s flow:

1. Update [`k8s/secrets/onepassword-item.yaml`](/Users/jacob/Development/EventBookingBackend/k8s/secrets/onepassword-item.yaml) with the correct vault and item path.
2. Create the GHCR image pull secret in the `event-booking` namespace. The manifests reference a secret named `ghcr-pull-secret` through the `event-booking-runtime` service account.

```bash
kubectl create namespace event-booking
kubectl create secret docker-registry ghcr-pull-secret \
  --namespace event-booking \
  --docker-server=ghcr.io \
  --docker-username="<github-username>" \
  --docker-password="<github-classic-pat-or-fine-grained-token>" \
  --docker-email="<email>"
```

The GitHub token must be able to read the private package. For GitHub Container Registry that typically means a token with package read access, such as a classic PAT with `read:packages`.

3. Apply the namespace, config, operator item, and workloads:

```bash
kubectl apply -k k8s
kubectl apply -f k8s/secrets/onepassword-item.yaml
```

4. Wait for the operator to create the `event-booking-secrets` Kubernetes `Secret`, then run the one-shot deploy job to create schemas, run post-install hooks, and execute migrations:

```bash
kubectl get secret -n event-booking event-booking-secrets
kubectl apply -f k8s/operations/deploy-job.yaml
kubectl logs -n event-booking job/event-booking-deploy -f
```

If your operator is configured with auto-restart support, changes to the backing 1Password item will update the Kubernetes `Secret`, and the `OnePasswordItem` annotation enables automatic redeploy behavior for this secret.

5. Get the service address from k3s:

```bash
kubectl get svc -n event-booking event-booking-web
```

The web service is defined as `LoadBalancer`, which works cleanly with the default k3s service load balancer for local access.

To roll the cluster forward after a new GHCR image is published, use [`bin/redeploy-k3s.sh`](/Users/jacob/Development/EventBookingBackend/bin/redeploy-k3s.sh):

```bash
bin/redeploy-k3s.sh
```

That restarts the `event-booking` and `worker` deployments without recreating secrets, persistent storage, or the namespace. Restarting `event-booking` also reruns the `copy-app-code` init container so NGINX picks up the latest static files from the new image. If you also want to rerun the deploy job for migrations before restarting workloads:

```bash
bin/redeploy-k3s.sh --with-migrations
```

## GitHub Environment Secrets

For deployments, create a GitHub Environment such as `production` and store runtime secrets there instead of in the repository.

Recommended environment secrets:

- `SECURITY_SALT`
- `DATABASE_URL`
- `DATABASE_TEST_URL`
- `POSTGRES_DB`
- `POSTGRES_USER`
- `POSTGRES_PASSWORD`
- `AWS_REGION`
- `AWS_ACCESS_KEY_ID`
- `AWS_SECRET_ACCESS_KEY`
- `AWS_SESSION_TOKEN`
- `AWS_SQS_QUEUE_URL`
- `AWS_SQS_QUEUE_NAME`
- `COGNITO_DOMAIN`
- `COGNITO_CLIENT_ID`
- `COGNITO_CLIENT_SECRET`
- `COGNITO_USER_POOL_ID`
- `SMTP_HOST`
- `SMTP_PORT`
- `SMTP_TIMEOUT`
- `SMTP_TLS`
- `SMTP_LOG`
- `SMTP_USERNAME`
- `SMTP_PASSWORD`
- `SMTP_CLIENT`
- `EMAIL_TRANSPORT_SMTP_URL`

The publish workflow does not consume these values because image builds should remain secret-free. Use them only in the deployment step that starts the container.

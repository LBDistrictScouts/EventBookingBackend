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

The theme settings are source-controlled in [`config/app.php`](/Users/jacob/Development/EventBookingBackend/config/app.php) and the build script defaults. The current source of truth is `@lbd-scouts/district-styles`, with local development compiling from `../district-styles` into `webroot/theme/theme.css`.
Docker image builds do not compile the theme themselves, because the sibling theme repo is outside the Docker build context. Run the assets build first so `webroot/theme/theme.css` exists before building the image.

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

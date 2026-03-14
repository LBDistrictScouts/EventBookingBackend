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
- `AWS_URL`
- `AWS_SQS_QUEUE_NAME`
- `COGNITO_DOMAIN`
- `COGNITO_CLIENT_ID`
- `COGNITO_CLIENT_SECRET`
- `COGNITO_USER_POOL_ID`

4. Create the Postgres schemas used by the app and tests:

```sql
CREATE SCHEMA IF NOT EXISTS data;
CREATE SCHEMA IF NOT EXISTS test;
```

The app expects PostgreSQL schema-separated connections. In CI the main connection uses `schema=data` and the test connection uses `schema=test`.

5. Run migrations as needed:

```bash
bin/cake migrations migrate
```

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

Secrets for Docker are expected under `config/DockerSecrets/`.

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

The workflow is configured for Node 24-compatible GitHub Actions runtimes and uses explicit Cognito/AWS test values so CI does not depend on local machine configuration.

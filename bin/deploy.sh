#!/usr/bin/env sh

set -eu

: "${POSTGRES_DB:?POSTGRES_DB is required}"
: "${POSTGRES_USER:?POSTGRES_USER is required}"
: "${POSTGRES_PASSWORD:?POSTGRES_PASSWORD is required}"

export PGPASSWORD="$POSTGRES_PASSWORD"

echo "Waiting for database..."
until pg_isready -h db -U "$POSTGRES_USER" -d "$POSTGRES_DB" >/dev/null 2>&1; do
    sleep 1
done

echo "Ensuring required schemas exist..."
psql -h db -U "$POSTGRES_USER" -d "$POSTGRES_DB" -c 'CREATE SCHEMA IF NOT EXISTS data;'

if [ "${CREATE_TEST_SCHEMA:-false}" = "true" ]; then
    psql -h db -U "$POSTGRES_USER" -d "$POSTGRES_DB" -c 'CREATE SCHEMA IF NOT EXISTS test;'
fi

echo "Running composer post-install script..."
composer run-script post-install-cmd --no-interaction

echo "Running database migrations..."
composer run-script migrate

echo "Running database seeding"

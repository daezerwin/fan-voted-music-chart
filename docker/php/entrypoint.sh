#!/bin/sh
set -e

# Only the `app` service should run migrations. `queue` and `scheduler` boot
# the exact same image but must not race `app` (or each other) to migrate —
# RUN_MIGRATIONS is set to "true" only on the `app` service in
# docker-compose.prod.yml. Migrations are idempotent, so running this once
# per deploy on every `app` container start is safe.
if [ "$RUN_MIGRATIONS" = "true" ]; then
    php artisan migrate --force
fi

exec "$@"

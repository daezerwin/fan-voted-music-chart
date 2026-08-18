#!/bin/sh
set -e

# Only the `app` service should run migrations. `queue` and `scheduler` boot
# the exact same image but must not race `app` (or each other) to migrate —
# RUN_MIGRATIONS is set to "true" only on the `app` service in
# docker-compose.prod.yml. Migrations are idempotent, so running this once
# per deploy on every `app` container start is safe.
if [ "$RUN_MIGRATIONS" = "true" ]; then
    # A failed migration must not take the whole container down: that would
    # make `app` (the only image with php/artisan) crash-loop and become
    # unreachable via `docker exec`, right when you most need to get in and
    # run artisan by hand to diagnose it.
    php artisan migrate --force || echo "WARNING: migrations failed — continuing startup so the container stays reachable" >&2
fi

exec "$@"

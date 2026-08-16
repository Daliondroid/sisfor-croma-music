#!/bin/bash

echo "[entrypoint] Ensuring storage subdirectories exist..."
mkdir -p /var/www/storage/app/public \
         /var/www/storage/framework/cache/data \
         /var/www/storage/framework/sessions \
         /var/www/storage/framework/testing \
         /var/www/storage/framework/views \
         /var/www/storage/logs \
         /var/www/bootstrap/cache

chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true
chmod -R 775 /var/www/storage /var/www/bootstrap/cache || true

# Remove any stale bootstrap cache files from host or previous mounts
rm -f /var/www/bootstrap/cache/*.php

# Ensure storage symlink exists
if [ ! -L /var/www/public/storage ]; then
    echo "[entrypoint] Creating storage symlink..."
    php artisan storage:link || true
fi

# Wait for database readiness synchronously
echo "[entrypoint] Waiting for database connection..."
until php -r "try { new PDO('mysql:host=' . (getenv('DB_HOST') ?: 'db') . ';port=' . (getenv('DB_PORT') ?: '3306'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); exit(0); } catch (Throwable \$e) { exit(1); }" 2>/dev/null; do
    sleep 2
done
echo "[entrypoint] Database connection established."

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    echo "[entrypoint] Running database migrations..."
    php artisan migrate --force || true
fi

if [ "${APP_ENV:-local}" = "production" ]; then
    echo "[entrypoint] Optimizing caches for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
else
    php artisan config:clear || true
    php artisan route:clear || true
    php artisan view:clear || true
fi

echo "[entrypoint] Boot complete. Executing process: $@"
exec "$@"

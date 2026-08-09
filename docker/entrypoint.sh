#!/bin/bash
set -e

echo "[entrypoint] Waiting for MySQL to be ready..."

until mysqladmin ping -h "db" -u "${DB_USERNAME}" -p"${DB_PASSWORD}" --silent 2>/dev/null; do
    echo "[entrypoint] MySQL not ready, retrying in 3s..."
    sleep 3
done

echo "[entrypoint] MySQL is ready."

# Fix runtime permissions in case the volume was mounted fresh
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Ensure storage symlink exists
if [ ! -L /var/www/public/storage ]; then
    echo "[entrypoint] Creating storage symlink..."
    php artisan storage:link
fi

# Run pending database migrations
echo "[entrypoint] Running database migrations..."
php artisan migrate --force

# Cache Laravel configuration for performance
echo "[entrypoint] Caching config, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "[entrypoint] Bootstrap complete. Starting PHP-FPM..."
exec "$@"

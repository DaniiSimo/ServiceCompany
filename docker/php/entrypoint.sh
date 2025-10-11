#!/bin/sh
set -e

APP_DIR=/var/www/html

chown -R www:www "${APP_DIR}"

composer install

if [ ! -f "/var/www/html/.env" ]; then
  cp /var/www/html/.env.example /var/www/html/.env
  php artisan key:generate --force
fi

php artisan migrate --force

echo "Clearing configurations..."
php artisan config:clear
php artisan route:clear

exec "$@"

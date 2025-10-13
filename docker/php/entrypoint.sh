#!/bin/sh
set -e

APP_DIR=/var/www/html

chown -R www:www "${APP_DIR}"

cd "$APP_DIR"

if [ ! -d "vendor" ] || [ -z "$(ls -A vendor 2>/dev/null)" ]; then
  echo "Installing Composer dependencies..."
  if [ "${APP_ENV:-local}" = "production" ]; then
    composer install --no-interaction --no-dev --prefer-dist --optimize-autoloader
  else
    composer install --no-interaction --prefer-dist --optimize-autoloader
  fi
fi

if [ ! -f "/var/www/html/.env" ]; then
  echo "Generate key app..."
  cp /var/www/html/.env.example /var/www/html/.env
  php artisan key:generate --force
fi

echo "Checking if migrations table exists..."
HAS_MIGRATIONS_TABLE="$(
  php -r "
    require 'vendor/autoload.php';
    \$app = require 'bootstrap/app.php';
    \$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();
    echo Illuminate\\Support\\Facades\\Schema::hasTable('migrations') ? '1' : '0';
  "
)"

if [ "$HAS_MIGRATIONS_TABLE" = "0" ]; then
  echo "Database looks empty -> running migrations..."
  php artisan migrate --force
else
  echo "Migrations table exists -> skipping initial migrate."
fi

echo "Clearing configurations..."
php artisan optimize:clear

touch /tmp/app_ready

exec "$@"

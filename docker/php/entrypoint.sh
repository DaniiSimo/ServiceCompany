#!/bin/sh
set -e

cd "/var/www/html"

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
  echo "Running data initialization..."
  php artisan db:seed --class=InitDataSeeder
else
  echo "Migrations table exists -> skipping initial migrate."
fi

echo "Clearing configurations..."
php artisan optimize:clear

touch /tmp/app_ready

exec "$@"

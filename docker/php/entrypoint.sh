#!/bin/sh
set -e

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    php artisan key:generate --force
fi

php artisan migrate --force

php artisan config:clear
php artisan route:clear
php artisan view:clear

exec "$@"

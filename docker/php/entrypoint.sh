#!/bin/sh
set -e

APP_KEY_VALUE=$(php artisan key:generate --show)
export APP_KEY="$APP_KEY_VALUE"

php artisan migrate --force

php artisan config:clear
php artisan route:clear
php artisan view:clear

exec "$@"

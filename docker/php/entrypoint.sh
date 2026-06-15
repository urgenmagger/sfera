#!/bin/sh
set -e

php artisan key:generate --force

if [ ! -f /var/www/html/storage/oauth-private.key ]; then
    php artisan passport:keys --force
fi

php artisan migrate --force

php artisan config:clear
php artisan route:clear
php artisan view:clear

exec "$@"

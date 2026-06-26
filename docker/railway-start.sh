#!/bin/bash
set -e

php artisan migrate --force
php artisan storage:link 2>/dev/null || true

if [ "$(php artisan tinker --execute='echo \App\Models\User::count();')" = "0" ]; then
    php artisan db:seed --force
fi

php artisan optimize:clear
php artisan db:seed --class=Database\\Seeders\\StudentPortalDemoSeeder --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8080}"

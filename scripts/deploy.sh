#!/usr/bin/env bash

set -euo pipefail

echo "[deploy] Starting production deployment..."

composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build

php artisan migrate --force
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart

echo "[deploy] Deployment completed successfully."

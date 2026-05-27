#!/usr/bin/env bash

set -euo pipefail

echo "[check] Running production readiness checks..."

php artisan institution:validate-environment --strict-production
php artisan route:list > /dev/null
php artisan about

if [ ! -d "storage/app/backups" ]; then
  echo "[check][warning] storage/app/backups does not exist yet."
fi

if [ ! -L "public/storage" ] && [ ! -d "public/storage" ]; then
  echo "[check][warning] public/storage link missing. Run php artisan storage:link."
fi

echo "[check] Done."

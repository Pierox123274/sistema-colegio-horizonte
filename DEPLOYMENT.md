# Despliegue — Sistema Colegio Horizonte

Guía operativa de despliegue para producción real (Fase 27).

## Requisitos servidor

- PHP 8.2+ con extensiones: `pdo_mysql`, `mbstring`, `xml`, `zip`, `intl`, `gd`, `opcache`
- MySQL 8+ (o MariaDB equivalente)
- Redis 7+ para colas/sesiones/caché
- Composer 2+
- Node 20+ (solo etapa de build)
- Permisos de escritura en `storage/` y `bootstrap/cache/`

## Variables críticas de producción

Configurar en `.env`:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://tu-dominio.com`
- `SESSION_SECURE_COOKIE=true`
- SMTP real (`MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`)
- `QUEUE_CONNECTION=redis`
- `CACHE_STORE=redis`
- `SESSION_DRIVER=redis`

## Deploy paso a paso

Puede ejecutar el script:

```bash
bash scripts/deploy.sh
```

O manualmente:

```bash
composer install --no-dev --optimize-autoloader --no-interaction
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart
```

## Verificación post deploy

```bash
bash scripts/production-check.sh
```

Además:

- Revisar `/intranet/system/health` (checks `ok/warning/critical`)
- Revisar `/intranet/system/jobs` (fallidos)
- Revisar `/intranet/system/backups` (últimos respaldos)

## Colas y scheduler

- Worker recomendado: `php artisan queue:work --tries=3`
- Cron recomendado: ver `deploy/cron/scheduler.txt`
- Supervisor base: `deploy/supervisor/laravel-worker.conf`

## Rollback básico

1. Restaurar versión previa del código
2. Restaurar backup DB/archivos si hubo cambios críticos
3. Ejecutar:
   - `php artisan optimize:clear`
   - `php artisan config:cache`
   - `php artisan queue:restart`

## Docker producción

- Referencia: `docker-compose.prod.yml`
- Nginx producción: `docker/nginx.prod.conf`
- Mantiene stack separado de desarrollo (`docker-compose.yml`).

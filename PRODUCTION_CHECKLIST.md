# Production Checklist — Horizonte

Checklist operativo para salida a internet.

## 1) Requisitos del servidor

- [ ] PHP 8.2+ con extensiones: `pdo_mysql`, `mbstring`, `xml`, `zip`, `intl`, `gd`, `opcache`
- [ ] MySQL 8+
- [ ] Node 20+ (build)
- [ ] Composer 2+
- [ ] Redis 7+

## 2) Configuración `.env`

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://tu-dominio.com`
- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `QUEUE_CONNECTION=redis`
- [ ] `CACHE_STORE=redis`
- [ ] `SESSION_DRIVER=redis`
- [ ] SMTP configurado
- [ ] Claves API (incluida IA) solo por variables de entorno

## 3) Permisos y storage

- [ ] `storage/` escribible
- [ ] `bootstrap/cache/` escribible
- [ ] `php artisan storage:link` ejecutado

## 4) Deploy

- [ ] Ejecutado `scripts/deploy.sh` o pasos manuales equivalentes
- [ ] Migraciones aplicadas con `--force`
- [ ] Assets compilados en `public/build`
- [ ] Cachés de Laravel (`config/route/view`) activas
- [ ] `php artisan queue:restart` ejecutado

## 5) Scheduler y workers

- [ ] Worker activo: `php artisan queue:work --tries=3`
- [ ] Cron activo cada minuto (`deploy/cron/scheduler.txt`) o `schedule:work`
- [ ] Supervisor aplicado (`deploy/supervisor/laravel-worker.conf`)

## 6) Backups

- [ ] Directorio `storage/app/backups` existe
- [ ] Respaldo reciente disponible
- [ ] Política de retención validada
- [ ] Proceso restore básico probado

## 7) Seguridad

- [ ] Headers HTTP aplicados
- [ ] `APP_DEBUG` apagado
- [ ] HTTPS activo (HSTS en producción)
- [ ] Sin exposición de `.env`
- [ ] Logs no exponen secretos

## 8) Verificación funcional

- [ ] `php artisan test`
- [ ] `npm run build`
- [ ] `vendor/bin/pint --dirty`
- [ ] `/intranet/system/health` en verde o con warnings controlados
- [ ] `/intranet/system/jobs` sin fallidos críticos
- [ ] `/intranet/system/backups` con backups recientes

## 9) Rollback básico

- [ ] Procedimiento documentado para volver al release anterior
- [ ] Backup pre-deploy disponible
- [ ] Comando de limpieza (`php artisan optimize:clear`) considerado en incidentes

## 10) Troubleshooting rápido

- Error 500 tras deploy: limpiar y regenerar cachés
- Jobs no procesan: validar worker + `QUEUE_CONNECTION`
- Scheduler no corre: revisar cron/supervisor y heartbeat en health
- Archivos no visibles: verificar `storage:link` y permisos

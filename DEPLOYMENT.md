# Despliegue — Sistema Colegio Horizonte

Guía breve para poner la aplicación Laravel + Inertia en producción institucional.

## Requisitos

- PHP 8.2+ con extensiones: `pdo_mysql` o `pdo_sqlite`, `mbstring`, `xml`, `zip`, `intl`, `gd`, `opcache`
- Node 20+ (solo en la máquina de build de assets)
- MySQL 8+ o MariaDB (recomendado para producción)
- Redis (recomendado para colas y sesiones a escala)

## Build de frontend

```bash
npm ci
npm run build
```

Los assets se sirven desde `public/build` vía Vite.

## Optimización Laravel

Tras desplegar código y `.env` en el servidor:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --no-dev --optimize-autoloader
```

Para volver a desarrollo local:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Colas y programador

- **Colas:** `php artisan queue:work redis` (o `database` según `QUEUE_CONNECTION`).
- **Programador:** en cron del servidor: `* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1`  
  o en Docker: servicio `schedule:work` (ver `docker-compose.yml`).

## Salud y operaciones

Los administradores pueden revisar `/intranet/system/health`, `/intranet/system/jobs` y `/intranet/system/backups` (solo rol **Administrador**).

## HTTPS y cookies

Con HTTPS activo, configure `SESSION_SECURE_COOKIE=true` y revise `SESSION_SAME_SITE` según `docs/SECURITY_POLICY.md`.

## Docker

Ver `DEVOPS.md` y `docker-compose.yml` para un stack de referencia (app PHP-FPM, nginx, MySQL, Redis, worker de cola y scheduler).

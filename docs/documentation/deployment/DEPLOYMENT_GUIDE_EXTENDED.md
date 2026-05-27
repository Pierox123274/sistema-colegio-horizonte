# Guía de despliegue extendida

## Requisitos

PHP 8.2+, MySQL 8+, Redis recomendado, Node 20+ (build), Composer 2+.

## Instalación

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
# Configurar .env (DB, mail, queue, AI, integraciones)
php artisan migrate --force
npm ci && npm run build
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Servicios

- PHP-FPM + Nginx  
- `php artisan queue:work` (supervisor)  
- Cron: `* * * * * php artisan schedule:run`

## Docker

Ver `docker-compose.yml` y `Dockerfile`.

Complemento: `DEPLOYMENT.md` en raíz.

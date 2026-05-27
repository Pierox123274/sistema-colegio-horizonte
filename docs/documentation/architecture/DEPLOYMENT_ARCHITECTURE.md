# Arquitectura de despliegue

## Propósito

Describir cómo se ejecuta el sistema en **desarrollo, staging y producción**.

## Componentes

- **App PHP-FPM** — Laravel.  
- **Nginx** — reverse proxy.  
- **MySQL** — datos.  
- **Redis** — colas/caché/sesión (recomendado producción).  
- **Worker** — `queue:work`.  
- **Scheduler** — `schedule:work` o cron → `routes/console.php`.  
- **Docker** — `Dockerfile`, `docker-compose.yml` (opcional).

## Flujo CI/CD

GitHub Actions: Pint → PHPUnit → `npm run build` → artefactos en `public/build`.

## Scripts

- `scripts/deploy.sh`  
- `scripts/production-check.sh`

## Relación

Ver `deployment/DEPLOYMENT_GUIDE_EXTENDED.md` y `DEPLOYMENT.md` en raíz.

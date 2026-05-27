# Checklist de producción extendido

## Pre-deploy

- [ ] Tests CI en verde  
- [ ] Variables `.env` revisadas  
- [ ] Migraciones probadas en staging  

## Deploy

- [ ] Modo mantenimiento si aplica  
- [ ] `composer install --no-dev`  
- [ ] `npm run build`  
- [ ] `php artisan migrate --force`  
- [ ] Reiniciar workers  

## Post-deploy

- [ ] Login smoke test por rol  
- [ ] Health check OK  
- [ ] Correo de prueba  
- [ ] Cola procesando jobs  
- [ ] Backup manual verificado

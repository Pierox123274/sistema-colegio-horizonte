# Checklist de seguridad — Producción

- [ ] `APP_ENV=production`, `APP_DEBUG=false`  
- [ ] `APP_KEY` generada y secreta  
- [ ] `APP_URL` con HTTPS  
- [ ] `SESSION_SECURE_COOKIE=true`  
- [ ] SMTP con credenciales de aplicación  
- [ ] `QUEUE_CONNECTION` ≠ sync  
- [ ] Redis para sesión/caché recomendado  
- [ ] Permisos `storage/` y `bootstrap/cache`  
- [ ] `php artisan storage:link`  
- [ ] Scheduler y worker activos  
- [ ] Respaldos automáticos verificados  
- [ ] Secretos IA e integraciones solo en `.env`  
- [ ] Webhooks con firma habilitada  
- [ ] Revisar `/intranet/system/health` en verde o warning justificado

Script: `bash scripts/production-check.sh`

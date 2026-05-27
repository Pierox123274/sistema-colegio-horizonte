# Solución de problemas

| Síntoma | Posible causa | Acción |
|---------|---------------|--------|
| 500 tras deploy | `.env`, permisos storage | logs `storage/logs/laravel.log` |
| Cola no procesa | worker caído | `queue:work`, revisar failed_jobs |
| Correo no sale | SMTP log | panel integraciones, `.env` MAIL_* |
| IA no responde | API off o sin key | `AI_TUTOR_ENABLED`, OpenAI key |
| 403 en rutas | rol | verificar Spatie roles |
| Assets rotos | build viejo | `npm run build`, limpiar caché |
| Error audit context | columna incorrecta | usar `metadata` (corregido Fase 31+) |

## Comandos útiles

```bash
php artisan optimize:clear
php artisan config:clear
php artisan queue:retry all
```

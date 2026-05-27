# Colas y scheduler

## Cola

`.env`: `QUEUE_CONNECTION=database` o `redis`.

```bash
php artisan queue:work --tries=3
```

Jobs: correos, respaldos, alertas, recordatorios meetings, insights IA.

## Scheduler

`routes/console.php` — purgas, backups, métricas, alertas.

```bash
php artisan schedule:work
# producción: cron cada minuto schedule:run
```

## Monitoreo

`/intranet/system/jobs` — failed_jobs.  
Health: heartbeat scheduler en caché.

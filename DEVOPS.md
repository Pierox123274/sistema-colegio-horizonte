# DevOps institucional — Horizonte

Este documento describe la **Fase 20**: automatización, respaldos, colas, programación, CI/CD base y preparación para producción / futura IA.

## Programación (`routes/console.php`)

Laravel 12 centraliza el scheduler en `routes/console.php` (equivalente operativo al histórico `app/Console/Kernel.php`).

Tareas registradas (prefijo `institution:`):

| Tarea | Frecuencia |
|-------|------------|
| `institution:purge-old-audit-logs` | Diaria 02:10 |
| `institution:purge-expired-user-sessions` | Cada 6 horas |
| `institution:prune-old-backups` | Diaria 04:05 |
| Job `CreateInstitutionalBackupJob` | Diaria 03:15 |
| Job `SendInstitutionDailySummaryJob` | Diaria 07:30 (requiere `DEVOPS_SEND_DAILY_SUMMARY=true` y correo) |
| Job `AcademicAlertScanJob` | Días laborables 06:40 |
| Job `FinancialAlertScanJob` | Días laborables 06:55 |
| Job `SecurityHealthScanJob` | Diaria 05:40 |
| Job `InstitutionMetricsSnapshotJob` | Cada hora (caché para analítica / IA futura) |

## Comandos Artisan

- `institution:purge-old-audit-logs` — retención configurable (`DEVOPS_AUDIT_LOG_RETENTION_DAYS`).
- `institution:purge-expired-user-sessions` — limpia filas antiguas de `user_sessions` inactivas.
- `institution:prune-old-backups` — mantiene los N ZIP más recientes.
- `institution:create-backup` — respaldo inmediato (síncrono).
- `institution:validate-environment` — validación básica de `APP_KEY` y, con `--strict-production`, reglas extra en producción.

## Colas y jobs

- Jobs en `app/Jobs/` implementan `ShouldQueue` donde aplica.
- `ProcessAnalyticsExportJob` es **placeholder** para exportaciones pesadas asíncronas / integración IA.
- Correos: `app/Mail/*` y vistas Markdown en `resources/views/emails/*`.
- Notificación de ejemplo: `App\Notifications\InstitutionOperationalAlertNotification`.

## Respaldos

- Directorio: `storage/app/backups/` (ignorado en git salvo `.gitignore`).
- **SQLite:** empaqueta archivo o nota si es `:memory:`.
- **MySQL:** intenta `mysqldump` (`DEVOPS_MYSQLDUMP_PATH`); si no está disponible, el ZIP puede contener solo `public/`.

## Panel intranet (solo Administrador)

- `/intranet/system/health` — estado DB, cola, disco, caché, snapshot de métricas.
- `/intranet/system/jobs` — jobs fallidos (`failed_jobs`).
- `POST /intranet/system/backups` — encola `CreateInstitutionalBackupJob`.

## Docker

- `Dockerfile` — imagen PHP 8.2-FPM con extensiones habituales.
- `docker-compose.yml` — servicios: `app`, `nginx`, `mysql`, `redis`, `queue`, `scheduler`.
- `docker/nginx.conf` — FastCGI hacia `app:9000`.

Ajuste variables de entorno del compose a su institución.

## CI/CD

- Workflow: `.github/workflows/ci.yml` — `composer install`, migraciones SQLite, **Pint**, **PHPUnit**, `npm ci`, `npm run build`.
- PHPStan no está incluido por defecto (evita baseline amplio); puede añadirse más adelante.

## Fase 27 — Hardening y producción real

- **Scripts de despliegue**:
  - `scripts/deploy.sh`
  - `scripts/production-check.sh`
- **Seguridad HTTP**: middleware global `SecurityHeadersMiddleware` con:
  - `X-Frame-Options`
  - `X-Content-Type-Options`
  - `Referrer-Policy`
  - `Permissions-Policy`
  - `Strict-Transport-Security` (solo producción + HTTPS)
  - CSP básica en producción.
- **Salud de producción** (`/intranet/system/health`):
  - checks de DB, cola, caché, storage, storage link, scheduler heartbeat, disco, HTTPS, SMTP, backups.
  - estados `ok`, `warning`, `critical`.
  - lectura de errores recientes del log principal.
- **Logging por canal**:
  - `health`, `security`, `audit`, `failed_jobs` (además de `daily` / `stack`).
- **Docker producción**:
  - `docker-compose.prod.yml`
  - `docker/nginx.prod.conf`
  - servicios separados para app, nginx, mysql, redis, queue, scheduler.
- **Operación scheduler**:
  - heartbeat cada minuto (`system.scheduler.last_run_at`) para diagnóstico de ejecución real.

## Configuración `.env`

Ver comentarios al final de `.env.example` (`DEVOPS_*` y seguridad).

## Autorización

Secretaría, docentes y estudiantes **no** acceden al panel técnico; solo **Administrador** (`SystemOperationsPolicy` + `SystemOperationsDashboard`).

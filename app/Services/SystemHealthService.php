<?php

namespace App\Services;

use App\Integrations\Services\IntegrationHealthService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

final class SystemHealthService
{
    /**
     * @return array<string, mixed>
     */
    public function healthSnapshot(): array
    {
        $database = $this->probeDatabase();
        $queue = $this->probeQueue();
        $disk = $this->probeDisk();
        $cacheWritable = $this->probeCacheWritable();
        $backupStatus = $this->probeBackups();
        $schedulerHeartbeat = Cache::get('system.scheduler.last_run_at');
        $schedulerHealthy = $schedulerHeartbeat !== null
            && now()->diffInMinutes((string) $schedulerHeartbeat) <= 5;

        $storagePath = storage_path();
        $bootstrapCachePath = base_path('bootstrap/cache');
        $publicStorageLinked = is_link(public_path('storage')) || is_dir(public_path('storage'));
        $mailConfigured = $this->isMailConfigured();
        $queueConfigured = $queue['driver'] !== 'sync';

        $checks = $this->buildHealthChecks(
            $database,
            $queue,
            $disk,
            $cacheWritable,
            $storagePath,
            $bootstrapCachePath,
            $publicStorageLinked,
            $schedulerHeartbeat,
            $schedulerHealthy,
            $mailConfigured,
            $queueConfigured,
            $backupStatus,
        );

        $overallStatus = $this->resolveOverallStatus($checks);

        Log::channel(config('logging.health_channel', 'daily'))->info('system_health_snapshot', [
            'overall_status' => $overallStatus,
            'generated_at' => now()->toIso8601String(),
        ]);

        $integrations = app(IntegrationHealthService::class)->snapshot();

        return [
            'status' => $overallStatus,
            'integrations' => $integrations,
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
                'debug' => (bool) config('app.debug'),
                'url' => config('app.url'),
            ],
            'database' => [
                'ok' => $database['ok'],
                'latency_ms' => $database['latency_ms'],
                'error' => $database['error'],
                'connection' => config('database.default'),
            ],
            'queue' => $queue,
            'storage' => [
                'disk_free_bytes' => $disk['free_bytes'],
                'disk_total_bytes' => $disk['total_bytes'],
                'disk_usage_percent' => $disk['usage_percent'],
                'is_storage_writable' => is_writable($storagePath),
                'is_bootstrap_cache_writable' => is_writable($bootstrapCachePath),
                'public_storage_linked' => $publicStorageLinked,
            ],
            'cache' => [
                'driver' => config('cache.default'),
                'writable' => $cacheWritable,
            ],
            'scheduler' => [
                'timezone' => config('app.timezone'),
                'note' => 'Ejecutar `php artisan schedule:work` o cron en producción.',
                'last_heartbeat' => $schedulerHeartbeat,
            ],
            'mail' => [
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'configured' => $mailConfigured,
            ],
            'backups' => $backupStatus,
            'checks' => $checks,
            'generated_at' => now()->toIso8601String(),
        ];
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, object>
     */
    public function failedJobsPaginator(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if (! Schema::hasTable('failed_jobs')) {
            return new LengthAwarePaginator([], 0, $perPage, 1);
        }

        return DB::table('failed_jobs')
            ->orderByDesc('failed_at')
            ->paginate($perPage);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function cachedMetricsSnapshot(): ?array
    {
        $key = (string) config('devops.metrics_cache_key', 'institution.metrics.snapshot');

        return Cache::get($key);
    }

    /**
     * @return array{ok: bool, latency_ms: ?float, error: ?string}
     */
    private function probeDatabase(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();

            return [
                'ok' => true,
                'latency_ms' => round((microtime(true) - $start) * 1000, 2),
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'latency_ms' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * @return array{driver: string, connection: array<string, mixed>, pending_jobs: int, failed_jobs: int}
     */
    private function probeQueue(): array
    {
        $driver = (string) config('queue.default');
        $pendingJobs = Schema::hasTable('jobs') ? (int) DB::table('jobs')->count() : 0;
        $failedJobs = Schema::hasTable('failed_jobs') ? (int) DB::table('failed_jobs')->count() : 0;

        return [
            'driver' => $driver,
            'connection' => (array) config("queue.connections.{$driver}", []),
            'pending_jobs' => $pendingJobs,
            'failed_jobs' => $failedJobs,
        ];
    }

    /**
     * @return array{free_bytes: int|false, total_bytes: int|false, usage_percent: ?float}
     */
    private function probeDisk(): array
    {
        $diskFree = @disk_free_space(base_path());
        $diskTotal = @disk_total_space(base_path());
        $diskUsagePercent = null;

        if ($diskFree !== false && $diskTotal !== false && $diskTotal > 0) {
            $diskUsagePercent = round((($diskTotal - $diskFree) / $diskTotal) * 100, 2);
        }

        return [
            'free_bytes' => $diskFree,
            'total_bytes' => $diskTotal,
            'usage_percent' => $diskUsagePercent,
        ];
    }

    private function probeCacheWritable(): bool
    {
        try {
            Cache::put('__health_check__', '1', 5);
            $writable = Cache::get('__health_check__') === '1';
            Cache::forget('__health_check__');

            return $writable;
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function probeBackups(): array
    {
        $backupDirectory = storage_path('app/backups');
        $backupFiles = File::isDirectory($backupDirectory) ? File::files($backupDirectory) : [];
        usort($backupFiles, fn (\SplFileInfo $a, \SplFileInfo $b) => $b->getMTime() <=> $a->getMTime());
        $latestBackup = $backupFiles[0] ?? null;

        return [
            'folder_exists' => File::isDirectory($backupDirectory),
            'count' => count($backupFiles),
            'latest_name' => $latestBackup?->getFilename(),
            'latest_size_bytes' => $latestBackup?->getSize(),
            'latest_modified_at' => $latestBackup?->getMTime() !== null
                ? date('c', $latestBackup->getMTime())
                : null,
        ];
    }

    /**
     * @param  array{ok: bool, latency_ms: ?float, error: ?string}  $database
     * @param  array{driver: string, connection: array<string, mixed>, pending_jobs: int, failed_jobs: int}  $queue
     * @param  array{free_bytes: int|false, total_bytes: int|false, usage_percent: ?float}  $disk
     * @param  array<string, mixed>  $backupStatus
     * @return array<string, array<string, mixed>>
     */
    private function buildHealthChecks(
        array $database,
        array $queue,
        array $disk,
        bool $cacheWritable,
        string $storagePath,
        string $bootstrapCachePath,
        bool $publicStorageLinked,
        mixed $schedulerHeartbeat,
        bool $schedulerHealthy,
        bool $mailConfigured,
        bool $queueConfigured,
        array $backupStatus,
    ): array {
        $phpVersion = PHP_VERSION;
        $httpsExpected = str_starts_with((string) config('app.url'), 'https://');
        $diskUsagePercent = $disk['usage_percent'];
        $backupDirectory = storage_path('app/backups');

        return [
            'php_version' => [
                'label' => 'PHP version',
                'status' => version_compare($phpVersion, '8.2.0', '>=') ? 'ok' : 'critical',
                'value' => $phpVersion,
                'message' => version_compare($phpVersion, '8.2.0', '>=')
                    ? 'Version compatible para producción.'
                    : 'Actualizar PHP a 8.2 o superior.',
            ],
            'app_env' => [
                'label' => 'Laravel env',
                'status' => app()->environment('production') ? 'ok' : 'warning',
                'value' => config('app.env'),
                'message' => app()->environment('production')
                    ? 'Entorno de producción activo.'
                    : 'Cambiar APP_ENV=production al desplegar.',
            ],
            'app_debug' => [
                'label' => 'APP_DEBUG',
                'status' => config('app.debug') ? 'critical' : 'ok',
                'value' => config('app.debug') ? 'true' : 'false',
                'message' => config('app.debug')
                    ? 'APP_DEBUG debe estar en false en producción.'
                    : 'Configuración segura.',
            ],
            'database' => [
                'label' => 'DB connection',
                'status' => $database['ok'] ? 'ok' : 'critical',
                'value' => config('database.default'),
                'message' => $database['ok']
                    ? 'Conexión operativa.'
                    : (string) ($database['error'] ?: 'No se pudo conectar a la base de datos.'),
            ],
            'queue_connection' => [
                'label' => 'Queue connection',
                'status' => $queueConfigured ? 'ok' : 'warning',
                'value' => $queue['driver'],
                'message' => $queueConfigured
                    ? 'Driver de colas listo para background jobs.'
                    : 'QUEUE_CONNECTION=sync no recomendado en producción.',
            ],
            'cache_driver' => [
                'label' => 'Cache driver',
                'status' => $cacheWritable ? 'ok' : 'critical',
                'value' => config('cache.default'),
                'message' => $cacheWritable ? 'Cache escribible.' : 'No se pudo escribir en caché.',
            ],
            'storage_writable' => [
                'label' => 'Storage writable',
                'status' => is_writable($storagePath) && is_writable($bootstrapCachePath) ? 'ok' : 'critical',
                'value' => $storagePath,
                'message' => is_writable($storagePath) && is_writable($bootstrapCachePath)
                    ? 'storage/ y bootstrap/cache tienen permisos correctos.'
                    : 'Revisar permisos de storage/ y bootstrap/cache.',
            ],
            'storage_link' => [
                'label' => 'Public storage link',
                'status' => $publicStorageLinked ? 'ok' : 'warning',
                'value' => public_path('storage'),
                'message' => $publicStorageLinked
                    ? 'Enlace público disponible.'
                    : 'Ejecutar php artisan storage:link.',
            ],
            'scheduler' => [
                'label' => 'Scheduler status',
                'status' => $schedulerHealthy ? 'ok' : 'warning',
                'value' => $schedulerHeartbeat,
                'message' => $schedulerHealthy
                    ? 'Scheduler reportó actividad reciente.'
                    : 'No hay heartbeat reciente del scheduler.',
            ],
            'disk_usage' => [
                'label' => 'Disk usage',
                'status' => $diskUsagePercent !== null && $diskUsagePercent >= 90
                    ? 'critical'
                    : ($diskUsagePercent !== null && $diskUsagePercent >= 80 ? 'warning' : 'ok'),
                'value' => $diskUsagePercent !== null ? "{$diskUsagePercent}%" : null,
                'message' => $diskUsagePercent === null
                    ? 'No se pudo calcular uso de disco.'
                    : ($diskUsagePercent >= 90
                        ? 'Uso de disco crítico.'
                        : ($diskUsagePercent >= 80 ? 'Uso de disco alto.' : 'Uso de disco saludable.')),
            ],
            'https' => [
                'label' => 'HTTPS',
                'status' => $httpsExpected ? 'ok' : 'warning',
                'value' => config('app.url'),
                'message' => $httpsExpected
                    ? 'APP_URL usa HTTPS.'
                    : 'Configurar APP_URL con https:// en producción.',
            ],
            'mail' => [
                'label' => 'Mail config',
                'status' => $mailConfigured ? 'ok' : 'warning',
                'value' => config('mail.default'),
                'message' => $mailConfigured
                    ? 'Configuración SMTP detectada.'
                    : 'Configurar SMTP real para notificaciones.',
            ],
            'backups' => [
                'label' => 'Backups folder',
                'status' => $backupStatus['folder_exists'] ? 'ok' : 'warning',
                'value' => $backupDirectory,
                'message' => $backupStatus['folder_exists']
                    ? "Respaldos detectados: {$backupStatus['count']}."
                    : 'Directorio de respaldos no encontrado.',
            ],
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $checks
     */
    private function resolveOverallStatus(array $checks): string
    {
        if (collect($checks)->contains(fn (array $check): bool => $check['status'] === 'critical')) {
            return 'critical';
        }

        if (collect($checks)->contains(fn (array $check): bool => $check['status'] === 'warning')) {
            return 'warning';
        }

        return 'ok';
    }

    private function isMailConfigured(): bool
    {
        $defaultMailer = (string) config('mail.default');
        if ($defaultMailer === 'log') {
            return false;
        }

        $smtpHost = (string) config('mail.mailers.smtp.host', '');
        $smtpPort = (string) config('mail.mailers.smtp.port', '');

        return $smtpHost !== '' && $smtpHost !== '127.0.0.1' && $smtpPort !== '';
    }
}

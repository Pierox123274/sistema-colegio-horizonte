<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

final class SystemHealthService
{
    /**
     * @return array<string, mixed>
     */
    public function healthSnapshot(): array
    {
        $dbOk = false;
        $dbLatencyMs = null;
        $dbError = null;

        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $dbOk = true;
            $dbLatencyMs = round((microtime(true) - $start) * 1000, 2);
        } catch (\Throwable $e) {
            $dbError = $e->getMessage();
        }

        $queueDriver = config('queue.default');
        $pendingJobs = 0;
        $failedJobs = 0;

        if (Schema::hasTable('jobs')) {
            $pendingJobs = (int) DB::table('jobs')->count();
        }
        if (Schema::hasTable('failed_jobs')) {
            $failedJobs = (int) DB::table('failed_jobs')->count();
        }

        $diskFree = @disk_free_space(base_path());
        $diskTotal = @disk_total_space(base_path());

        $cacheWritable = false;
        try {
            Cache::put('__health_check__', '1', 5);
            $cacheWritable = Cache::get('__health_check__') === '1';
            Cache::forget('__health_check__');
        } catch (\Throwable) {
            $cacheWritable = false;
        }

        return [
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
                'debug' => (bool) config('app.debug'),
                'url' => config('app.url'),
            ],
            'database' => [
                'ok' => $dbOk,
                'latency_ms' => $dbLatencyMs,
                'error' => $dbError,
                'connection' => config('database.default'),
            ],
            'queue' => [
                'driver' => $queueDriver,
                'pending_jobs' => $pendingJobs,
                'failed_jobs' => $failedJobs,
            ],
            'storage' => [
                'disk_free_bytes' => $diskFree !== false ? $diskFree : null,
                'disk_total_bytes' => $diskTotal !== false ? $diskTotal : null,
            ],
            'cache' => [
                'driver' => config('cache.default'),
                'writable' => $cacheWritable,
            ],
            'scheduler' => [
                'timezone' => config('app.timezone'),
                'note' => 'Ejecutar `php artisan schedule:work` o cron en producción.',
            ],
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
}

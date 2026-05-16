<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

/**
 * Snapshot ligero para futuras integraciones de IA / analítica operativa.
 */
class InstitutionMetricsSnapshotJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $payload = [
            'students' => Student::query()->count(),
            'users' => User::query()->count(),
            'payments_last_7d' => Payment::query()->where('paid_at', '>=', now()->subDays(7))->count(),
            'captured_at' => now()->toIso8601String(),
        ];

        Cache::put(
            (string) config('devops.metrics_cache_key', 'institution.metrics.snapshot'),
            $payload,
            (int) config('devops.metrics_cache_ttl_seconds', 3600),
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Jobs\CreateInstitutionalBackupJob;
use App\Services\InstitutionBackupService;
use App\Services\SystemHealthService;
use App\Support\EnvSecurityValidator;
use App\Support\SystemOperationsDashboard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Inertia\Response;

class IntranetSystemOperationsController extends Controller
{
    public function __construct(
        private readonly SystemHealthService $health,
        private readonly InstitutionBackupService $backups,
        private readonly EnvSecurityValidator $envValidator,
    ) {}

    public function health(Request $_request): Response
    {
        $this->authorize('viewHealth', SystemOperationsDashboard::class);

        return Inertia::render('Intranet/System/Health', [
            'health' => $this->health->healthSnapshot(),
            'metrics_snapshot' => $this->health->cachedMetricsSnapshot(),
            'env_issues' => $this->envValidator->validate(false),
            'backups_count' => count($this->backups->listBackups()),
            'recent_errors' => $this->recentErrorLines(),
        ]);
    }

    public function jobs(Request $request): Response
    {
        $this->authorize('viewJobs', SystemOperationsDashboard::class);

        $paginator = $this->health->failedJobsPaginator(15);

        $rows = collect($paginator->items())->map(fn ($row): array => [
            'id' => $row->id,
            'uuid' => $row->uuid ?? null,
            'connection' => $row->connection ?? null,
            'queue' => $row->queue ?? null,
            'exception' => isset($row->exception) ? mb_substr((string) $row->exception, 0, 400) : null,
            'failed_at' => $row->failed_at ?? null,
        ])->values()->all();

        return Inertia::render('Intranet/System/Jobs', [
            'failed_jobs' => [
                'data' => $rows,
                'links' => $paginator->linkCollection()->toArray(),
                'meta' => [
                    'total' => $paginator->total(),
                    'current_page' => $paginator->currentPage(),
                ],
            ],
            'queue_driver' => config('queue.default'),
        ]);
    }

    public function backups(Request $request): Response
    {
        $this->authorize('viewBackups', SystemOperationsDashboard::class);

        $list = collect($this->backups->listBackups())->map(fn (array $b): array => [
            'name' => $b['name'],
            'size_bytes' => $b['size_bytes'],
            'size_label' => $this->formatBytes($b['size_bytes']),
            'modified_at' => $b['modified_at'],
        ])->values()->all();

        return Inertia::render('Intranet/System/Backups', [
            'backups' => $list,
        ]);
    }

    public function dispatchBackup(Request $request): RedirectResponse
    {
        $this->authorize('dispatchBackup', SystemOperationsDashboard::class);

        dispatch(new CreateInstitutionalBackupJob)->afterCommit();

        return back()->with('success', 'Respaldo institucional encolado. Aparecerá en la lista al completarse.');
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes, 1024));

        return round($bytes / (1024 ** $i), 2).' '.$units[$i];
    }

    /**
     * @return list<string>
     */
    private function recentErrorLines(): array
    {
        $logPath = storage_path('logs/laravel.log');
        if (! File::exists($logPath)) {
            return [];
        }

        $contents = (string) File::get($logPath);
        $lines = preg_split('/\r\n|\r|\n/', $contents) ?: [];
        $errors = array_values(array_filter($lines, fn (string $line): bool => str_contains($line, '.ERROR:')));

        return array_slice($errors, -10);
    }
}

<?php

namespace App\Jobs;

use App\Services\InstitutionBackupService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CreateInstitutionalBackupJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 600;

    public function handle(InstitutionBackupService $backups): void
    {
        $path = $backups->createBackupArchive();
        Log::info('Institutional backup created', ['path' => $path]);
    }
}

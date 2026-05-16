<?php

namespace App\Console\Commands;

use App\Services\InstitutionBackupService;
use Illuminate\Console\Command;

class InstitutionPruneOldBackupsCommand extends Command
{
    protected $signature = 'institution:prune-old-backups';

    protected $description = 'Mantiene solo los N respaldos más recientes según configuración.';

    public function handle(InstitutionBackupService $backups): int
    {
        $backups->pruneOldBackups((int) config('devops.backup_max_files', 14));
        $this->info('Respaldos antiguos depurados.');

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use App\Services\InstitutionBackupService;
use Illuminate\Console\Command;

class InstitutionCreateBackupCommand extends Command
{
    protected $signature = 'institution:create-backup';

    protected $description = 'Genera un respaldo institucional (ZIP en storage/app/backups).';

    public function handle(InstitutionBackupService $backups): int
    {
        $path = $backups->createBackupArchive();
        $this->info('Respaldo creado: '.$path);

        return self::SUCCESS;
    }
}

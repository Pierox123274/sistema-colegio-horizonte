<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use Illuminate\Console\Command;

class InstitutionPurgeOldAuditLogsCommand extends Command
{
    protected $signature = 'institution:purge-old-audit-logs {--days= : Sobrescribe días de retención}';

    protected $description = 'Elimina registros de auditoría anteriores al umbral de retención.';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('devops.audit_log_retention_days', 365));
        $cutoff = now()->subDays(max(30, $days));

        $deleted = AuditLog::query()->where('created_at', '<', $cutoff)->delete();
        $this->info("Registros de auditoría eliminados: {$deleted}");

        return self::SUCCESS;
    }
}

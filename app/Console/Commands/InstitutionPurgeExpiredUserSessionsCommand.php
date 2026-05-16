<?php

namespace App\Console\Commands;

use App\Models\UserSession;
use Illuminate\Console\Command;

class InstitutionPurgeExpiredUserSessionsCommand extends Command
{
    protected $signature = 'institution:purge-expired-user-sessions {--days= : Sesiones inactivas más antiguas que N días}';

    protected $description = 'Elimina registros de sesión de usuario ya cerradas y antiguas.';

    public function handle(): int
    {
        $days = (int) ($this->option('days') ?: config('devops.user_session_retention_days', 90));
        $cutoff = now()->subDays(max(7, $days));

        $deleted = UserSession::query()
            ->where('is_active', false)
            ->where('updated_at', '<', $cutoff)
            ->delete();

        $this->info("Sesiones eliminadas: {$deleted}");

        return self::SUCCESS;
    }
}

<?php

namespace App\Jobs;

use App\Integrations\Services\InstitutionMailService;
use App\Models\IntegrationEmailLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

final class RetryInstitutionEmailJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $emailLogId,
    ) {}

    public function handle(InstitutionMailService $mail): void
    {
        $log = IntegrationEmailLog::query()->find($this->emailLogId);
        if ($log === null || $log->status === 'sent') {
            return;
        }

        Log::channel('integrations')->info('email_retry_attempt', [
            'log_id' => $log->id,
            'attempts' => $log->attempts,
        ]);

        $log->update(['status' => 'retrying']);
    }
}

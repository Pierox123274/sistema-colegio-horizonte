<?php

namespace App\Integrations\Services;

use App\Jobs\RetryInstitutionEmailJob;
use App\Models\IntegrationEmailLog;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

final class InstitutionMailService
{
    /**
     * @return array<string, mixed>
     */
    public function health(): array
    {
        $mailer = (string) config('mail.default');
        $configured = $mailer !== 'log' && config('mail.mailers.smtp.host') !== '127.0.0.1';
        $pending = Schema::hasTable('integration_email_logs')
            ? IntegrationEmailLog::query()->where('status', 'queued')->count()
            : 0;
        $failed = Schema::hasTable('integration_email_logs')
            ? IntegrationEmailLog::query()->where('status', 'failed')->count()
            : 0;

        return [
            'mailer' => $mailer,
            'configured' => $configured,
            'from' => config('mail.from.address'),
            'queue_pending' => $pending,
            'failed' => $failed,
            'preview_enabled' => (bool) config('integrations.mail.preview_enabled'),
        ];
    }

    public function sendLogged(Mailable $mailable, string|array $to): void
    {
        $recipients = is_array($to) ? $to : [$to];
        $log = null;

        if (config('integrations.mail.delivery_log_enabled') && Schema::hasTable('integration_email_logs')) {
            $log = IntegrationEmailLog::query()->create([
                'mailable_class' => $mailable::class,
                'recipient_hash' => hash('sha256', implode(',', $recipients)),
                'subject' => null,
                'status' => 'queued',
                'mailer' => config('mail.default'),
            ]);
        }

        try {
            Mail::to($recipients)->queue($mailable);

            $log?->update([
                'status' => 'queued',
                'attempts' => 1,
            ]);
        } catch (\Throwable $e) {
            $log?->update([
                'status' => 'failed',
                'error_message' => mb_substr($e->getMessage(), 0, 500),
            ]);

            if ($log !== null) {
                RetryInstitutionEmailJob::dispatch($log->id)->delay(now()->addMinutes(5));
            }

            throw $e;
        }
    }

    public function markSent(IntegrationEmailLog $log): void
    {
        $log->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }

    public function markFailed(IntegrationEmailLog $log, string $message): void
    {
        $attempts = $log->attempts + 1;
        $max = (int) config('integrations.mail.max_retries', 3);

        $log->update([
            'status' => $attempts >= $max ? 'failed' : 'retrying',
            'attempts' => $attempts,
            'error_message' => mb_substr($message, 0, 500),
        ]);

        if ($attempts < $max) {
            RetryInstitutionEmailJob::dispatch($log->id)->delay(now()->addMinutes(min(30, 5 * $attempts)));
        }
    }
}

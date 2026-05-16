<?php

namespace App\Jobs;

use App\Mail\SecurityDigestMail;
use App\Models\LoginAttempt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SecurityHealthScanJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $to = config('devops.operations_email');
        if (! is_string($to) || $to === '') {
            return;
        }

        $failed24h = LoginAttempt::query()
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subHours(24))
            ->count();

        if ($failed24h < (int) config('security.login_max_attempts', 5) * 3) {
            return;
        }

        Mail::to($to)->send(new SecurityDigestMail([
            'failed_logins_24h' => $failed24h,
            'message' => 'Se detectó un volumen elevado de intentos fallidos en las últimas 24 horas.',
        ]));
    }
}

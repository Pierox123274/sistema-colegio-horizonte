<?php

namespace App\Jobs;

use App\Enums\IntranetRole;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Mail\SecurityDigestMail;
use App\Models\LoginAttempt;
use App\Models\User;
use App\Services\UserNotificationService;
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

        app(UserNotificationService::class)->notifyMany(
            users: User::query()->role(IntranetRole::Administrador->value)->get(),
            title: 'Alerta de seguridad',
            message: "Intentos fallidos en 24h: {$failed24h}",
            category: NotificationCategory::Security,
            priority: NotificationPriority::Critical,
            actionUrl: route('intranet.security.access-monitor.index', absolute: false),
            actionLabel: 'Revisar seguridad',
            forceEmail: true,
            mailTemplate: 'security-alert'
        );
    }
}

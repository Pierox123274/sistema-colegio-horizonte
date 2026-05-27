<?php

namespace App\Jobs;

use App\Enums\IntranetRole;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\User;
use App\Services\SystemHealthService;
use App\Services\UserNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendSystemNotificationsJob implements ShouldQueue
{
    use Queueable;

    public function handle(
        UserNotificationService $notifications,
        SystemHealthService $healthService
    ): void {
        $health = $healthService->healthSnapshot();
        $status = (string) ($health['status'] ?? 'ok');
        if ($status === 'ok') {
            return;
        }

        $admins = User::query()->role(IntranetRole::Administrador->value)->get();
        $priority = $status === 'critical' ? NotificationPriority::Critical : NotificationPriority::High;

        $notifications->notifyMany(
            users: $admins,
            title: 'Alerta operativa del sistema',
            message: 'Health check reporta estado '.$status.'.',
            category: NotificationCategory::System,
            priority: $priority,
            actionUrl: route('intranet.system.health.index', absolute: false),
            actionLabel: 'Revisar salud',
            forceEmail: $status === 'critical',
            mailTemplate: 'security-alert'
        );
    }
}

<?php

namespace App\Jobs;

use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Enums\PensionStatus;
use App\Models\Pension;
use App\Services\UserNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendFinancialRemindersJob implements ShouldQueue
{
    use Queueable;

    public function handle(UserNotificationService $notifications): void
    {
        $upcoming = Pension::query()
            ->with('enrollment.student.user')
            ->whereDate('due_date', '>=', today())
            ->whereDate('due_date', '<=', now()->addDays(5)->toDateString())
            ->whereIn('status', [PensionStatus::Pendiente->value, PensionStatus::Parcial->value])
            ->get();

        foreach ($upcoming as $pension) {
            $user = $pension->enrollment?->student?->user;
            if ($user === null) {
                continue;
            }

            $notifications->notifyUser(
                user: $user,
                title: 'Pago próximo',
                message: "Tienes una pensión con vencimiento {$pension->due_date?->format('d/m/Y')}.",
                category: NotificationCategory::Financial,
                priority: NotificationPriority::Medium,
                actionUrl: route('student.payments.index', absolute: false),
                actionLabel: 'Ver pagos',
                meta: ['pension_id' => $pension->id]
            );
        }

        $overdue = Pension::query()
            ->with('enrollment.student.user')
            ->where('status', PensionStatus::Vencido->value)
            ->get();

        foreach ($overdue as $pension) {
            $user = $pension->enrollment?->student?->user;
            if ($user === null) {
                continue;
            }

            $notifications->notifyUser(
                user: $user,
                title: 'Pago vencido',
                message: 'Tienes una pensión vencida pendiente de regularizar.',
                category: NotificationCategory::Financial,
                priority: NotificationPriority::High,
                actionUrl: route('student.payments.index', absolute: false),
                actionLabel: 'Regularizar pago',
                meta: ['pension_id' => $pension->id]
            );
        }
    }
}

<?php

namespace App\Jobs;

use App\Enums\PensionStatus;
use App\Mail\FinancialAlertSummaryMail;
use App\Models\Pension;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class FinancialAlertScanJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $overdue = Pension::query()->where('status', PensionStatus::Vencido->value)->count();
        $to = config('devops.operations_email');
        if ($overdue === 0 || ! is_string($to) || $to === '') {
            return;
        }

        Mail::to($to)->send(new FinancialAlertSummaryMail([
            'overdue_pensions' => $overdue,
            'message' => 'Hay pensiones marcadas como vencidas.',
        ]));
    }
}

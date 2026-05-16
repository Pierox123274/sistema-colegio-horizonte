<?php

namespace App\Jobs;

use App\Enums\EnrollmentStatus;
use App\Mail\AcademicAlertSummaryMail;
use App\Models\Enrollment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class AcademicAlertScanJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        $pending = Enrollment::query()->where('status', EnrollmentStatus::Pendiente->value)->count();
        $to = config('devops.operations_email');
        if ($pending === 0 || ! is_string($to) || $to === '') {
            return;
        }

        Mail::to($to)->send(new AcademicAlertSummaryMail([
            'pending_enrollments' => $pending,
            'message' => 'Hay matrículas pendientes de revisión.',
        ]));
    }
}

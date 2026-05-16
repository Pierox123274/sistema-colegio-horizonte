<?php

namespace App\Jobs;

use App\Enums\EnrollmentStatus;
use App\Mail\InstitutionDailySummaryMail;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendInstitutionDailySummaryJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        if (! config('devops.send_daily_summary', false)) {
            return;
        }

        $to = config('devops.operations_email');
        if (! is_string($to) || $to === '') {
            return;
        }

        $metrics = [
            'students_total' => Student::query()->count(),
            'users_total' => User::query()->count(),
            'enrollments_pending' => Enrollment::query()->where('status', EnrollmentStatus::Pendiente->value)->count(),
            'payments_today' => Payment::query()->whereDate('paid_at', today())->count(),
            'generated_at' => now()->translatedFormat('d/m/Y H:i'),
        ];

        Mail::to($to)->send(new InstitutionDailySummaryMail($metrics));
    }
}

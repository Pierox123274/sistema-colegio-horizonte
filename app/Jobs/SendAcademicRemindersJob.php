<?php

namespace App\Jobs;

use App\Enums\EnrollmentStatus;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\Assignment;
use App\Models\OnlineExam;
use App\Models\User;
use App\Services\UserNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendAcademicRemindersJob implements ShouldQueue
{
    use Queueable;

    public function handle(UserNotificationService $notifications): void
    {
        $windowStart = now();
        $windowEnd = now()->addHours(24);

        $assignments = Assignment::query()
            ->with('virtualClassroom')
            ->whereNotNull('due_at')
            ->whereBetween('due_at', [$windowStart, $windowEnd])
            ->where('is_published', true)
            ->get();

        foreach ($assignments as $assignment) {
            $classroom = $assignment->virtualClassroom;
            if ($classroom === null) {
                continue;
            }

            $users = User::query()
                ->whereHas('student.enrollments', function ($query) use ($classroom): void {
                    $query->where('academic_year_id', $classroom->academic_year_id)
                        ->where('section_id', $classroom->section_id)
                        ->where('status', EnrollmentStatus::Matriculado->value);
                })
                ->get();

            $notifications->notifyMany(
                users: $users,
                title: 'Entrega pendiente',
                message: "La tarea '{$assignment->title}' vence pronto.",
                category: NotificationCategory::Academic,
                priority: NotificationPriority::High,
                actionUrl: route('student.classrooms.show', $classroom, absolute: false),
                actionLabel: 'Ver aula',
                mailTemplate: 'task-new',
                meta: ['assignment_id' => $assignment->id]
            );
        }

        $exams = OnlineExam::query()
            ->with('virtualClassroom')
            ->whereNotNull('available_until')
            ->whereBetween('available_until', [$windowStart, $windowEnd])
            ->where('is_published', true)
            ->get();

        foreach ($exams as $exam) {
            $classroom = $exam->virtualClassroom;
            if ($classroom === null) {
                continue;
            }

            $users = User::query()
                ->whereHas('student.enrollments', function ($query) use ($classroom): void {
                    $query->where('academic_year_id', $classroom->academic_year_id)
                        ->where('section_id', $classroom->section_id)
                        ->where('status', EnrollmentStatus::Matriculado->value);
                })
                ->get();

            $notifications->notifyMany(
                users: $users,
                title: 'Evaluación próxima a cerrar',
                message: "La evaluación '{$exam->title}' está por finalizar.",
                category: NotificationCategory::Academic,
                priority: NotificationPriority::High,
                actionUrl: route('student.classrooms.show', $classroom, absolute: false),
                actionLabel: 'Revisar evaluación',
                mailTemplate: 'exam-reminder',
                meta: ['online_exam_id' => $exam->id]
            );
        }
    }
}

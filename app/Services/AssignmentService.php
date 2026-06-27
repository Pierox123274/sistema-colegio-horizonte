<?php

namespace App\Services;

use App\Enums\AssignmentSubmissionStatus;
use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Enums\EnrollmentStatus;
use App\Enums\ExperienceSource;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\User;
use App\Models\VirtualClassroom;
use Illuminate\Http\UploadedFile;

final class AssignmentService
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly LMSService $lms,
        private readonly LMSAdaptiveIntegrationService $adaptive,
        private readonly LMSGradeSyncService $gradeSync,
        private readonly GamificationService $gamification,
        private readonly UserNotificationService $notifications,
    ) {}

    public function createAssignment(User $user, VirtualClassroom $classroom, array $data): Assignment
    {
        $assignment = Assignment::query()->create([
            'virtual_classroom_id' => $classroom->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'max_score' => $data['max_score'] ?? 20,
            'due_at' => $data['due_at'] ?? null,
            'rubric' => $data['rubric'] ?? null,
            'is_published' => $data['is_published'] ?? true,
            'created_by_user_id' => $user->id,
        ]);

        $this->lms->syncCalendarForAssignment($assignment);

        $this->audit->log(
            AuditAction::Create,
            AuditModule::Lms,
            $user,
            Assignment::class,
            $assignment->id,
            'Creación de tarea',
            null,
            ['title' => $assignment->title],
            AuditResult::Success,
        );

        $recipients = User::query()
            ->whereHas('student.enrollments', function ($query) use ($classroom): void {
                $query->where('academic_year_id', $classroom->academic_year_id)
                    ->where('section_id', $classroom->section_id)
                    ->where('status', EnrollmentStatus::Matriculado->value);
            })
            ->get();

        $this->notifications->notifyMany(
            users: $recipients,
            title: 'Nueva tarea en aula virtual',
            message: $assignment->title,
            category: NotificationCategory::Lms,
            priority: NotificationPriority::High,
            actionUrl: route('student.classrooms.show', $classroom, absolute: false),
            actionLabel: 'Ir al aula',
            mailTemplate: 'task-new',
            meta: ['assignment_id' => $assignment->id]
        );

        return $assignment;
    }

    public function ensureSubmissionRow(Assignment $assignment, Student $student): AssignmentSubmission
    {
        return AssignmentSubmission::query()->firstOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
            ],
            [
                'user_id' => $student->user_id,
                'status' => AssignmentSubmissionStatus::Pending,
            ],
        );
    }

    public function submit(
        User $user,
        Student $student,
        Assignment $assignment,
        ?string $comment,
        ?UploadedFile $file,
    ): AssignmentSubmission {
        $submission = $this->ensureSubmissionRow($assignment, $student);

        $path = $submission->file_path;
        if ($file !== null) {
            $path = $file->store('lms/submissions/'.$assignment->id, 'public');
        }

        $status = AssignmentSubmissionStatus::Submitted;
        if ($assignment->due_at !== null && now()->gt($assignment->due_at)) {
            $status = AssignmentSubmissionStatus::Overdue;
        }

        $submission->fill([
            'user_id' => $user->id,
            'status' => $status,
            'file_path' => $path,
            'student_comment' => $comment,
            'submitted_at' => now(),
        ]);
        $submission->save();

        $this->audit->log(
            AuditAction::Create,
            AuditModule::Lms,
            $user,
            AssignmentSubmission::class,
            $submission->id,
            'Entrega de tarea',
            null,
            ['assignment_id' => $assignment->id],
            AuditResult::Success,
        );

        $this->gamification->awardXp(
            $student,
            ExperienceSource::AssignmentCompleted,
            50,
            'Tarea completada en aula virtual',
            $assignment
        );

        if ($assignment->due_at !== null && now()->lt($assignment->due_at)) {
            $this->gamification->awardXp(
                $student,
                ExperienceSource::AssignmentEarly,
                20,
                'Entrega temprana',
                $assignment
            );
        }

        return $submission;
    }

    public function grade(
        User $teacher,
        AssignmentSubmission $submission,
        float $score,
        ?string $feedback,
    ): AssignmentSubmission {
        $submission->loadMissing('assignment');
        $max = (float) $submission->assignment->max_score;
        $normalized = min($max, max(0, $score));

        $submission->fill([
            'score' => $normalized,
            'teacher_feedback' => $feedback,
            'status' => AssignmentSubmissionStatus::Reviewed,
            'reviewed_at' => now(),
            'reviewed_by_user_id' => $teacher->id,
        ]);
        $submission->save();

        $this->adaptive->onAssignmentGraded($submission);
        $this->gradeSync->syncAssignmentSubmission($submission);

        $this->audit->log(
            AuditAction::Update,
            AuditModule::Lms,
            $teacher,
            AssignmentSubmission::class,
            $submission->id,
            'Calificación de tarea',
            null,
            ['score' => $normalized],
            AuditResult::Success,
        );

        return $submission;
    }

    public function resolveSubmissionStatus(Assignment $assignment, ?AssignmentSubmission $submission): string
    {
        if ($submission === null) {
            if ($assignment->due_at !== null && now()->gt($assignment->due_at)) {
                return AssignmentSubmissionStatus::Overdue->value;
            }

            return AssignmentSubmissionStatus::Pending->value;
        }

        return $submission->status->value;
    }
}

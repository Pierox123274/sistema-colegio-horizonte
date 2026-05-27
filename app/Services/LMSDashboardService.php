<?php

namespace App\Services;

use App\Enums\AssignmentSubmissionStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\MeetingStatus;
use App\Enums\OnlineExamAttemptStatus;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Enrollment;
use App\Models\OnlineExam;
use App\Models\OnlineExamAttempt;
use App\Models\Student;
use App\Models\User;
use App\Models\VirtualClassroom;
use App\Models\VirtualMeeting;

final class LMSDashboardService
{
    public function __construct(
        private readonly VirtualClassroomAccessService $access,
        private readonly TeacherContextService $teacherContext,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function teacherSummary(User $teacher): array
    {
        $classroomIds = $this->access->queryClassroomsForTeacher($teacher)->pluck('id');

        $pendingReview = AssignmentSubmission::query()
            ->where('status', AssignmentSubmissionStatus::Submitted)
            ->whereHas('assignment', fn ($q) => $q->whereIn('virtual_classroom_id', $classroomIds))
            ->count();

        $sectionIds = $this->teacherContext->activeSectionIdsFor($teacher);
        $studentIds = [];
        if ($sectionIds !== []) {
            $year = $this->teacherContext->activeAcademicYear();
            if ($year !== null) {
                $studentIds = Enrollment::query()
                    ->where('academic_year_id', $year->id)
                    ->whereIn('section_id', $sectionIds)
                    ->where('status', EnrollmentStatus::Matriculado->value)
                    ->pluck('student_id')
                    ->all();
            }
        }

        $missingSubmissions = 0;
        if ($studentIds !== []) {
            $publishedAssignments = Assignment::query()
                ->whereIn('virtual_classroom_id', $classroomIds)
                ->where('is_published', true)
                ->count();
            $submitted = AssignmentSubmission::query()
                ->whereIn('assignment_id', Assignment::query()->whereIn('virtual_classroom_id', $classroomIds)->select('id'))
                ->whereIn('student_id', $studentIds)
                ->whereIn('status', [
                    AssignmentSubmissionStatus::Submitted->value,
                    AssignmentSubmissionStatus::Reviewed->value,
                ])
                ->select('student_id', 'assignment_id')
                ->groupBy('student_id', 'assignment_id')
                ->get()
                ->count();
            $missingSubmissions = max(0, ($publishedAssignments * count($studentIds)) - $submitted);
        }

        $activeExams = OnlineExam::query()
            ->whereIn('virtual_classroom_id', $classroomIds)
            ->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('available_until')->orWhere('available_until', '>=', now()))
            ->count();

        $upcomingMeetings = VirtualMeeting::query()
            ->whereIn('status', [MeetingStatus::Scheduled->value, MeetingStatus::Live->value])
            ->where('ends_at', '>=', now())
            ->where(function ($q) use ($teacher, $classroomIds): void {
                $q->where('host_user_id', $teacher->id)
                    ->orWhereHas('participants', fn ($p) => $p->where('user_id', $teacher->id))
                    ->orWhereIn('virtual_classroom_id', $classroomIds);
            })
            ->count();

        return [
            'classrooms_count' => $classroomIds->count(),
            'pending_review' => $pendingReview,
            'missing_submissions_estimate' => $missingSubmissions,
            'active_exams' => $activeExams,
            'upcoming_meetings' => $upcomingMeetings,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function studentSummary(Student $student): array
    {
        $classroomIds = $this->access->queryClassroomsForStudent($student)->pluck('id');

        $pendingAssignments = Assignment::query()
            ->whereIn('virtual_classroom_id', $classroomIds)
            ->where('is_published', true)
            ->whereDoesntHave('submissions', fn ($q) => $q
                ->where('student_id', $student->id)
                ->whereIn('status', [
                    AssignmentSubmissionStatus::Submitted->value,
                    AssignmentSubmissionStatus::Reviewed->value,
                ]))
            ->count();

        $upcomingExams = OnlineExam::query()
            ->whereIn('virtual_classroom_id', $classroomIds)
            ->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('available_until')->orWhere('available_until', '>=', now()))
            ->count();

        $studentUserId = $student->user_id;
        $upcomingMeetings = $studentUserId
            ? VirtualMeeting::query()
                ->whereIn('status', [MeetingStatus::Scheduled->value, MeetingStatus::Live->value])
                ->where('ends_at', '>=', now())
                ->whereHas('participants', fn ($q) => $q->where('user_id', $studentUserId))
                ->count()
            : 0;

        return [
            'classrooms_count' => $classroomIds->count(),
            'pending_assignments' => $pendingAssignments,
            'upcoming_exams' => $upcomingExams,
            'upcoming_meetings' => $upcomingMeetings,
        ];
    }

    /**
     * @return array<string, int>
     */
    public function institutionOverview(): array
    {
        return [
            'classrooms' => VirtualClassroom::query()->where('is_active', true)->count(),
            'assignments' => Assignment::query()->count(),
            'submissions' => AssignmentSubmission::query()
                ->whereIn('status', [
                    AssignmentSubmissionStatus::Submitted->value,
                    AssignmentSubmissionStatus::Reviewed->value,
                ])
                ->count(),
            'exam_attempts' => OnlineExamAttempt::query()
                ->where('status', OnlineExamAttemptStatus::Completed)
                ->count(),
        ];
    }
}

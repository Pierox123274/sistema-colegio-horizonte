<?php

namespace Tests\Unit\LMS;

use App\Enums\AssignmentSubmissionStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Enums\OnlineExamAttemptStatus;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\GradeRecord;
use App\Models\OnlineExam;
use App\Models\OnlineExamAttempt;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Models\VirtualClassroom;
use App\Services\LMSGradeSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LMSGradeSyncServiceTest extends TestCase
{
    use RefreshDatabase;

    private LMSGradeSyncService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(LMSGradeSyncService::class);
    }

    public function test_syncs_assignment_grade_to_grade_record(): void
    {
        ['classroom' => $classroom, 'teacher' => $teacher, 'student' => $student] = $this->lmsContext();

        $assignment = Assignment::factory()->create([
            'virtual_classroom_id' => $classroom->id,
            'max_score' => 20,
            'created_by_user_id' => $teacher->id,
        ]);

        $submission = AssignmentSubmission::query()->create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'user_id' => $student->user_id,
            'status' => AssignmentSubmissionStatus::Reviewed,
            'score' => 14.5,
            'reviewed_by_user_id' => $teacher->id,
            'reviewed_at' => now(),
        ]);

        $this->service->syncAssignmentSubmission($submission);

        $assignment->refresh();
        $this->assertNotNull($assignment->evaluation_id);

        $evaluation = Evaluation::query()->findOrFail($assignment->evaluation_id);
        $this->assertStringContainsString('LMS — Tarea', $evaluation->title);
        $this->assertEquals(20.0, (float) $evaluation->max_score);

        $record = GradeRecord::query()
            ->where('evaluation_id', $evaluation->id)
            ->where('student_id', $student->id)
            ->first();

        $this->assertNotNull($record);
        $this->assertEquals(14.5, (float) $record->score);
        $this->assertSame($teacher->id, $record->recorded_by_user_id);
    }

    public function test_syncs_online_exam_percent_to_grade_record_scale(): void
    {
        ['classroom' => $classroom, 'teacher' => $teacher, 'student' => $student] = $this->lmsContext();

        $exam = OnlineExam::factory()->create([
            'virtual_classroom_id' => $classroom->id,
            'created_by_user_id' => $teacher->id,
        ]);

        $attempt = OnlineExamAttempt::query()->create([
            'online_exam_id' => $exam->id,
            'student_id' => $student->id,
            'user_id' => $student->user_id,
            'attempt_number' => 1,
            'status' => OnlineExamAttemptStatus::Completed,
            'score_percent' => 75,
            'answers' => [],
            'started_at' => now(),
            'completed_at' => now(),
        ]);

        $this->service->syncOnlineExamAttempt($attempt);

        $exam->refresh();
        $evaluation = Evaluation::query()->findOrFail($exam->evaluation_id);
        $this->assertStringContainsString('LMS — Examen', $evaluation->title);

        $record = GradeRecord::query()
            ->where('evaluation_id', $evaluation->id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $this->assertEquals(15.0, (float) $record->score);
    }

    public function test_regrading_assignment_updates_existing_grade_record(): void
    {
        ['classroom' => $classroom, 'teacher' => $teacher, 'student' => $student] = $this->lmsContext();

        $assignment = Assignment::factory()->create([
            'virtual_classroom_id' => $classroom->id,
            'created_by_user_id' => $teacher->id,
        ]);

        $submission = AssignmentSubmission::query()->create([
            'assignment_id' => $assignment->id,
            'student_id' => $student->id,
            'user_id' => $student->user_id,
            'status' => AssignmentSubmissionStatus::Reviewed,
            'score' => 10,
            'reviewed_by_user_id' => $teacher->id,
        ]);

        $this->service->syncAssignmentSubmission($submission);

        $submission->update(['score' => 18]);
        $this->service->syncAssignmentSubmission($submission->fresh());

        $this->assertSame(1, GradeRecord::query()->count());
        $this->assertEquals(18.0, (float) GradeRecord::query()->value('score'));
    }

    /**
     * @return array{classroom: VirtualClassroom, teacher: User, student: Student}
     */
    private function lmsContext(): array
    {
        $year = AcademicYear::factory()->active()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $subject = Subject::factory()->create();

        $teacher = User::factory()->create();
        $teacher->syncRoles([IntranetRole::Docente->value]);

        TeacherAssignment::factory()->create([
            'user_id' => $teacher->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'is_active' => true,
        ]);

        $studentUser = User::factory()->create();
        $studentUser->syncRoles([IntranetRole::Estudiante->value]);
        $student = Student::factory()->create(['user_id' => $studentUser->id]);

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $classroom = VirtualClassroom::factory()->create([
            'academic_year_id' => $year->id,
            'section_id' => $section->id,
            'subject_id' => $subject->id,
            'teacher_user_id' => $teacher->id,
            'created_by_user_id' => $teacher->id,
        ]);

        return compact('classroom', 'teacher', 'student');
    }
}

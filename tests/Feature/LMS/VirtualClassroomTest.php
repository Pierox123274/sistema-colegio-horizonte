<?php

namespace Tests\Feature\LMS;

use App\Enums\AssignmentSubmissionStatus;
use App\Enums\AuditModule;
use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Enums\OnlineExamAttemptStatus;
use App\Enums\OnlineExamQuestionType;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\AuditLog;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\LearningRecommendation;
use App\Models\OnlineExam;
use App\Models\OnlineExamAttempt;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Models\VirtualClassroom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class VirtualClassroomTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{
     *     year: AcademicYear,
     *     section: Section,
     *     subject: Subject,
     *     teacher: User,
     *     studentUser: User,
     *     student: Student,
     *     otherStudentUser: User,
     * }
     */
    private function setupLmsScenario(): array
    {
        $year = AcademicYear::factory()->active()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $sectionA = Section::factory()->create([
            'grade_id' => $grade->id,
            'code' => 'LMS-A',
            'name' => 'Sección LMS A',
        ]);
        $sectionB = Section::factory()->create([
            'grade_id' => $grade->id,
            'code' => 'LMS-B',
            'name' => 'Sección LMS B',
        ]);
        $subject = Subject::factory()->create();

        $teacher = User::factory()->create();
        $teacher->syncRoles([IntranetRole::Docente->value]);

        TeacherAssignment::factory()->create([
            'user_id' => $teacher->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $sectionA->id,
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
            'section_id' => $sectionA->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $otherStudentUser = User::factory()->create();
        $otherStudentUser->syncRoles([IntranetRole::Estudiante->value]);
        $otherStudent = Student::factory()->create(['user_id' => $otherStudentUser->id]);
        Enrollment::factory()->create([
            'student_id' => $otherStudent->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $sectionB->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        return compact('year', 'sectionA', 'sectionB', 'subject', 'teacher', 'studentUser', 'student', 'otherStudentUser');
    }

    public function test_teacher_creates_classroom_assignment_and_exam(): void
    {
        $ctx = $this->setupLmsScenario();

        $this->actingAs($ctx['teacher'])
            ->post(route('teacher.classrooms.store'), [
                'title' => 'Aula Matemáticas',
                'description' => 'Contenidos semanales',
                'academic_year_id' => $ctx['year']->id,
                'section_id' => $ctx['sectionA']->id,
                'subject_id' => $ctx['subject']->id,
            ])
            ->assertRedirect();

        $classroom = VirtualClassroom::query()->firstOrFail();
        $this->assertSame('Aula Matemáticas', $classroom->title);

        $this->actingAs($ctx['teacher'])
            ->post(route('teacher.classrooms.assignments.store', $classroom), [
                'title' => 'Tarea 1',
                'max_score' => 20,
            ])
            ->assertRedirect(route('teacher.classrooms.show', $classroom));

        $this->assertDatabaseHas('assignments', ['title' => 'Tarea 1', 'virtual_classroom_id' => $classroom->id]);

        $this->actingAs($ctx['teacher'])
            ->post(route('teacher.classrooms.exams.store', $classroom), [
                'title' => 'Examen parcial',
                'question_stem' => '2+2=?',
                'correct_option' => 'a',
            ])
            ->assertRedirect(route('teacher.classrooms.show', $classroom));

        $this->assertDatabaseHas('online_exams', ['title' => 'Examen parcial', 'virtual_classroom_id' => $classroom->id]);
    }

    public function test_student_submits_assignment_teacher_grades_and_adaptive_hooks(): void
    {
        $ctx = $this->setupLmsScenario();

        $classroom = VirtualClassroom::factory()->create([
            'academic_year_id' => $ctx['year']->id,
            'section_id' => $ctx['sectionA']->id,
            'subject_id' => $ctx['subject']->id,
            'teacher_user_id' => $ctx['teacher']->id,
            'created_by_user_id' => $ctx['teacher']->id,
        ]);

        $assignment = Assignment::factory()->create([
            'virtual_classroom_id' => $classroom->id,
            'max_score' => 20,
            'created_by_user_id' => $ctx['teacher']->id,
        ]);

        $this->actingAs($ctx['studentUser'])
            ->post(route('student.classrooms.assignments.submit', [$classroom, $assignment]), [
                'student_comment' => 'Entrega lista',
            ])
            ->assertRedirect();

        $submission = AssignmentSubmission::query()->firstOrFail();
        $this->assertSame(AssignmentSubmissionStatus::Submitted->value, $submission->status->value);

        $this->actingAs($ctx['teacher'])
            ->post(route('teacher.classrooms.submissions.grade', [$classroom, $assignment, $submission]), [
                'score' => 8,
                'teacher_feedback' => 'Revisar ejercicios',
            ])
            ->assertRedirect();

        $submission->refresh();
        $this->assertSame(AssignmentSubmissionStatus::Reviewed->value, $submission->status->value);
        $this->assertEquals(8.0, (float) $submission->score);

        $this->assertTrue(
            LearningRecommendation::query()->where('student_id', $ctx['student']->id)->exists(),
            'Calificación baja debe generar recomendación adaptativa',
        );

        $this->assertTrue(
            AuditLog::query()->where('module', AuditModule::Lms->value)->exists(),
            'Debe auditar acciones LMS',
        );

        $assignment->refresh();
        $this->assertNotNull($assignment->evaluation_id);

        $this->assertDatabaseHas('grade_records', [
            'evaluation_id' => $assignment->evaluation_id,
            'student_id' => $ctx['student']->id,
            'score' => 8,
        ]);
    }

    public function test_student_completes_online_exam(): void
    {
        $ctx = $this->setupLmsScenario();

        $classroom = VirtualClassroom::factory()->create([
            'academic_year_id' => $ctx['year']->id,
            'section_id' => $ctx['sectionA']->id,
            'subject_id' => $ctx['subject']->id,
            'teacher_user_id' => $ctx['teacher']->id,
        ]);

        $exam = OnlineExam::factory()->create([
            'virtual_classroom_id' => $classroom->id,
            'created_by_user_id' => $ctx['teacher']->id,
        ]);
        $question = $exam->questions()->create([
            'question_type' => OnlineExamQuestionType::MultipleChoice,
            'stem' => 'Capital de Perú',
            'options' => [
                ['label' => 'A', 'value' => 'a'],
                ['label' => 'B', 'value' => 'b'],
            ],
            'correct_answer' => ['value' => 'a'],
            'points' => 1,
            'sort_order' => 1,
        ]);

        $this->actingAs($ctx['studentUser'])
            ->post(route('student.classrooms.exams.start', $exam))
            ->assertRedirect();

        $attempt = OnlineExamAttempt::query()->firstOrFail();
        $this->assertSame(OnlineExamAttemptStatus::InProgress->value, $attempt->status->value);

        $this->actingAs($ctx['studentUser'])
            ->post(route('student.classrooms.exam-attempt.answer', $attempt), [
                'answers' => [$question->id => 'a'],
            ])
            ->assertRedirect(route('student.classrooms.exam-attempt', $attempt));

        $attempt->refresh();
        $this->assertSame(OnlineExamAttemptStatus::Completed->value, $attempt->status->value);
        $this->assertEquals(100.0, (float) $attempt->score_percent);

        $exam->refresh();
        $this->assertNotNull($exam->evaluation_id);

        $this->assertDatabaseHas('grade_records', [
            'evaluation_id' => $exam->evaluation_id,
            'student_id' => $ctx['student']->id,
            'score' => 20,
        ]);
    }

    public function test_student_cannot_access_classroom_outside_enrollment(): void
    {
        $ctx = $this->setupLmsScenario();

        $classroom = VirtualClassroom::factory()->create([
            'academic_year_id' => $ctx['year']->id,
            'section_id' => $ctx['sectionA']->id,
            'subject_id' => $ctx['subject']->id,
            'teacher_user_id' => $ctx['teacher']->id,
        ]);

        $this->actingAs($ctx['otherStudentUser'])
            ->get(route('student.classrooms.show', $classroom))
            ->assertForbidden();
    }

    public function test_teacher_cannot_create_classroom_for_unassigned_section(): void
    {
        $ctx = $this->setupLmsScenario();

        $this->actingAs($ctx['teacher'])
            ->post(route('teacher.classrooms.store'), [
                'title' => 'Aula no asignada',
                'academic_year_id' => $ctx['year']->id,
                'section_id' => $ctx['sectionB']->id,
                'subject_id' => $ctx['subject']->id,
            ])
            ->assertForbidden();
    }

    public function test_dashboards_include_lms_summary(): void
    {
        $ctx = $this->setupLmsScenario();

        VirtualClassroom::factory()->create([
            'academic_year_id' => $ctx['year']->id,
            'section_id' => $ctx['sectionA']->id,
            'subject_id' => $ctx['subject']->id,
            'teacher_user_id' => $ctx['teacher']->id,
        ]);

        $this->actingAs($ctx['teacher'])
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Teacher/Dashboard')
                ->has('lms.classrooms_count'));

        $this->actingAs($ctx['studentUser'])
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Student/Dashboard')
                ->has('lms.classrooms_count'));
    }

    public function test_admin_views_lms_institution_overview(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles([IntranetRole::Administrador->value]);

        $this->actingAs($admin)
            ->get(route('intranet.lms.overview'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/LMS/Overview')
                ->has('overview.classrooms'));
    }

    public function test_teacher_and_student_classroom_pages_respond(): void
    {
        $ctx = $this->setupLmsScenario();

        $classroom = VirtualClassroom::factory()->create([
            'academic_year_id' => $ctx['year']->id,
            'section_id' => $ctx['sectionA']->id,
            'subject_id' => $ctx['subject']->id,
            'teacher_user_id' => $ctx['teacher']->id,
        ]);

        $this->actingAs($ctx['teacher'])
            ->get(route('teacher.classrooms.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Teacher/Classrooms/Index'));

        $this->actingAs($ctx['teacher'])
            ->get(route('teacher.classrooms.show', $classroom))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Teacher/Classrooms/Show'));

        $this->actingAs($ctx['studentUser'])
            ->get(route('student.classrooms.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Student/Classrooms/Index'));

        $this->actingAs($ctx['studentUser'])
            ->get(route('student.classrooms.show', $classroom))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Student/Classrooms/Show'));
    }
}

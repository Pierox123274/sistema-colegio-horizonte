<?php

namespace Tests\Feature\AdaptiveLearning;

use App\Enums\DiagnosticExamMode;
use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\AcademicYear;
use App\Models\DiagnosticExam;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\QuestionBank;
use App\Models\Section;
use App\Models\Student;
use App\Models\Subject;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdaptiveLearningTest extends TestCase
{
    use RefreshDatabase;

    private function studentWithProfile(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Estudiante->value]);
        Student::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    private function createFixedExamWithTwoMcQuestions(): DiagnosticExam
    {
        $subject = Subject::factory()->create();

        $q1 = QuestionBank::factory()->create([
            'subject_id' => $subject->id,
            'topic' => 'Fracciones',
            'question_type' => QuestionType::MultipleChoice,
            'difficulty' => QuestionDifficulty::Basic,
            'stem' => '¿Cuánto es 1+1?',
        ]);
        $opt1a = $q1->options()->create(['sort_order' => 0, 'label' => 'A', 'body' => '2', 'is_correct' => true]);
        $q1->options()->create(['sort_order' => 1, 'label' => 'B', 'body' => '3', 'is_correct' => false]);

        $q2 = QuestionBank::factory()->create([
            'subject_id' => $subject->id,
            'topic' => 'Geometría',
            'question_type' => QuestionType::MultipleChoice,
            'difficulty' => QuestionDifficulty::Intermediate,
            'stem' => 'Un triángulo tiene cuántos lados?',
        ]);
        $opt2a = $q2->options()->create(['sort_order' => 0, 'label' => 'A', 'body' => '3', 'is_correct' => true]);
        $q2->options()->create(['sort_order' => 1, 'label' => 'B', 'body' => '4', 'is_correct' => false]);

        $exam = DiagnosticExam::factory()->create([
            'subject_id' => $subject->id,
            'mode' => DiagnosticExamMode::Fixed,
            'prevent_retake_after_completion' => false,
        ]);

        $exam->questions()->attach([
            $q1->id => ['sort_order' => 1, 'points' => 1],
            $q2->id => ['sort_order' => 2, 'points' => 1],
        ]);

        return $exam->fresh();
    }

    public function test_student_can_view_diagnostic_index_and_learning_path(): void
    {
        $user = $this->studentWithProfile();

        $this->actingAs($user)
            ->get(route('student.diagnostic.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Student/Diagnostic/Index'));

        $this->actingAs($user)
            ->get(route('student.learning-path.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Student/LearningPath'));
    }

    public function test_student_completes_fixed_diagnostic_and_updates_profile(): void
    {
        $user = $this->studentWithProfile();
        $student = Student::query()->where('user_id', $user->id)->firstOrFail();
        $exam = $this->createFixedExamWithTwoMcQuestions();

        $ordered = $exam->questions()->orderByPivot('sort_order')->get();
        $q1 = $ordered[0];
        $correctOpt1 = $q1->options()->where('is_correct', true)->firstOrFail();

        $this->actingAs($user)
            ->get(route('student.diagnostic.show', $exam))
            ->assertOk();

        $this->actingAs($user)
            ->post(route('student.diagnostic.start', $exam))
            ->assertRedirect();

        $attempt = $student->diagnosticAttempts()->firstOrFail();

        $this->actingAs($user)
            ->post(route('student.diagnostic.answer', $attempt), ['answer' => $correctOpt1->id])
            ->assertRedirect();

        $attempt->refresh();
        $this->assertNotNull($attempt->answers);

        $q2 = $ordered[1];
        $wrongOpt2 = $q2->options()->where('is_correct', false)->firstOrFail();

        $this->actingAs($user)
            ->post(route('student.diagnostic.answer', $attempt->fresh()), ['answer' => $wrongOpt2->id])
            ->assertRedirect();

        $attempt->refresh();
        $this->assertSame('completed', $attempt->status->value);
        $this->assertNotNull($attempt->classified_level);
        $this->assertEquals(50.0, (float) $attempt->score_percent);

        $student->refresh();
        $this->assertNotNull($student->adaptiveProfile);
        $this->assertGreaterThan(0, $student->learningRecommendations()->count());
    }

    public function test_teacher_adaptive_pages_respond(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Docente->value]);

        $this->actingAs($user)
            ->get(route('teacher.adaptive-learning.index'))
            ->assertRedirect(route('teacher.pedagogical-panel.index'));

        $this->actingAs($user)
            ->get(route('teacher.diagnostic-results.index'))
            ->assertRedirect(route('teacher.diagnostics.index'));

        $this->actingAs($user)
            ->get(route('teacher.pedagogical-panel.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Teacher/PedagogicalPanel'));

        $this->actingAs($user)
            ->get(route('teacher.diagnostics.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Teacher/Diagnostics/Index'));
    }

    public function test_admin_can_view_adaptive_analytics(): void
    {
        $admin = User::factory()->create();
        $admin->syncRoles([IntranetRole::Administrador->value]);

        $this->actingAs($admin)
            ->get(route('intranet.adaptive-analytics.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Intranet/AdaptiveAnalytics/Index'));
    }

    public function test_secretaria_can_view_intranet_adaptive_diagnostic_exams_index(): void
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Secretaria->value]);

        $this->actingAs($user)
            ->get(route('intranet.adaptive.diagnostic-exams.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Intranet/Adaptive/DiagnosticExams/Index'));
    }

    public function test_teacher_cannot_store_diagnostic_for_unassigned_section(): void
    {
        $year = AcademicYear::factory()->active()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $sectionA = Section::factory()->create([
            'grade_id' => $grade->id,
            'code' => 'A-DIAG-T',
            'name' => 'Sección A Diag',
        ]);
        $sectionB = Section::factory()->create([
            'grade_id' => $grade->id,
            'code' => 'B-DIAG-T',
            'name' => 'Sección B Diag',
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
            'is_tutor' => false,
            'is_active' => true,
        ]);

        $this->actingAs($teacher)
            ->post(route('teacher.diagnostics.store'), [
                'title' => 'Diagnóstico fuera de alcance',
                'description' => null,
                'subject_id' => $subject->id,
                'academic_year_id' => $year->id,
                'educational_level_id' => $level->id,
                'grade_id' => $grade->id,
                'section_id' => $sectionB->id,
                'mode' => DiagnosticExamMode::Fixed->value,
                'is_active' => true,
                'prevent_retake_after_completion' => false,
                'adaptive_question_count' => 6,
                'threshold_basic_percent' => 40,
                'threshold_intermediate_percent' => 70,
            ])
            ->assertForbidden();
    }

    public function test_student_cannot_view_diagnostic_outside_enrollment_section(): void
    {
        $year = AcademicYear::factory()->active()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $sectionA = Section::factory()->create([
            'grade_id' => $grade->id,
            'code' => 'A-SCOPE',
            'name' => 'Sección A Scope',
        ]);
        $sectionB = Section::factory()->create([
            'grade_id' => $grade->id,
            'code' => 'B-SCOPE',
            'name' => 'Sección B Scope',
        ]);
        $subject = Subject::factory()->create();

        $user = $this->studentWithProfile();
        $student = Student::query()->where('user_id', $user->id)->firstOrFail();

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $sectionA->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $exam = DiagnosticExam::factory()->create([
            'subject_id' => $subject->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $sectionB->id,
            'mode' => DiagnosticExamMode::Fixed,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->get(route('student.diagnostic.show', $exam))
            ->assertForbidden();
    }
}

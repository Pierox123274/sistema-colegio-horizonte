<?php

namespace Tests\Feature\Intranet;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use App\Models\Product;
use App\Models\Section;
use App\Models\Student;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_admin_can_view_intranet_analytics(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        Product::factory()->create([
            'code' => 'UNI-001',
            'name' => 'Polo institucional',
            'current_stock' => 2,
            'minimum_stock' => 5,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('intranet.analytics.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Analytics/Index')
                ->has('academic.summary')
                ->has('financial.summary')
                ->has('inventory.summary')
                ->has('inventory.low_stock', 1)
                ->where('inventory.low_stock.0.code', 'UNI-001')
                ->has('users'));
    }

    public function test_secretaria_sees_academic_and_financial_without_inventory(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($secretaria)
            ->get(route('intranet.analytics.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Intranet/Analytics/Index')
                ->has('academic')
                ->has('financial')
                ->where('permissions.inventory', false)
                ->missing('inventory')
                ->missing('users'));
    }

    public function test_teacher_can_view_own_analytics(): void
    {
        $teacher = $this->userWithRole(IntranetRole::Docente);
        $year = AcademicYear::factory()->create(['is_active' => true]);
        $section = Section::factory()->create();
        $student = Student::factory()->create();

        TeacherAssignment::factory()->create([
            'user_id' => $teacher->id,
            'academic_year_id' => $year->id,
            'section_id' => $section->id,
            'is_active' => true,
        ]);

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $evaluation = Evaluation::factory()->create([
            'academic_year_id' => $year->id,
            'section_id' => $section->id,
        ]);

        GradeRecord::factory()->create([
            'student_id' => $student->id,
            'evaluation_id' => $evaluation->id,
            'score' => 8,
        ]);

        Attendance::factory()->create([
            'student_id' => $student->id,
            'section_id' => $section->id,
            'status' => AttendanceStatus::Presente,
            'attendance_date' => now()->toDateString(),
        ]);

        $this->actingAs($teacher)
            ->get(route('teacher.analytics.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Teacher/Analytics/Index')
                ->where('has_assignments', true)
                ->has('academic.summary')
                ->has('academic.attendance_distribution', 1)
                ->where('academic.attendance_distribution.0.label', 'Presente'));
    }

    public function test_student_cannot_access_analytics(): void
    {
        $student = $this->userWithRole(IntranetRole::Estudiante);

        $this->actingAs($student)
            ->get(route('intranet.analytics.index'))
            ->assertForbidden();

        $this->actingAs($student)
            ->get(route('teacher.analytics.index'))
            ->assertForbidden();
    }

    public function test_export_pdf_responds_for_academic_report(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)
            ->get(route('intranet.reports.analytics.export.pdf', 'academic'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_csv_responds_for_grades_report(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($admin)
            ->get(route('intranet.reports.analytics.export.csv', 'grades'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
    }

    public function test_metrics_and_rankings_are_calculated(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $year = AcademicYear::factory()->create(['is_active' => true]);
        $section = Section::factory()->create();
        $studentA = Student::factory()->create(['first_name' => 'Ana', 'last_name' => 'Alta']);
        $studentB = Student::factory()->create(['first_name' => 'Ben', 'last_name' => 'Bajo']);

        Enrollment::factory()->create([
            'student_id' => $studentA->id,
            'academic_year_id' => $year->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);
        Enrollment::factory()->create([
            'student_id' => $studentB->id,
            'academic_year_id' => $year->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $evaluation = Evaluation::factory()->create([
            'academic_year_id' => $year->id,
            'section_id' => $section->id,
        ]);

        GradeRecord::factory()->create([
            'student_id' => $studentA->id,
            'evaluation_id' => $evaluation->id,
            'score' => 18,
        ]);
        GradeRecord::factory()->create([
            'student_id' => $studentB->id,
            'evaluation_id' => $evaluation->id,
            'score' => 9,
        ]);

        $this->actingAs($admin)
            ->get(route('intranet.analytics.index', ['academic_year_id' => $year->id]))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('academic.summary.total_students', 2)
                ->where('academic.summary.institutional_average', 13.5)
                ->has('academic.top_students', 2)
                ->has('academic.risk_students', 1));
    }
}

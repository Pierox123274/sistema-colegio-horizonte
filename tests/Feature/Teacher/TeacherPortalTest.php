<?php

namespace Tests\Feature\Teacher;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Guardian;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TeacherPortalTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_docente_entra_al_dashboard(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Dashboard'));
    }

    public function test_administrador_puede_entrar_al_portal_docente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Dashboard'));
    }

    public function test_secretaria_no_entra_al_portal_docente(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($secretaria)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_estudiante_no_entra_al_portal_docente(): void
    {
        $estudiante = $this->userWithRole(IntranetRole::Estudiante);

        $this->actingAs($estudiante)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_apoderado_no_entra_al_portal_docente(): void
    {
        $apoderado = $this->userWithRole(IntranetRole::Apoderado);

        $this->actingAs($apoderado)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_docente_ve_asistencia(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.attendance.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Attendance/Index'));
    }

    public function test_docente_ve_notas(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.grades.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Grades/Index'));
    }

    public function test_docente_ve_estudiantes(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.students.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Students/Index'));
    }

    public function test_docente_ve_reportes(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.reports.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Reports/Index'));
    }

    public function test_docente_portal_no_expone_textos_erp(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertDontSee('ERP', false)
            ->assertDontSee('Panel ERP', false);

        $this->actingAs($docente)
            ->get(route('teacher.attendance.index'))
            ->assertOk()
            ->assertDontSee('ERP', false);

        $this->actingAs($docente)
            ->get(route('teacher.grades.index'))
            ->assertOk()
            ->assertDontSee('ERP', false);

        $this->actingAs($docente)
            ->get(route('teacher.students.index'))
            ->assertOk()
            ->assertDontSee('ERP', false)
            ->assertDontSee('Vista ERP', false);

        $this->actingAs($docente)
            ->get(route('teacher.reports.index'))
            ->assertOk()
            ->assertDontSee('ERP', false);

        $this->actingAs($docente)
            ->get(route('teacher.assignments.index'))
            ->assertOk()
            ->assertDontSee('ERP', false);
    }

    public function test_docente_puede_abrir_registro_asistencia_y_notas(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.attendance.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Attendance/Register'));

        $this->actingAs($docente)
            ->get(route('teacher.grades.records'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Teacher/Grades/Records'));
    }

    public function test_docente_ve_pagina_mis_asignaciones(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.assignments.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Teacher/Assignments/Index')
                ->has('overview')
                ->has('overview.summary')
                ->has('active_tab')
                ->has('empty_message'));
    }

    public function test_docente_sin_asignaciones_ve_mensaje_vacio(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('teacher.assignments.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('has_teaching_assignments', false)
                ->where('empty_message', fn ($msg) => str_contains($msg, 'administrador académico')));
    }

    public function test_administrador_ve_estadisticas_globales_en_dashboard(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $year = AcademicYear::factory()->active()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);

        Enrollment::factory()->count(2)->create([
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $this->actingAs($admin)
            ->get(route('teacher.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Teacher/Dashboard')
                ->where('teacher_portal_scoped', false)
                ->where('stats.enrolled_students', 2)
                ->has('stats.subjects_count'));
    }

    public function test_docente_ve_detalle_estudiante_y_grupos_por_seccion(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $year = AcademicYear::factory()->active()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id, 'code' => 'A']);
        $docente = $this->userWithRole(IntranetRole::Docente);

        $student = Student::factory()->create(['first_name' => 'Pedro', 'last_name' => 'Ríos']);
        $guardian = Guardian::factory()->create(['first_name' => 'Ana', 'last_name' => 'Ríos']);

        Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $student->guardians()->attach($guardian->id, [
            'relationship' => 'Madre',
            'is_primary' => true,
            'is_financial_responsible' => true,
            'emergency_priority' => 1,
        ]);

        $this->actingAs($admin)->post(route('intranet.admin.teacher-assignments.store'), [
            'user_id' => $docente->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'subject_id' => null,
            'is_tutor' => true,
            'is_active' => true,
        ])->assertRedirect();

        $this->actingAs($docente)
            ->get(route('teacher.students.index', ['section_id' => $section->id, 'search' => 'Pedro']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Teacher/Students/Index')
                ->where('teacher_portal_scoped', true)
                ->where('has_teaching_assignments', true)
                ->has('grouped_students'));

        $this->actingAs($docente)
            ->get(route('teacher.students.show', $student))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Teacher/Students/Show')
                ->where('student.id', $student->id)
                ->has('guardian_links', 1));
    }
}

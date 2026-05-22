<?php

namespace Tests\Feature\Student;

use App\Enums\IntranetRole;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\GradeRecord;
use App\Models\Payment;
use App\Models\Pension;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StudentPortalTest extends TestCase
{
    use RefreshDatabase;

    private function estudianteConFicha(): User
    {
        $user = User::factory()->create();
        $user->syncRoles([IntranetRole::Estudiante->value]);

        $student = Student::factory()->create(['user_id' => $user->id]);

        GradeRecord::factory()->create(['student_id' => $student->id]);

        return $user;
    }

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_estudiante_entra_al_dashboard(): void
    {
        $estudiante = $this->estudianteConFicha();

        $this->actingAs($estudiante)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Student/Dashboard'));
    }

    public function test_estudiante_ve_solo_sus_notas(): void
    {
        $estudiante = $this->estudianteConFicha();
        $student = Student::query()->where('user_id', $estudiante->id)->firstOrFail();

        $otro = Student::factory()->create();
        GradeRecord::factory()->create(['student_id' => $otro->id]);

        $this->actingAs($estudiante)
            ->get(route('student.grades.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Student/Grades/Index')
                ->has('history.data', 1));
    }

    public function test_estudiante_login_redirige_al_portal_estudiante(): void
    {
        $estudiante = $this->estudianteConFicha();

        $response = $this->post('/login', [
            'email' => $estudiante->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('student.dashboard', absolute: false));
    }

    public function test_estudiante_sin_admin_es_redirigido_desde_intranet_dashboard(): void
    {
        $estudiante = $this->estudianteConFicha();

        $this->actingAs($estudiante)
            ->get('/intranet/dashboard')
            ->assertRedirect(route('student.dashboard', absolute: false));
    }

    public function test_estudiante_no_entra_al_portal_docente(): void
    {
        $estudiante = $this->estudianteConFicha();

        $this->actingAs($estudiante)
            ->get(route('teacher.dashboard'))
            ->assertForbidden();
    }

    public function test_docente_no_entra_al_portal_estudiante(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }

    public function test_apoderado_no_entra_al_portal_estudiante(): void
    {
        $apoderado = $this->userWithRole(IntranetRole::Apoderado);

        $this->actingAs($apoderado)
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }

    public function test_secretaria_no_entra_al_portal_estudiante(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($secretaria)
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }

    public function test_administrador_puede_entrar_al_portal_estudiante(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)
            ->get(route('student.dashboard'))
            ->assertOk();
    }

    public function test_estudiante_sin_ficha_vinculada_no_accede_al_portal(): void
    {
        $estudiante = $this->userWithRole(IntranetRole::Estudiante);

        $this->actingAs($estudiante)
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }

    public function test_estudiante_asistencia_no_falla_con_metricas_vacias(): void
    {
        $estudiante = $this->estudianteConFicha();

        $this->actingAs($estudiante)
            ->get(route('student.attendance.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Student/Attendance/Index')
                ->where('metrics.total', 0)
                ->where('metrics.present_count', 0));
    }

    public function test_estudiante_ve_solo_su_asistencia(): void
    {
        $estudiante = $this->estudianteConFicha();
        $student = Student::query()->where('user_id', $estudiante->id)->firstOrFail();

        $base = Attendance::factory()->create();
        Attendance::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $base->academic_year_id,
            'educational_level_id' => $base->educational_level_id,
            'grade_id' => $base->grade_id,
            'section_id' => $base->section_id,
        ]);

        $otro = Student::factory()->create();
        Attendance::factory()->create([
            'student_id' => $otro->id,
            'academic_year_id' => $base->academic_year_id,
            'educational_level_id' => $base->educational_level_id,
            'grade_id' => $base->grade_id,
            'section_id' => $base->section_id,
        ]);

        $this->actingAs($estudiante)
            ->get(route('student.attendance.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Student/Attendance/Index')
                ->has('history.data', 1)
                ->where('metrics.total', 1));
    }

    public function test_estudiante_puede_filtrar_asistencia_por_estado(): void
    {
        $estudiante = $this->estudianteConFicha();
        $student = Student::query()->where('user_id', $estudiante->id)->firstOrFail();
        $base = Attendance::factory()->create([
            'student_id' => $student->id,
            'status' => 'presente',
        ]);

        Attendance::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $base->academic_year_id,
            'educational_level_id' => $base->educational_level_id,
            'grade_id' => $base->grade_id,
            'section_id' => $base->section_id,
            'attendance_date' => $base->attendance_date->copy()->subDay()->toDateString(),
            'status' => 'falta',
        ]);

        $this->actingAs($estudiante)
            ->get(route('student.attendance.index', ['status' => 'falta']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('history.data', 1)
                ->where('filters.status', 'falta'));
    }

    public function test_estudiante_ve_solo_sus_pagos(): void
    {
        $estudiante = $this->estudianteConFicha();
        $student = Student::query()->where('user_id', $estudiante->id)->firstOrFail();
        $otro = Student::factory()->create();

        $enrollment = Enrollment::factory()->create(['student_id' => $student->id]);
        Pension::factory()->create(['enrollment_id' => $enrollment->id]);
        Pension::factory()->create([
            'enrollment_id' => Enrollment::factory()->create(['student_id' => $otro->id])->id,
        ]);

        Payment::factory()->create(['student_id' => $student->id]);
        Payment::factory()->create(['student_id' => $otro->id]);

        $this->actingAs($estudiante)
            ->get(route('student.payments.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Student/Payments/Index')
                ->has('payments.data', 1)
                ->where('summary.total_count', 1));
    }

    public function test_portal_estudiante_no_expone_textos_erp(): void
    {
        $estudiante = $this->estudianteConFicha();

        $this->actingAs($estudiante)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertDontSee('ERP', false)
            ->assertDontSee('Panel ERP', false);
    }
}

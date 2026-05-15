<?php

namespace Tests\Feature\Intranet;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminUserAndTeacherAssignmentTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    public function test_administrador_crea_usuario_docente(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)->post(route('intranet.admin.users.store'), [
            'name' => 'Docente Nuevo',
            'email' => 'docente.nuevo@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => IntranetRole::Docente->value,
            'is_active' => true,
        ])->assertRedirect(route('intranet.admin.users.index'));

        $created = User::query()->where('email', 'docente.nuevo@test.com')->first();
        $this->assertNotNull($created);
        $this->assertTrue($created->hasRole(IntranetRole::Docente->value));
    }

    public function test_administrador_crea_secretaria(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $this->actingAs($admin)->post(route('intranet.admin.users.store'), [
            'name' => 'Secretaria Nueva',
            'email' => 'secretaria.nueva@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => IntranetRole::Secretaria->value,
            'is_active' => true,
        ])->assertRedirect(route('intranet.admin.users.index'));

        $this->assertTrue(
            User::query()->where('email', 'secretaria.nueva@test.com')->first()?->hasRole(IntranetRole::Secretaria->value) ?? false
        );
    }

    public function test_secretaria_no_accede_a_usuarios_ni_asignaciones(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($secretaria)->get(route('intranet.admin.users.index'))->assertForbidden();
        $this->actingAs($secretaria)->get(route('intranet.admin.teacher-assignments.index'))->assertForbidden();
    }

    public function test_admin_asigna_docente_a_seccion_y_docente_ve_solo_esos_estudiantes(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);
        $year = AcademicYear::factory()->active()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $sectionA = Section::factory()->create(['grade_id' => $grade->id, 'code' => 'A']);
        $sectionB = Section::factory()->create(['grade_id' => $grade->id, 'code' => 'B']);

        $docente = $this->userWithRole(IntranetRole::Docente);

        $studentIn = Student::factory()->create(['first_name' => 'EnSeccionA']);
        $studentOut = Student::factory()->create(['first_name' => 'EnSeccionB']);

        Enrollment::factory()->create([
            'student_id' => $studentIn->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $sectionA->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        Enrollment::factory()->create([
            'student_id' => $studentOut->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $sectionB->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        $this->actingAs($admin)->post(route('intranet.admin.teacher-assignments.store'), [
            'user_id' => $docente->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $sectionA->id,
            'subject_id' => null,
            'is_tutor' => true,
            'is_active' => true,
        ])->assertRedirect(route('intranet.admin.teacher-assignments.index'));

        $this->assertDatabaseHas('teacher_assignments', [
            'user_id' => $docente->id,
            'section_id' => $sectionA->id,
        ]);

        $this->actingAs($docente)->get(route('teacher.students.index'))->assertOk()->assertInertia(
            fn (Assert $page) => $page
                ->component('Teacher/Students/Index')
                ->has('students.data', 1)
                ->where('students.data.0.first_name', 'EnSeccionA')
        );

        $this->actingAs($docente)->get(route('intranet.students.show', $studentIn))->assertOk();
        $this->actingAs($docente)->get(route('intranet.students.show', $studentOut))->assertForbidden();
    }

    public function test_docente_sin_asignaciones_ve_portal_estudiantes_vacio(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);
        AcademicYear::factory()->active()->create();

        $this->actingAs($docente)->get(route('teacher.students.index'))->assertOk()->assertInertia(
            fn (Assert $page) => $page
                ->component('Teacher/Students/Index')
                ->where('has_teaching_assignments', false)
                ->where('teacher_portal_scoped', true)
        );
    }

    public function test_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        $user = User::factory()->create([
            'email' => 'inactivo@test.com',
            'is_active' => false,
        ]);
        $user->syncRoles([IntranetRole::Docente->value]);

        $this->post('/login', [
            'email' => 'inactivo@test.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');
    }
}

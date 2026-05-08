<?php

namespace Tests\Feature\Intranet;

use App\Enums\EnrollmentStatus;
use App\Enums\GuardianRelationshipType;
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

class EnrollmentManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    /**
     * @return array<string, mixed>
     */
    private function validEnrollmentPayload(
        Student $student,
        AcademicYear $year,
        EducationalLevel $level,
        Grade $grade,
        Section $section,
        ?int $guardianId = null,
        ?string $code = null,
        string $status = 'matriculado',
    ): array {
        return [
            'enrollment_code' => $code ?? 'MAT-TST-'.$year->year.'-UNIT01',
            'student_id' => $student->id,
            'guardian_id' => $guardianId,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'classroom_id' => null,
            'enrollment_date' => now()->toDateString(),
            'amount' => '150.50',
            'status' => $status,
            'observations' => 'Prueba',
        ];
    }

    public function test_administrador_puede_crear_matricula(): void
    {
        $level = EducationalLevel::factory()->create(['is_active' => true]);
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $year = AcademicYear::factory()->create(['year' => 2031]);
        $student = Student::factory()->create();

        $admin = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($admin)->post(
            route('intranet.enrollments.store'),
            $this->validEnrollmentPayload($student, $year, $level, $grade, $section, code: 'MAT-2031-ADM99'),
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'enrollment_code' => 'MAT-2031-ADM99',
            'student_id' => $student->id,
        ]);
    }

    public function test_secretaria_puede_crear_matricula(): void
    {
        $level = EducationalLevel::factory()->create(['is_active' => true]);
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $year = AcademicYear::factory()->create(['year' => 2032]);
        $student = Student::factory()->create();

        $user = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($user)->post(
            route('intranet.enrollments.store'),
            $this->validEnrollmentPayload($student, $year, $level, $grade, $section, code: 'MAT-2032-SEC01'),
        );

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', ['enrollment_code' => 'MAT-2032-SEC01']);
    }

    public function test_docente_puede_listar_y_ver_detalle_pero_no_crear_ni_editar(): void
    {
        $level = EducationalLevel::factory()->create(['is_active' => true]);
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $year = AcademicYear::factory()->create(['year' => 2033]);
        $student = Student::factory()->create();

        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
        ]);

        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('intranet.enrollments.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Intranet/Enrollments/Index'));

        $this->actingAs($docente)
            ->get(route('intranet.enrollments.show', $enrollment))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Intranet/Enrollments/Show'));

        $this->actingAs($docente)
            ->get(route('intranet.enrollments.create'))
            ->assertForbidden();

        $this->actingAs($docente)
            ->post(
                route('intranet.enrollments.store'),
                $this->validEnrollmentPayload(
                    Student::factory()->create(),
                    AcademicYear::factory()->create(['year' => 2044]),
                    $level,
                    $grade,
                    $section,
                    code: 'MAT-2044-DOC-DENY',
                ),
            )
            ->assertForbidden();

        $this->actingAs($docente)
            ->get(route('intranet.enrollments.edit', $enrollment))
            ->assertForbidden();
    }

    public function test_estudiante_no_accede_a_matriculas(): void
    {
        $user = $this->userWithRole(IntranetRole::Estudiante);

        $this->actingAs($user)
            ->get(route('intranet.enrollments.index'))
            ->assertForbidden();
    }

    public function test_apoderado_no_accede_a_matriculas(): void
    {
        $user = $this->userWithRole(IntranetRole::Apoderado);

        $this->actingAs($user)
            ->get(route('intranet.enrollments.index'))
            ->assertForbidden();
    }

    public function test_no_permite_doble_matricula_activa_mismo_ano(): void
    {
        $level = EducationalLevel::factory()->create(['is_active' => true]);
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $year = AcademicYear::factory()->create(['year' => 2034]);
        $student = Student::factory()->create();

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($secretaria)->post(
            route('intranet.enrollments.store'),
            $this->validEnrollmentPayload($student, $year, $level, $grade, $section, code: 'MAT-2034-A', status: 'pendiente'),
        )->assertRedirect();

        $response = $this->actingAs($secretaria)->post(
            route('intranet.enrollments.store'),
            $this->validEnrollmentPayload($student, $year, $level, $grade, $section, code: 'MAT-2034-B', status: 'matriculado'),
        );

        $response->assertSessionHasErrors(['student_id']);
    }

    public function test_permite_segunda_matricula_si_primera_no_bloquea(): void
    {
        $level = EducationalLevel::factory()->create(['is_active' => true]);
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $year = AcademicYear::factory()->create(['year' => 2035]);
        $student = Student::factory()->create();

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $this->actingAs($secretaria)->post(
            route('intranet.enrollments.store'),
            $this->validEnrollmentPayload($student, $year, $level, $grade, $section, code: 'MAT-2035-X', status: 'anulado'),
        )->assertRedirect();

        $this->actingAs($secretaria)->post(
            route('intranet.enrollments.store'),
            $this->validEnrollmentPayload($student, $year, $level, $grade, $section, code: 'MAT-2035-Y', status: 'pendiente'),
        )->assertRedirect();

        $this->assertEquals(2, Enrollment::query()->where('student_id', $student->id)->count());
    }

    public function test_no_permite_apoderado_no_vinculado(): void
    {
        $level = EducationalLevel::factory()->create(['is_active' => true]);
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $year = AcademicYear::factory()->create(['year' => 2036]);
        $student = Student::factory()->create();
        $otherGuardian = Guardian::factory()->create();

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $payload = $this->validEnrollmentPayload($student, $year, $level, $grade, $section, code: 'MAT-2036-G');
        $payload['guardian_id'] = $otherGuardian->id;

        $response = $this->actingAs($secretaria)->post(
            route('intranet.enrollments.store'),
            $payload,
        );

        $response->assertSessionHasErrors(['guardian_id']);
    }

    public function test_validacion_seccion_de_otro_grado(): void
    {
        $level = EducationalLevel::factory()->create(['is_active' => true]);
        $gradeA = Grade::factory()->create(['educational_level_id' => $level->id]);
        $gradeB = Grade::factory()->create(['educational_level_id' => $level->id]);
        $sectionForA = Section::factory()->create(['grade_id' => $gradeA->id]);
        $year = AcademicYear::factory()->create(['year' => 2037]);
        $student = Student::factory()->create();

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $payload = $this->validEnrollmentPayload($student, $year, $level, $gradeB, $sectionForA, code: 'MAT-2037-BADSEC');

        $response = $this->actingAs($secretaria)->post(route('intranet.enrollments.store'), $payload);

        $response->assertSessionHasErrors(['section_id']);
    }

    public function test_validaciones_basicas_requeridas(): void
    {
        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($secretaria)->post(route('intranet.enrollments.store'), []);

        $response->assertSessionHasErrors([
            'student_id',
            'academic_year_id',
            'educational_level_id',
            'grade_id',
            'section_id',
            'enrollment_date',
            'amount',
            'status',
        ]);
    }

    public function test_secretaria_puede_buscar_estudiantes_para_matricula(): void
    {
        $student = Student::factory()->create([
            'code' => 'SRCH-100',
            'first_name' => 'Luisa',
            'last_name' => 'Ramos',
            'document_number' => '44443333',
        ]);

        $user = $this->userWithRole(IntranetRole::Secretaria);

        $res = $this->actingAs($user)->getJson(
            route('intranet.enrollments.students.search', ['q' => 'SR']),
        );

        $res->assertOk();
        $res->assertJsonPath('students.0.id', $student->id);
        $res->assertJsonPath('students.0.code', 'SRCH-100');
    }

    public function test_busqueda_con_menos_de_dos_caracteres_devuelve_vacio(): void
    {
        Student::factory()->create(['code' => 'ABC-1']);

        $user = $this->userWithRole(IntranetRole::Secretaria);

        $res = $this->actingAs($user)->getJson(
            route('intranet.enrollments.students.search', ['q' => 'A']),
        );

        $res->assertOk();
        $res->assertJsonCount(0, 'students');
    }

    public function test_docente_no_puede_buscar_estudiantes_para_matricula(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->getJson(route('intranet.enrollments.students.search', ['q' => 'ab']))
            ->assertForbidden();
    }

    public function test_preview_estudiante_incluye_apoderados(): void
    {
        $student = Student::factory()->create();
        $guardian = Guardian::factory()->create(['first_name' => 'María', 'last_name' => 'López']);
        $student->guardians()->attach($guardian->id, [
            'relationship' => GuardianRelationshipType::Madre->value,
            'is_primary' => true,
            'is_financial_responsible' => true,
            'emergency_priority' => 1,
            'observations' => null,
        ]);

        $admin = $this->userWithRole(IntranetRole::Administrador);

        $res = $this->actingAs($admin)->getJson(
            route('intranet.enrollments.students.preview', $student),
        );

        $res->assertOk();
        $res->assertJsonPath('preview.id', $student->id);
        $res->assertJsonCount(1, 'preview.guardians');
        $res->assertJsonPath('preview.guardians.0.value', (string) $guardian->id);
    }

    public function test_puede_actualizar_matricula(): void
    {
        $level = EducationalLevel::factory()->create(['is_active' => true]);
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $year = AcademicYear::factory()->create(['year' => 2038]);
        $student = Student::factory()->create();

        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'enrollment_code' => 'MAT-2038-EDIT',
            'status' => EnrollmentStatus::Pendiente->value,
            'amount' => '10.00',
        ]);

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $payload = $this->validEnrollmentPayload($student, $year, $level, $grade, $section, code: 'MAT-2038-EDIT');
        $payload['status'] = EnrollmentStatus::Matriculado->value;
        $payload['amount'] = '200.00';

        $response = $this->actingAs($secretaria)->put(
            route('intranet.enrollments.update', $enrollment),
            $payload,
        );

        $response->assertRedirect(route('intranet.enrollments.show', $enrollment, absolute: false));

        $enrollment->refresh();
        $this->assertSame(EnrollmentStatus::Matriculado, $enrollment->status);
        $this->assertSame('200.00', (string) $enrollment->amount);
    }
}

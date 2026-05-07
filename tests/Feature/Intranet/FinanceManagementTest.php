<?php

namespace Tests\Feature\Intranet;

use App\Enums\IntranetRole;
use App\Enums\PaymentConceptType;
use App\Enums\PaymentEntryStatus;
use App\Enums\PensionStatus;
use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\PaymentConcept;
use App\Models\Pension;
use App\Models\Section;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceManagementTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(IntranetRole $role): User
    {
        $user = User::factory()->create();
        $user->syncRoles([$role->value]);

        return $user;
    }

    /**
     * @return array{0: Enrollment, 1: Student, 2: PaymentConcept}
     */
    private function enrollmentWithConcept(): array
    {
        $level = EducationalLevel::factory()->create(['is_active' => true]);
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $year = AcademicYear::factory()->create(['year' => 2099]);
        $student = Student::factory()->create();

        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
        ]);

        $concept = PaymentConcept::factory()->create([
            'code' => 'CON-FIN-'.uniqid(),
            'type' => PaymentConceptType::Pension,
            'default_amount' => 350,
            'is_active' => true,
        ]);

        return [$enrollment, $student, $concept];
    }

    public function test_administrador_crea_concepto_de_pago(): void
    {
        $admin = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($admin)->post(route('intranet.payment-concepts.store'), [
            'code' => 'CON-ADM-UNIT',
            'name' => 'Concepto prueba',
            'description' => null,
            'default_amount' => '120.00',
            'type' => PaymentConceptType::Pension->value,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payment_concepts', ['code' => 'CON-ADM-UNIT']);
    }

    public function test_no_permite_concepto_duplicado(): void
    {
        PaymentConcept::factory()->create(['code' => 'CON-DUP']);

        $admin = $this->userWithRole(IntranetRole::Administrador);

        $response = $this->actingAs($admin)->post(route('intranet.payment-concepts.store'), [
            'code' => 'CON-DUP',
            'name' => 'Otro nombre',
            'description' => null,
            'default_amount' => '10.00',
            'type' => PaymentConceptType::Otro->value,
            'is_active' => true,
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    public function test_secretaria_crea_pension(): void
    {
        [$enrollment, , $concept] = $this->enrollmentWithConcept();

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($secretaria)->post(route('intranet.pensions.store'), [
            'enrollment_id' => $enrollment->id,
            'payment_concept_id' => $concept->id,
            'month' => 7,
            'year' => 2026,
            'amount' => '100.00',
            'due_date' => '2026-07-10',
            'status' => PensionStatus::Pendiente->value,
            'observations' => null,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('pensions', [
            'enrollment_id' => $enrollment->id,
            'month' => 7,
            'year' => 2026,
        ]);
    }

    public function test_no_permite_doble_pension_mismo_mes_anio(): void
    {
        [$enrollment, , $concept] = $this->enrollmentWithConcept();

        Pension::factory()->create([
            'enrollment_id' => $enrollment->id,
            'payment_concept_id' => $concept->id,
            'month' => 8,
            'year' => 2026,
            'amount' => 80,
            'due_date' => '2026-08-15',
            'status' => PensionStatus::Pendiente->value,
        ]);

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($secretaria)->post(route('intranet.pensions.store'), [
            'enrollment_id' => $enrollment->id,
            'payment_concept_id' => $concept->id,
            'month' => 8,
            'year' => 2026,
            'amount' => '90.00',
            'due_date' => '2026-08-20',
            'status' => PensionStatus::Pendiente->value,
            'observations' => null,
        ]);

        $response->assertSessionHasErrors(['month']);
    }

    public function test_secretaria_registra_pago_y_actualiza_estado_de_pension(): void
    {
        [$enrollment, $student, $concept] = $this->enrollmentWithConcept();

        $pension = Pension::factory()->create([
            'enrollment_id' => $enrollment->id,
            'payment_concept_id' => $concept->id,
            'month' => 9,
            'year' => 2026,
            'amount' => 100,
            'due_date' => '2026-09-10',
            'status' => PensionStatus::Pendiente->value,
        ]);

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($secretaria)->post(route('intranet.payments.store'), [
            'student_id' => $student->id,
            'guardian_id' => null,
            'enrollment_id' => $enrollment->id,
            'pension_id' => $pension->id,
            'payment_concept_id' => $concept->id,
            'amount' => '100.00',
            'payment_method' => 'efectivo',
            'paid_at' => '2026-09-05 10:00:00',
            'observations' => null,
        ]);

        $response->assertRedirect();

        $pension->refresh();
        $this->assertSame(PensionStatus::Pagado, $pension->status);

        $this->assertDatabaseHas('payments', [
            'student_id' => $student->id,
            'pension_id' => $pension->id,
            'status' => PaymentEntryStatus::Registrado->value,
        ]);
    }

    public function test_no_permite_pago_superior_al_pendiente(): void
    {
        [$enrollment, $student, $concept] = $this->enrollmentWithConcept();

        $pension = Pension::factory()->create([
            'enrollment_id' => $enrollment->id,
            'payment_concept_id' => $concept->id,
            'month' => 10,
            'year' => 2026,
            'amount' => 100,
            'due_date' => '2026-10-10',
            'status' => PensionStatus::Pendiente->value,
        ]);

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($secretaria)->post(route('intranet.payments.store'), [
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'pension_id' => $pension->id,
            'payment_concept_id' => $concept->id,
            'amount' => '120.00',
            'payment_method' => 'efectivo',
            'paid_at' => '2026-10-05 10:00:00',
        ]);

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_validacion_monto_pago_positivo(): void
    {
        [, $student, $concept] = $this->enrollmentWithConcept();

        $secretaria = $this->userWithRole(IntranetRole::Secretaria);

        $response = $this->actingAs($secretaria)->post(route('intranet.payments.store'), [
            'student_id' => $student->id,
            'payment_concept_id' => $concept->id,
            'amount' => '0',
            'payment_method' => 'efectivo',
            'paid_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertSessionHasErrors(['amount']);
    }

    public function test_docente_no_accede_a_finanzas(): void
    {
        $docente = $this->userWithRole(IntranetRole::Docente);

        $this->actingAs($docente)
            ->get(route('intranet.payment-concepts.index'))
            ->assertForbidden();

        $this->actingAs($docente)
            ->get(route('intranet.pensions.index'))
            ->assertForbidden();

        $this->actingAs($docente)
            ->get(route('intranet.payments.index'))
            ->assertForbidden();
    }

    public function test_estudiante_no_accede_finanzas(): void
    {
        $user = $this->userWithRole(IntranetRole::Estudiante);

        $this->actingAs($user)
            ->get(route('intranet.payments.index'))
            ->assertForbidden();
    }

    public function test_apoderado_no_accede_finanzas(): void
    {
        $user = $this->userWithRole(IntranetRole::Apoderado);

        $this->actingAs($user)
            ->get(route('intranet.payments.index'))
            ->assertForbidden();
    }
}

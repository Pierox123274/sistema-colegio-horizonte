<?php

namespace Tests\Unit\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\PaymentEntryStatus;
use App\Enums\PensionStatus;
use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Pension;
use App\Models\Student;
use App\Services\FinancialAnalyticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialAnalyticsServiceTest extends TestCase
{
    use RefreshDatabase;

    private FinancialAnalyticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FinancialAnalyticsService::class);
    }

    public function test_summary_calculates_income_and_morosity(): void
    {
        $year = AcademicYear::factory()->create();
        $student = Student::factory()->create();
        $enrollment = Enrollment::factory()->create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        Payment::factory()->create([
            'student_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'amount' => 150.50,
            'paid_at' => now(),
            'status' => PaymentEntryStatus::Registrado->value,
        ]);

        Pension::factory()->create([
            'enrollment_id' => $enrollment->id,
            'status' => PensionStatus::Vencido->value,
        ]);
        Pension::factory()->create([
            'enrollment_id' => $enrollment->id,
            'status' => PensionStatus::Pagado->value,
        ]);

        $summary = $this->service->summary(['academic_year_id' => $year->id]);

        $this->assertSame(150.50, $summary['total_income']);
        $this->assertSame(1, $summary['overdue_pensions']);
        $this->assertSame(1, $summary['paid_pensions']);
        $this->assertSame(50.0, $summary['morosity_rate']);
    }

    public function test_income_trend_groups_payments_by_day(): void
    {
        $student = Student::factory()->create();
        Payment::factory()->create([
            'student_id' => $student->id,
            'amount' => 80,
            'paid_at' => now()->subDay(),
            'status' => PaymentEntryStatus::Registrado->value,
        ]);
        Payment::factory()->create([
            'student_id' => $student->id,
            'amount' => 20,
            'paid_at' => now()->subDay(),
            'status' => PaymentEntryStatus::Registrado->value,
        ]);

        $trend = $this->service->incomeTrend([
            'date_from' => now()->subDays(7)->toDateString(),
        ]);

        $this->assertNotEmpty($trend);
        $this->assertSame(100.0, $trend[0]['value']);
    }

    public function test_recent_payments_returns_formatted_rows(): void
    {
        $student = Student::factory()->create([
            'first_name' => 'Luis',
            'last_name' => 'García',
        ]);
        Payment::factory()->create([
            'student_id' => $student->id,
            'amount' => 99.99,
            'paid_at' => now(),
            'status' => PaymentEntryStatus::Registrado->value,
        ]);

        $rows = $this->service->recentPayments(['date_from' => now()->subDay()->toDateString()]);

        $this->assertCount(1, $rows);
        $this->assertStringContainsString('García', $rows[0]['student']);
        $this->assertSame(99.99, $rows[0]['amount']);
    }

    public function test_pension_status_distribution_labels_statuses(): void
    {
        $enrollment = Enrollment::factory()->create();
        Pension::factory()->create([
            'enrollment_id' => $enrollment->id,
            'status' => PensionStatus::Pendiente->value,
        ]);
        Pension::factory()->create([
            'enrollment_id' => $enrollment->id,
            'status' => PensionStatus::Pagado->value,
        ]);

        $distribution = $this->service->pensionStatusDistribution([]);

        $this->assertCount(2, $distribution);
        $this->assertSame(2, collect($distribution)->sum('value'));
        $this->assertContains('Pendiente', collect($distribution)->pluck('label')->all());
        $this->assertContains('Pagado', collect($distribution)->pluck('label')->all());
    }

    public function test_payments_query_uses_default_date_window_when_missing(): void
    {
        $student = Student::factory()->create();
        Payment::factory()->create([
            'student_id' => $student->id,
            'amount' => 10,
            'paid_at' => now()->subDays(10),
            'status' => PaymentEntryStatus::Registrado->value,
        ]);
        Payment::factory()->create([
            'student_id' => $student->id,
            'amount' => 10,
            'paid_at' => now()->subDays(40),
            'status' => PaymentEntryStatus::Registrado->value,
        ]);

        $count = $this->service->paymentsQuery([])->count();

        $this->assertSame(1, $count);
    }
}

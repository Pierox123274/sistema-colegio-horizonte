<?php

namespace App\Services;

use App\Enums\PaymentEntryStatus;
use App\Enums\PensionStatus;
use App\Models\Payment;
use App\Models\Pension;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private readonly PensionService $pensionService
    ) {}

    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Payment::query()
            ->with([
                'student:id,code,first_name,last_name',
                'guardian:id,first_name,last_name',
                'paymentConcept:id,code,name',
                'enrollment:id,enrollment_code',
                'pension:id,month,year',
            ])
            ->orderByDesc('paid_at')
            ->orderByDesc('id');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('payment_code', 'like', $like)
                    ->orWhereHas('student', function ($s) use ($like): void {
                        $s->where('first_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like)
                            ->orWhere('code', 'like', $like);
                    });
            });
        }

        if ($request->query('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->query('payment_method')) {
            $query->where('payment_method', $request->query('payment_method'));
        }

        if ($request->query('month')) {
            $query->whereMonth('paid_at', (int) $request->query('month'));
        }

        if ($request->query('year')) {
            $query->whereYear('paid_at', (int) $request->query('year'));
        }

        if ($request->query('paid_from')) {
            $query->whereDate('paid_at', '>=', $request->query('paid_from'));
        }

        if ($request->query('paid_to')) {
            $query->whereDate('paid_at', '<=', $request->query('paid_to'));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Total pagos registrados para una pensión (excluye anulados).
     */
    public function registeredTotalForPension(Pension $pension): string
    {
        return (string) $pension->payments()
            ->where('status', PaymentEntryStatus::Registrado)
            ->sum('amount');
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Payment
    {
        return DB::transaction(function () use ($data): Payment {
            if (empty($data['payment_code'])) {
                $data['payment_code'] = $this->generateUniquePaymentCode();
            }

            $pension = null;
            if (! empty($data['pension_id'])) {
                $pension = Pension::query()->findOrFail((int) $data['pension_id']);
                $this->assertCanPayPension($pension, (float) $data['amount']);
            }

            /** @var Payment $payment */
            $payment = Payment::query()->create($data);

            if ($pension !== null) {
                $this->pensionService->refreshStatus($pension);
            }

            return $payment->fresh(['student', 'guardian', 'enrollment', 'pension', 'paymentConcept', 'createdByUser']);
        });
    }

    public function cancel(Payment $payment): Payment
    {
        return DB::transaction(function () use ($payment): Payment {
            if ($payment->status === PaymentEntryStatus::Anulado) {
                return $payment;
            }

            $payment->update(['status' => PaymentEntryStatus::Anulado]);

            if ($payment->pension_id !== null) {
                $pension = Pension::query()->find($payment->pension_id);
                if ($pension !== null) {
                    $this->pensionService->refreshStatus($pension);
                }
            }

            return $payment->fresh();
        });
    }

    /**
     * Resumen para UI de registro de pago (deuda pendiente por pensión).
     *
     * @return array{pensions: list<array<string, mixed>>, payments_recent: list<Payment>}
     */
    public function studentFinancialSummary(int $studentId): array
    {
        $student = Student::query()
            ->with([
                'enrollments' => fn ($q) => $q->orderByDesc('id'),
                'enrollments.pensions' => fn ($q) => $q
                    ->with('paymentConcept:id,code,name')
                    ->orderByDesc('year')
                    ->orderByDesc('month'),
            ])
            ->findOrFail($studentId);

        $pensionRows = [];
        foreach ($student->enrollments as $enrollment) {
            foreach ($enrollment->pensions as $pension) {
                if ($pension->status === PensionStatus::Anulado) {
                    continue;
                }
                $pending = $this->pensionService->pendingAmount($pension);
                $pensionRows[] = [
                    'id' => $pension->id,
                    'payment_concept_id' => $pension->payment_concept_id,
                    'enrollment_id' => $enrollment->id,
                    'enrollment_code' => $enrollment->enrollment_code,
                    'month' => $pension->month,
                    'year' => $pension->year,
                    'due_date' => $pension->due_date->format('Y-m-d'),
                    'amount' => (string) $pension->amount,
                    'pending' => $pending,
                    'status' => $pension->status->value,
                    'concept' => $pension->paymentConcept?->name,
                ];
            }
        }

        $recent = Payment::query()
            ->where('student_id', $studentId)
            ->with('paymentConcept:id,name')
            ->orderByDesc('paid_at')
            ->limit(15)
            ->get();

        return [
            'pensions' => $pensionRows,
            'payments_recent' => $recent->all(),
        ];
    }

    private function assertCanPayPension(Pension $pension, float $paymentAmount): void
    {
        if ($pension->status === PensionStatus::Anulado) {
            throw ValidationException::withMessages([
                'pension_id' => ['La pensión está anulada.'],
            ]);
        }

        $pending = (float) $this->pensionService->pendingAmount($pension);
        if ($paymentAmount - $pending > 0.009) {
            throw ValidationException::withMessages([
                'amount' => ['El monto excede el saldo pendiente de la pensión (S/ '.number_format($pending, 2).').'],
            ]);
        }
    }

    private function generateUniquePaymentCode(): string
    {
        $year = now()->year;
        do {
            $code = 'PAY-'.$year.'-'.strtoupper(Str::random(6));
        } while (Payment::query()->where('payment_code', $code)->exists());

        return $code;
    }
}

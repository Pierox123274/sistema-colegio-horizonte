<?php

namespace App\Services;

use App\Enums\PaymentEntryStatus;
use App\Enums\PensionStatus;
use App\Models\Payment;
use App\Models\Pension;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

final class FinancialAnalyticsService
{
    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function summary(array $filters): array
    {
        $paymentsQuery = $this->paymentsQuery($filters);
        $totalIncome = (float) (clone $paymentsQuery)->sum('amount');

        $pensionsQuery = $this->pensionsQuery($filters);
        $pending = (clone $pensionsQuery)
            ->whereIn('status', [
                PensionStatus::Pendiente->value,
                PensionStatus::Parcial->value,
                PensionStatus::Vencido->value,
            ])
            ->count();

        $paid = (clone $pensionsQuery)->where('status', PensionStatus::Pagado->value)->count();
        $overdue = (clone $pensionsQuery)->where('status', PensionStatus::Vencido->value)->count();
        $totalPensions = (clone $pensionsQuery)
            ->where('status', '!=', PensionStatus::Anulado->value)
            ->count();

        return [
            'total_income' => round($totalIncome, 2),
            'pending_pensions' => $pending,
            'paid_pensions' => $paid,
            'overdue_pensions' => $overdue,
            'morosity_rate' => $totalPensions > 0
                ? round(($overdue / $totalPensions) * 100, 1)
                : 0,
        ];
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return list<array{label: string, value: float}>
     */
    public function incomeTrend(array $filters): array
    {
        return $this->paymentsQuery($filters)
            ->select(DB::raw('DATE(paid_at) as day'), DB::raw('SUM(amount) as total'))
            ->groupBy('day')
            ->orderBy('day')
            ->limit(14)
            ->get()
            ->map(fn ($row): array => [
                'label' => (string) $row->day,
                'value' => round((float) $row->total, 2),
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return list<array<string, mixed>>
     */
    public function recentPayments(array $filters, int $limit = 8): array
    {
        return $this->paymentsQuery($filters)
            ->with(['student:id,code,first_name,last_name', 'paymentConcept:id,name'])
            ->orderByDesc('paid_at')
            ->limit($limit)
            ->get()
            ->map(fn (Payment $p): array => [
                'code' => $p->payment_code,
                'student' => trim(($p->student?->last_name ?? '').', '.($p->student?->first_name ?? '')),
                'concept' => $p->paymentConcept?->name,
                'amount' => round((float) $p->amount, 2),
                'paid_at' => $p->paid_at?->translatedFormat('d/m/Y H:i'),
            ])
            ->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return list<array{label: string, value: int}>
     */
    public function pensionStatusDistribution(array $filters): array
    {
        $rows = $this->pensionsQuery($filters)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->where('status', '!=', PensionStatus::Anulado->value)
            ->groupBy('status')
            ->get();

        $labels = collect(PensionStatus::options())->keyBy('value');

        return $rows->map(function ($row) use ($labels): array {
            $statusValue = $row->status instanceof PensionStatus
                ? $row->status->value
                : (string) $row->status;

            return [
                'label' => ($labels->get($statusValue) ?? [])['label'] ?? $statusValue,
                'value' => (int) $row->total,
            ];
        })->all();
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Payment>
     */
    public function paymentsQuery(array $filters): Builder
    {
        $query = Payment::query()->where('status', PaymentEntryStatus::Registrado->value);

        $query->whereDate(
            'paid_at',
            '>=',
            $filters['date_from'] ?? now()->subDays(30)->toDateString(),
        );

        if (! empty($filters['date_to'])) {
            $query->whereDate('paid_at', '<=', $filters['date_to']);
        }

        if (! empty($filters['academic_year_id'])) {
            $query->whereHas('enrollment', fn (Builder $q) => $q->where('academic_year_id', (int) $filters['academic_year_id']));
        }

        return $query;
    }

    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<Pension>
     */
    private function pensionsQuery(array $filters): Builder
    {
        $query = Pension::query();

        if (! empty($filters['academic_year_id'])) {
            $query->whereHas('enrollment', fn (Builder $q) => $q->where('academic_year_id', (int) $filters['academic_year_id']));
        }

        return $query;
    }
}

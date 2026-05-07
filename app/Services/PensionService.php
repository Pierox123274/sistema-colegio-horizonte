<?php

namespace App\Services;

use App\Enums\PaymentEntryStatus;
use App\Enums\PensionStatus;
use App\Models\Pension;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PensionService
{
    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Pension::query()
            ->with([
                'enrollment.student:id,code,first_name,last_name',
                'paymentConcept:id,code,name',
            ])
            ->orderByDesc('year')
            ->orderByDesc('month');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->whereHas('enrollment.student', function ($q) use ($like): void {
                $q->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('code', 'like', $like)
                    ->orWhere('document_number', 'like', $like);
            });
        }

        if ($request->query('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->query('month')) {
            $query->where('month', $request->query('month'));
        }

        if ($request->query('year')) {
            $query->where('year', $request->query('year'));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Pension
    {
        return Pension::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Pension $pension, array $data): Pension
    {
        $pension->update($data);

        return $pension->fresh();
    }

    /**
     * Importe pendiente de una pensión (registrado vs anulado).
     */
    public function pendingAmount(Pension $pension): string
    {
        $pension->refresh();
        $paid = (float) $pension->payments()
            ->where('status', PaymentEntryStatus::Registrado)
            ->sum('amount');

        return number_format(max(0, (float) $pension->amount - $paid), 2, '.', '');
    }

    /**
     * Recalcula estado según pagos registrados y fecha de vencimiento.
     */
    public function refreshStatus(Pension $pension): void
    {
        $pension->refresh();

        if ($pension->status === PensionStatus::Anulado) {
            return;
        }

        $paid = (float) $pension->payments()
            ->where('status', PaymentEntryStatus::Registrado)
            ->sum('amount');

        $amount = (float) $pension->amount;
        $today = now()->startOfDay();
        $due = $pension->due_date->copy()->startOfDay();

        if ($amount <= 0) {
            $next = PensionStatus::Pagado;
        } elseif ($paid >= $amount) {
            $next = PensionStatus::Pagado;
        } elseif ($paid > 0) {
            $next = PensionStatus::Parcial;
        } elseif ($due->lt($today)) {
            $next = PensionStatus::Vencido;
        } else {
            $next = PensionStatus::Pendiente;
        }

        if ($pension->status !== $next) {
            $pension->forceFill(['status' => $next])->save();
        }
    }
}

<?php

namespace App\Services;

use App\Models\CashMovement;
use App\Models\CashRegister;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashRegisterService
{
    public function paginateForIndex(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        return CashRegister::query()
            ->with(['user:id,name', 'closedByUser:id,name'])
            ->withCount(['sales', 'movements'])
            ->orderByDesc('business_date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function openForUser(int $userId, float $openingBalance, ?string $notes = null): CashRegister
    {
        return DB::transaction(function () use ($userId, $openingBalance, $notes): CashRegister {
            $today = now()->toDateString();
            $alreadyOpen = CashRegister::query()
                ->where('user_id', $userId)
                ->whereDate('business_date', $today)
                ->where('status', 'abierta')
                ->exists();

            if ($alreadyOpen) {
                throw ValidationException::withMessages([
                    'cash_register' => ['Ya existe una caja abierta para el usuario en la fecha actual.'],
                ]);
            }

            $cashRegister = CashRegister::query()->create([
                'user_id' => $userId,
                'business_date' => $today,
                'status' => 'abierta',
                'opening_balance' => $openingBalance,
                'opened_at' => now(),
                'opening_notes' => $notes,
            ]);

            CashMovement::query()->create([
                'cash_register_id' => $cashRegister->id,
                'type' => 'apertura',
                'amount' => $openingBalance,
                'description' => 'Apertura de caja',
                'moved_at' => now(),
                'created_by_user_id' => $userId,
            ]);

            return $cashRegister->fresh(['user']);
        });
    }

    public function close(CashRegister $cashRegister, int $closedByUserId, ?string $notes = null): CashRegister
    {
        return DB::transaction(function () use ($cashRegister, $closedByUserId, $notes): CashRegister {
            /** @var CashRegister $locked */
            $locked = CashRegister::query()->lockForUpdate()->findOrFail($cashRegister->id);
            if ($locked->status === 'cerrada') {
                return $locked;
            }

            $closingBalance = (float) CashMovement::query()
                ->where('cash_register_id', $locked->id)
                ->sum('amount');

            $locked->update([
                'status' => 'cerrada',
                'closed_at' => now(),
                'closing_balance' => $closingBalance,
                'closed_by_user_id' => $closedByUserId,
                'closing_notes' => $notes,
            ]);

            CashMovement::query()->create([
                'cash_register_id' => $locked->id,
                'type' => 'cierre',
                'amount' => 0,
                'description' => 'Cierre de caja',
                'moved_at' => now(),
                'created_by_user_id' => $closedByUserId,
            ]);

            return $locked->fresh(['user', 'closedByUser']);
        });
    }

    public function currentOpenForUser(int $userId): ?CashRegister
    {
        return CashRegister::query()
            ->where('user_id', $userId)
            ->where('status', 'abierta')
            ->latest('opened_at')
            ->first();
    }

    /**
     * @return array{open: int, closed: int, total_sales: string, net_cash: string}
     */
    public function stats(): array
    {
        return [
            'open' => CashRegister::query()->where('status', 'abierta')->count(),
            'closed' => CashRegister::query()->where('status', 'cerrada')->count(),
            'total_sales' => number_format((float) CashMovement::query()->where('type', 'venta')->sum('amount'), 2, '.', ''),
            'net_cash' => number_format((float) CashMovement::query()->sum('amount'), 2, '.', ''),
        ];
    }
}

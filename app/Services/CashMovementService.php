<?php

namespace App\Services;

use App\Models\CashMovement;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class CashMovementService
{
    public function paginateForIndex(Request $request, int $perPage = 25): LengthAwarePaginator
    {
        $query = CashMovement::query()
            ->with([
                'cashRegister:id,business_date,user_id',
                'cashRegister.user:id,name',
                'sale:id,sale_code,status',
                'createdByUser:id,name',
            ])
            ->orderByDesc('moved_at')
            ->orderByDesc('id');

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($cashRegisterId = $request->query('cash_register_id')) {
            $query->where('cash_register_id', (int) $cashRegisterId);
        }

        return $query->paginate($perPage)->withQueryString();
    }
}

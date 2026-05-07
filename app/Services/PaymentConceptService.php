<?php

namespace App\Services;

use App\Models\PaymentConcept;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class PaymentConceptService
{
    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = PaymentConcept::query()->orderBy('code');

        if ($request->query('search')) {
            $term = '%'.trim((string) $request->query('search')).'%';
            $query->where(function ($q) use ($term): void {
                $q->where('code', 'like', $term)
                    ->orWhere('name', 'like', $term);
            });
        }

        if ($request->query('type')) {
            $query->where('type', $request->query('type'));
        }

        if ($request->query('is_active') !== null && $request->query('is_active') !== '') {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): PaymentConcept
    {
        return PaymentConcept::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(PaymentConcept $concept, array $data): PaymentConcept
    {
        $concept->update($data);

        return $concept->fresh();
    }

    public function deleteIfUnused(PaymentConcept $concept): bool
    {
        if ($concept->pensions()->exists() || $concept->payments()->exists()) {
            return false;
        }
        $concept->delete();

        return true;
    }
}

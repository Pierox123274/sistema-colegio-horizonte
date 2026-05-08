<?php

namespace App\Services;

use App\Models\Sale;
use Carbon\CarbonInterface;

class SaleReceiptService
{
    /**
     * @return array<string, mixed>
     */
    public function buildReceiptData(Sale $sale): array
    {
        $sale->loadMissing([
            'student',
            'guardian',
            'items.product',
            'createdByUser:id,name',
        ]);

        $soldAt = $sale->sold_at;
        if (! $soldAt instanceof CarbonInterface) {
            $soldAt = now();
        }

        return [
            'sale' => $sale,
            'receipt_number' => 'BOL-'.$soldAt->format('Ymd').'-'.str_pad((string) $sale->id, 6, '0', STR_PAD_LEFT),
            'sold_at_human' => $soldAt->format('d/m/Y H:i:s'),
            'student_name' => $sale->student ? trim($sale->student->first_name.' '.$sale->student->last_name) : null,
            'guardian_name' => $sale->guardian ? trim($sale->guardian->first_name.' '.$sale->guardian->last_name) : null,
            'registered_by' => $sale->createdByUser?->name ?? 'Usuario no identificado',
            'total' => number_format((float) $sale->total, 2, '.', ''),
            'institution' => [
                'name' => (string) config('institution.name', 'I.E.P. Horizonte'),
                'identifier' => (string) config('institution.identifier', 'RUC DEMO 20123456789'),
                'address' => (string) config('institution.address', 'Av. Demo 123, Lima'),
                'message' => (string) config('institution.receipt_message', 'Gracias por su compra.'),
            ],
        ];
    }
}

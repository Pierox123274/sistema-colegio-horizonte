<?php

namespace App\Http\Controllers;

use App\Models\CashMovement;
use App\Services\CashMovementService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CashMovementController extends Controller
{
    public function __construct(
        private readonly CashMovementService $cashMovementService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', CashMovement::class);

        return Inertia::render('Intranet/Sales/CashMovements/Index', [
            'movements' => $this->cashMovementService->paginateForIndex($request),
            'filters' => [
                'type' => $request->query('type', ''),
                'cash_register_id' => $request->query('cash_register_id', ''),
            ],
            'catalog' => [
                'types' => [
                    ['value' => 'apertura', 'label' => 'Apertura'],
                    ['value' => 'venta', 'label' => 'Venta'],
                    ['value' => 'anulacion_venta', 'label' => 'Anulación venta'],
                    ['value' => 'cierre', 'label' => 'Cierre'],
                ],
            ],
        ]);
    }
}

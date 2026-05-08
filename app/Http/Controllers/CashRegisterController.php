<?php

namespace App\Http\Controllers;

use App\Http\Requests\Intranet\CloseCashRegisterRequest;
use App\Http\Requests\Intranet\OpenCashRegisterRequest;
use App\Models\CashRegister;
use App\Services\CashRegisterService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CashRegisterController extends Controller
{
    public function __construct(
        private readonly CashRegisterService $cashRegisterService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', CashRegister::class);

        return Inertia::render('Intranet/Sales/CashRegisters/Index', [
            'cash_registers' => $this->cashRegisterService->paginateForIndex($request),
            'stats' => $this->cashRegisterService->stats(),
            'current_open' => $request->user() ? $this->cashRegisterService->currentOpenForUser($request->user()->id) : null,
        ]);
    }

    public function open(OpenCashRegisterRequest $request): RedirectResponse
    {
        $this->cashRegisterService->openForUser(
            (int) $request->user()->id,
            (float) $request->validated('opening_balance'),
            $request->validated('opening_notes')
        );

        return redirect()->route('intranet.sales.cash-registers.index')->with('success', 'Caja abierta correctamente.');
    }

    public function close(CloseCashRegisterRequest $request, CashRegister $cashRegister): RedirectResponse
    {
        $this->cashRegisterService->close(
            $cashRegister,
            (int) $request->user()->id,
            $request->validated('closing_notes')
        );

        return redirect()->route('intranet.sales.cash-registers.index')->with('success', 'Caja cerrada correctamente.');
    }
}

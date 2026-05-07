<?php

namespace App\Http\Controllers;

use App\Enums\PaymentConceptType;
use App\Http\Requests\Intranet\StorePaymentConceptRequest;
use App\Http\Requests\Intranet\UpdatePaymentConceptRequest;
use App\Models\PaymentConcept;
use App\Services\PaymentConceptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentConceptController extends Controller
{
    public function __construct(
        private readonly PaymentConceptService $paymentConceptService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', PaymentConcept::class);

        return Inertia::render('Intranet/PaymentConcepts/Index', [
            'concepts' => $this->paymentConceptService->paginateForIndex($request),
            'filters' => [
                'search' => $request->query('search', ''),
                'type' => $request->query('type', ''),
                'is_active' => $request->query('is_active', ''),
            ],
            'catalog' => [
                'types' => PaymentConceptType::options(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', PaymentConcept::class);

        return Inertia::render('Intranet/PaymentConcepts/Create', [
            'catalog' => [
                'types' => PaymentConceptType::options(),
            ],
        ]);
    }

    public function store(StorePaymentConceptRequest $request): RedirectResponse
    {
        $this->paymentConceptService->create($request->validated());

        return redirect()
            ->route('intranet.payment-concepts.index')
            ->with('success', 'Concepto de pago registrado.');
    }

    public function show(Request $request, PaymentConcept $paymentConcept): Response
    {
        $this->authorize('view', $paymentConcept);

        $paymentConcept->loadCount(['pensions', 'payments']);

        return Inertia::render('Intranet/PaymentConcepts/Show', [
            'concept' => $paymentConcept,
        ]);
    }

    public function edit(Request $request, PaymentConcept $paymentConcept): Response
    {
        $this->authorize('update', $paymentConcept);

        return Inertia::render('Intranet/PaymentConcepts/Edit', [
            'concept' => $paymentConcept,
            'catalog' => [
                'types' => PaymentConceptType::options(),
            ],
        ]);
    }

    public function update(UpdatePaymentConceptRequest $request, PaymentConcept $paymentConcept): RedirectResponse
    {
        $this->paymentConceptService->update($paymentConcept, $request->validated());

        return redirect()
            ->route('intranet.payment-concepts.show', $paymentConcept)
            ->with('success', 'Concepto actualizado.');
    }

    public function destroy(Request $request, PaymentConcept $paymentConcept): RedirectResponse
    {
        $this->authorize('delete', $paymentConcept);

        if (! $this->paymentConceptService->deleteIfUnused($paymentConcept)) {
            return redirect()
                ->back()
                ->with('error', 'No se puede eliminar: hay pensiones o pagos asociados.');
        }

        return redirect()
            ->route('intranet.payment-concepts.index')
            ->with('success', 'Concepto eliminado.');
    }
}

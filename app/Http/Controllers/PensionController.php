<?php

namespace App\Http\Controllers;

use App\Enums\PensionStatus;
use App\Http\Requests\Intranet\StorePensionRequest;
use App\Http\Requests\Intranet\UpdatePensionRequest;
use App\Models\Enrollment;
use App\Models\PaymentConcept;
use App\Models\Pension;
use App\Services\PensionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PensionController extends Controller
{
    public function __construct(
        private readonly PensionService $pensionService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Pension::class);

        return Inertia::render('Intranet/Pensions/Index', [
            'pensions' => $this->pensionService->paginateForIndex($request),
            'filters' => [
                'search' => $request->query('search', ''),
                'status' => $request->query('status', ''),
                'month' => $request->query('month', ''),
                'year' => $request->query('year', ''),
            ],
            'catalog' => [
                'statuses' => PensionStatus::options(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Pension::class);

        $enrollmentOptions = Enrollment::query()
            ->with(['student:id,code,first_name,last_name'])
            ->orderByDesc('id')
            ->limit(150)
            ->get()
            ->map(fn (Enrollment $e): array => [
                'value' => (string) $e->id,
                'label' => $e->enrollment_code.' — '.$e->student?->fullName(),
            ])
            ->values()
            ->all();

        $conceptOptions = PaymentConcept::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'default_amount'])
            ->map(fn (PaymentConcept $c): array => [
                'value' => (string) $c->id,
                'label' => $c->code.' — '.$c->name,
                'default_amount' => (string) $c->default_amount,
            ])
            ->values()
            ->all();

        return Inertia::render('Intranet/Pensions/Create', [
            'catalog' => [
                'enrollments' => $enrollmentOptions,
                'concepts' => $conceptOptions,
                'statuses' => PensionStatus::options(),
            ],
        ]);
    }

    public function store(StorePensionRequest $request): RedirectResponse
    {
        $pension = $this->pensionService->create($request->validated());

        return redirect()
            ->route('intranet.pensions.show', $pension)
            ->with('success', 'Pensión registrada.');
    }

    public function show(Request $request, Pension $pension): Response
    {
        $this->authorize('view', $pension);

        $pension->load([
            'enrollment.student:id,code,first_name,last_name',
            'enrollment.academicYear:id,name,year',
            'paymentConcept',
            'payments' => fn ($q) => $q->orderByDesc('paid_at'),
        ]);

        return Inertia::render('Intranet/Pensions/Show', [
            'pension' => $pension,
            'pending_amount' => $this->pensionService->pendingAmount($pension),
        ]);
    }

    public function edit(Request $request, Pension $pension): Response
    {
        $this->authorize('update', $pension);

        $enrollmentOptions = Enrollment::query()
            ->with(['student:id,code,first_name,last_name'])
            ->orderByDesc('id')
            ->limit(150)
            ->get()
            ->map(fn (Enrollment $e): array => [
                'value' => (string) $e->id,
                'label' => $e->enrollment_code.' — '.$e->student?->fullName(),
            ])
            ->values()
            ->all();

        $conceptOptions = PaymentConcept::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name', 'default_amount'])
            ->map(fn (PaymentConcept $c): array => [
                'value' => (string) $c->id,
                'label' => $c->code.' — '.$c->name,
                'default_amount' => (string) $c->default_amount,
            ])
            ->values()
            ->all();

        return Inertia::render('Intranet/Pensions/Edit', [
            'pension' => $pension,
            'catalog' => [
                'enrollments' => $enrollmentOptions,
                'concepts' => $conceptOptions,
                'statuses' => PensionStatus::options(),
            ],
        ]);
    }

    public function update(UpdatePensionRequest $request, Pension $pension): RedirectResponse
    {
        $this->pensionService->update($pension, $request->validated());
        $this->pensionService->refreshStatus($pension->fresh());

        return redirect()
            ->route('intranet.pensions.show', $pension)
            ->with('success', 'Pensión actualizada.');
    }
}

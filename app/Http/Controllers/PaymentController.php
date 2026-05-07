<?php

namespace App\Http\Controllers;

use App\Enums\PaymentEntryStatus;
use App\Enums\PaymentMethod;
use App\Http\Requests\Intranet\StorePaymentRequest;
use App\Models\Payment;
use App\Models\PaymentConcept;
use App\Models\Student;
use App\Services\EnrollmentService;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly EnrollmentService $enrollmentService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Payment::class);

        return Inertia::render('Intranet/Payments/Index', [
            'payments' => $this->paymentService->paginateForIndex($request),
            'filters' => [
                'search' => $request->query('search', ''),
                'status' => $request->query('status', ''),
                'payment_method' => $request->query('payment_method', ''),
                'month' => $request->query('month', ''),
                'year' => $request->query('year', ''),
                'paid_from' => $request->query('paid_from', ''),
                'paid_to' => $request->query('paid_to', ''),
            ],
            'catalog' => [
                'methods' => PaymentMethod::options(),
                'statuses' => PaymentEntryStatus::options(),
            ],
        ]);
    }

    public function searchStudents(Request $request): JsonResponse
    {
        $this->authorize('create', Payment::class);

        $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $q = trim((string) $request->query('q', ''));

        return response()->json([
            'students' => $this->enrollmentService->searchStudentsForEnrollment($q),
        ]);
    }

    public function studentSummary(Student $student): JsonResponse
    {
        $this->authorize('create', Payment::class);

        return response()->json($this->paymentService->studentFinancialSummary($student->id));
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Payment::class);

        $concepts = PaymentConcept::query()
            ->where('is_active', true)
            ->orderBy('code')
            ->get(['id', 'code', 'name'])
            ->map(fn (PaymentConcept $c): array => [
                'value' => (string) $c->id,
                'label' => $c->code.' — '.$c->name,
            ])
            ->values()
            ->all();

        return Inertia::render('Intranet/Payments/Create', [
            'catalog' => [
                'concepts' => $concepts,
                'methods' => PaymentMethod::options(),
                'statuses' => PaymentEntryStatus::options(),
            ],
        ]);
    }

    public function store(StorePaymentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if (! isset($data['status'])) {
            $data['status'] = PaymentEntryStatus::Registrado->value;
        }

        $payment = $this->paymentService->create($data);

        return redirect()
            ->route('intranet.payments.show', $payment)
            ->with('success', 'Pago registrado.');
    }

    public function show(Request $request, Payment $payment): Response
    {
        $this->authorize('view', $payment);

        $payment->load([
            'student',
            'guardian',
            'enrollment',
            'pension.paymentConcept',
            'paymentConcept',
        ]);

        return Inertia::render('Intranet/Payments/Show', [
            'payment' => $payment,
        ]);
    }

    public function cancel(Request $request, Payment $payment): RedirectResponse
    {
        $this->authorize('cancel', $payment);

        $this->paymentService->cancel($payment);

        return redirect()
            ->route('intranet.payments.show', $payment)
            ->with('success', 'Pago anulado.');
    }
}

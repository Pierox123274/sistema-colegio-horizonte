<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use App\Services\StudentContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentPaymentsController extends Controller
{
    public function __construct(
        private readonly StudentContextService $studentContext,
        private readonly PaymentService $paymentService
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $context = $this->studentContext->portalContext($user);
        $student = $context['student'];

        $paymentsPayload = [
            'summary' => [
                'total_count' => 0,
                'paid_count' => 0,
                'pending_count' => 0,
                'pending_amount' => 0,
                'pending_amount_label' => 'S/ 0.00',
            ],
            'pensions' => [],
            'payments' => null,
            'has_pending_pensions' => false,
        ];

        if ($student !== null) {
            $paymentsPayload = $this->studentContext->paymentsPortalFor($student, $this->paymentService);
        }

        return Inertia::render('Student/Payments/Index', [
            'student' => $student !== null ? $this->studentContext->studentCard($student) : null,
            'summary' => $paymentsPayload['summary'],
            'pensions' => $paymentsPayload['pensions'],
            'payments' => $paymentsPayload['payments'],
            'has_pending_pensions' => $paymentsPayload['has_pending_pensions'],
            'has_student' => $context['has_student'],
            'portal_scoped' => $context['portal_scoped'],
            'empty_message' => $context['empty_message'],
        ]);
    }
}

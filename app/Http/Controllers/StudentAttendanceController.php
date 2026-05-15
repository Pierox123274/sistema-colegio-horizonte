<?php

namespace App\Http\Controllers;

use App\Services\StudentContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentAttendanceController extends Controller
{
    public function __construct(
        private readonly StudentContextService $studentContext
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $context = $this->studentContext->portalContext($user);
        $student = $context['student'];

        $attendancePayload = [
            'history' => null,
            'metrics' => $this->studentContext->attendanceMetrics(collect()),
            'filters' => [
                'date_from' => '',
                'date_to' => '',
                'status' => '',
                'section_id' => '',
            ],
            'catalog' => [
                'statuses' => [],
                'sections' => [],
            ],
        ];

        if ($student !== null) {
            $attendancePayload = $this->studentContext->attendancePortalFor($student, $request);
        }

        return Inertia::render('Student/Attendance/Index', [
            'student' => $student !== null ? $this->studentContext->studentCard($student) : null,
            'history' => $attendancePayload['history'],
            'metrics' => $attendancePayload['metrics'],
            'filters' => $attendancePayload['filters'],
            'catalog' => $attendancePayload['catalog'],
            'has_student' => $context['has_student'],
            'portal_scoped' => $context['portal_scoped'],
            'empty_message' => $context['empty_message'],
        ]);
    }
}

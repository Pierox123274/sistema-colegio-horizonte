<?php

namespace App\Http\Controllers;

use App\Services\GamificationService;
use App\Services\LMSDashboardService;
use App\Services\StudentContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentDashboardController extends Controller
{
    public function __construct(
        private readonly StudentContextService $studentContext
    ) {}

    public function index(
        Request $request,
        LMSDashboardService $lmsDashboard,
        GamificationService $gamification
    ): Response {
        $user = $request->user();
        abort_if($user === null, 403);

        $context = $this->studentContext->portalContext($user);
        $student = $context['student'];
        $lms = $student !== null ? $lmsDashboard->studentSummary($student) : [];
        $gamificationSummary = $student !== null ? $gamification->studentSummary($student) : null;
        $academicYear = $this->studentContext->activeAcademicYear();

        $stats = [
            'grade_records_count' => 0,
            'attendance_records_count' => 0,
            'payments_count' => 0,
            'recent_grades' => [],
        ];
        $enrollment = null;
        $studentCard = null;
        $academicHistory = [];

        if ($student !== null) {
            $stats = $this->studentContext->dashboardStats($student);
            $enrollment = $this->studentContext->currentEnrollmentPayload($student);
            $studentCard = $this->studentContext->studentCard($student);
            $academicHistory = $this->studentContext->academicHistoryEnrollments($student);
        }

        return Inertia::render('Student/Dashboard', [
            'academic_year' => $academicYear?->only(['id', 'name', 'year', 'is_active']),
            'lms' => $lms,
            'gamification' => $gamificationSummary,
            'student' => $studentCard,
            'enrollment' => $enrollment,
            'stats' => $stats,
            'academic_history' => $academicHistory,
            'has_student' => $context['has_student'],
            'portal_scoped' => $context['portal_scoped'],
            'empty_message' => $context['empty_message'],
            'links' => $this->studentContext->portalLinks(),
        ]);
    }
}

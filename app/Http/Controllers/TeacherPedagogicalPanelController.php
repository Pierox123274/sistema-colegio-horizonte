<?php

namespace App\Http\Controllers;

use App\Services\AdaptiveAnalyticsService;
use App\Support\AIDashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherPedagogicalPanelController extends Controller
{
    public function index(Request $request, AdaptiveAnalyticsService $analytics): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        return Inertia::render('Teacher/PedagogicalPanel', [
            'low_students' => $analytics->teacherLowLevelStudents($user),
            'weak_topics' => $analytics->teacherWeakTopics($user),
            'students_without_diagnostic' => $analytics->teacherStudentsWithoutDiagnosticCount($user),
            'ai_insights_href' => $user->can('useTeacherInsights', AIDashboard::class)
                ? route('teacher.ai-insights.index', absolute: false)
                : null,
            'analytics_href' => route('teacher.analytics.index', absolute: false),
            'diagnostics_href' => route('teacher.diagnostics.index', absolute: false),
            'academic_risk_href' => route('teacher.academic-risk.index', absolute: false),
        ]);
    }
}

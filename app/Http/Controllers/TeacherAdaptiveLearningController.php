<?php

namespace App\Http\Controllers;

use App\Services\AdaptiveAnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAdaptiveLearningController extends Controller
{
    public function index(Request $request, AdaptiveAnalyticsService $analytics): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        return Inertia::render('Teacher/AdaptiveLearning', [
            'low_students' => $analytics->teacherLowLevelStudents($user),
            'weak_topics' => $analytics->teacherWeakTopics($user),
        ]);
    }

    public function diagnosticResults(Request $request, AdaptiveAnalyticsService $analytics): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        return Inertia::render('Teacher/DiagnosticResults', [
            'low_students' => $analytics->teacherLowLevelStudents($user),
            'weak_topics' => $analytics->teacherWeakTopics($user),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\AcademicRiskAnalysisService;
use App\Services\AITutorService;
use App\Support\AIDashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAIController extends Controller
{
    public function insights(
        Request $request,
        AcademicRiskAnalysisService $risk,
        AITutorService $ai,
    ): Response {
        $this->authorize('useTeacherInsights', AIDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $rows = $risk->studentsAtRiskForTeacher($user);
        $cacheKey = $ai->teacherInsightCacheKey($user->id);
        $cached = Cache::get($cacheKey);
        $insight = $cached ?? $ai->teacherSectionInsight($user, $rows);

        return Inertia::render('Teacher/AIInsights', [
            'risk_rows' => $rows,
            'insight' => $insight,
            'ai_enabled' => (bool) config('ai.tutor_enabled'),
        ]);
    }

    public function studentsRisk(Request $request, AcademicRiskAnalysisService $risk): Response
    {
        $this->authorize('useTeacherInsights', AIDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $rows = $risk->studentsAtRiskForTeacher($user);

        return Inertia::render('Teacher/StudentsRisk', [
            'risk_rows' => $rows,
        ]);
    }
}

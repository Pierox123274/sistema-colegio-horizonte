<?php

namespace App\Http\Controllers;

use App\Services\AcademicRiskAnalysisService;
use App\Support\AIDashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAcademicRiskController extends Controller
{
    public function index(Request $request, AcademicRiskAnalysisService $risk): Response
    {
        $this->authorize('useTeacherInsights', AIDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $rows = $risk->studentsAtRiskForTeacher($user);

        return Inertia::render('Teacher/AcademicRisk', [
            'risk_rows' => $rows,
        ]);
    }
}

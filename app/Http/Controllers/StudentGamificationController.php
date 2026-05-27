<?php

namespace App\Http\Controllers;

use App\Services\GamificationService;
use App\Services\StudentContextService;
use App\Support\GamificationDashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentGamificationController extends Controller
{
    public function index(
        Request $request,
        StudentContextService $context,
        GamificationService $gamification,
    ): Response {
        $this->authorize('viewStudent', GamificationDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);
        $student = $context->portalStudentFor($user);
        abort_if($student === null, 403);

        return Inertia::render('Student/Gamification/Index', [
            'summary' => $gamification->studentSummary($student),
        ]);
    }
}

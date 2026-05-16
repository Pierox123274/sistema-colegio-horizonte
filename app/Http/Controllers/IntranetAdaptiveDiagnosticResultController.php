<?php

namespace App\Http\Controllers;

use App\Enums\DiagnosticAttemptStatus;
use App\Models\DiagnosticAttempt;
use App\Models\DiagnosticExam;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntranetAdaptiveDiagnosticResultController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', DiagnosticExam::class);

        $attempts = DiagnosticAttempt::query()
            ->where('status', DiagnosticAttemptStatus::Completed)
            ->with(['student:id,first_name,last_name,code', 'diagnosticExam:id,title'])
            ->orderByDesc('completed_at')
            ->paginate(30)
            ->through(fn (DiagnosticAttempt $a) => [
                'id' => $a->id,
                'student_name' => $a->student?->fullName(),
                'student_code' => $a->student?->code,
                'exam_title' => $a->diagnosticExam?->title,
                'score_percent' => $a->score_percent,
                'classified_level' => $a->classified_level,
                'completed_at' => $a->completed_at?->toIso8601String(),
            ]);

        return Inertia::render('Intranet/Adaptive/Results/Index', [
            'attempts' => $attempts,
        ]);
    }
}

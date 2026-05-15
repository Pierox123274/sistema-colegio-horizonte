<?php

namespace App\Http\Controllers;

use App\Models\GradeRecord;
use App\Services\AcademicGradeService;
use App\Services\StudentContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentGradesController extends Controller
{
    public function __construct(
        private readonly StudentContextService $studentContext,
        private readonly AcademicGradeService $academicGradeService
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $context = $this->studentContext->portalContext($user);
        $student = $context['student'];

        $history = null;
        $metrics = ['course_average' => 0, 'general_average' => 0];

        if ($student !== null) {
            $history = $this->academicGradeService->studentHistory($student);
            $rows = GradeRecord::query()
                ->where('student_id', $student->id)
                ->get();
            $metrics = $this->studentContext->gradeMetrics($rows);
        }

        return Inertia::render('Student/Grades/Index', [
            'student' => $student !== null ? $this->studentContext->studentCard($student) : null,
            'history' => $history,
            'metrics' => $metrics,
            'has_student' => $context['has_student'],
            'portal_scoped' => $context['portal_scoped'],
            'empty_message' => $context['empty_message'],
        ]);
    }
}

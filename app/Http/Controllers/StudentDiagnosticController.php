<?php

namespace App\Http\Controllers;

use App\Enums\DiagnosticAttemptStatus;
use App\Http\Requests\Student\StoreDiagnosticAnswerRequest;
use App\Models\DiagnosticAttempt;
use App\Models\DiagnosticExam;
use App\Services\AdaptiveDiagnosticService;
use App\Services\DiagnosticExamAccessService;
use App\Services\StudentContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentDiagnosticController extends Controller
{
    public function index(
        Request $request,
        StudentContextService $context,
        DiagnosticExamAccessService $examAccess,
    ): Response {
        $portal = $context->portalContext($request->user());
        $student = $portal['student'];

        $query = $student !== null
            ? $examAccess->queryExamsForStudent($student)
            : DiagnosticExam::query()->whereRaw('0 = 1');

        $exams = $query
            ->with('subject:id,name')
            ->orderBy('title')
            ->get()
            ->map(fn (DiagnosticExam $e) => [
                'id' => $e->id,
                'title' => $e->title,
                'description' => $e->description,
                'mode' => $e->mode->value,
                'subject' => $e->subject ? ['id' => $e->subject->id, 'name' => $e->subject->name] : null,
                'prevent_retake' => $e->prevent_retake_after_completion,
            ]);

        return Inertia::render('Student/Diagnostic/Index', [
            'portal' => $portal,
            'exams' => $exams,
        ]);
    }

    public function show(
        Request $request,
        StudentContextService $context,
        AdaptiveDiagnosticService $adaptive,
        DiagnosticExam $exam,
    ): Response|RedirectResponse {
        $this->authorize('take', $exam);
        $exam->load('subject');
        $portal = $context->portalContext($request->user());
        $student = $portal['student'];
        if ($student === null) {
            return redirect()->route('student.diagnostic.index');
        }

        $canStart = $adaptive->canStartExam($student, $exam);

        return Inertia::render('Student/Diagnostic/Show', [
            'portal' => $portal,
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'mode' => $exam->mode->value,
                'adaptive_question_count' => $exam->adaptive_question_count,
                'subject' => $exam->subject ? ['name' => $exam->subject->name] : null,
            ],
            'can_start' => $canStart,
        ]);
    }

    public function start(
        Request $request,
        StudentContextService $context,
        AdaptiveDiagnosticService $adaptive,
        DiagnosticExam $exam,
    ): RedirectResponse {
        $this->authorize('take', $exam);
        $student = $context->portalStudentFor($request->user());
        abort_if($student === null, 403);

        try {
            $attempt = $adaptive->startAttempt($student, $request->user(), $exam);
        } catch (\Throwable $e) {
            return redirect()
                ->route('student.diagnostic.show', $exam)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('student.diagnostic.attempt', $attempt);
    }

    public function attempt(
        Request $request,
        AdaptiveDiagnosticService $adaptive,
        DiagnosticAttempt $attempt,
    ): Response {
        $this->authorize('interact', $attempt);
        $attempt->load('diagnosticExam.subject');

        $question = $adaptive->peekCurrentQuestionPayload($attempt);
        $completed = $attempt->status === DiagnosticAttemptStatus::Completed;

        return Inertia::render('Student/Diagnostic/Attempt', [
            'attempt' => [
                'id' => $attempt->id,
                'status' => $attempt->status->value,
                'score_percent' => $attempt->score_percent,
                'classified_level' => $attempt->classified_level,
                'weakness_by_topic' => $attempt->weakness_by_topic ?? [],
                'exam' => [
                    'title' => $attempt->diagnosticExam->title,
                    'mode' => $attempt->diagnosticExam->mode->value,
                ],
            ],
            'question' => $question,
            'completed' => $completed,
        ]);
    }

    public function answer(
        StoreDiagnosticAnswerRequest $request,
        AdaptiveDiagnosticService $adaptive,
        DiagnosticAttempt $attempt,
    ): RedirectResponse {
        $this->authorize('interact', $attempt);

        $attempt->refresh();

        $raw = $request->input('answer');
        $adaptive->submitAnswer($attempt, $raw);

        return redirect()
            ->route('student.diagnostic.attempt', $attempt->fresh())
            ->with('success', 'Respuesta registrada.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\OnlineExam;
use App\Models\OnlineExamAttempt;
use App\Models\VirtualClassroom;
use App\Services\AssignmentService;
use App\Services\OnlineExamService;
use App\Services\StudentContextService;
use App\Services\VirtualClassroomAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentVirtualClassroomController extends Controller
{
    public function index(
        Request $request,
        StudentContextService $context,
        VirtualClassroomAccessService $access,
    ): Response {
        $this->authorize('viewAny', VirtualClassroom::class);

        $student = $context->portalStudentFor($request->user());
        abort_if($student === null, 403);

        $classrooms = $access->queryClassroomsForStudent($student)
            ->with(['subject:id,name', 'section:id,name'])
            ->paginate(15)
            ->through(fn (VirtualClassroom $c) => [
                'id' => $c->id,
                'title' => $c->title,
                'subject' => $c->subject?->name,
                'section' => $c->section?->name,
            ]);

        return Inertia::render('Student/Classrooms/Index', [
            'classrooms' => $classrooms,
        ]);
    }

    public function show(
        Request $request,
        StudentContextService $context,
        VirtualClassroom $classroom,
        AssignmentService $assignments,
    ): Response {
        $this->authorize('view', $classroom);

        $student = $context->portalStudentFor($request->user());
        abort_if($student === null, 403);

        $classroom->load([
            'announcements' => fn ($q) => $q->latest()->limit(10),
            'resources' => fn ($q) => $q->orderBy('sort_order'),
            'assignments' => fn ($q) => $q->where('is_published', true),
            'onlineExams' => fn ($q) => $q->where('is_published', true),
        ]);

        $assignmentRows = $classroom->assignments->map(function (Assignment $a) use ($student, $assignments) {
            $sub = $a->submissions()->where('student_id', $student->id)->first();

            return [
                'id' => $a->id,
                'title' => $a->title,
                'due_at' => $a->due_at?->toIso8601String(),
                'max_score' => $a->max_score,
                'status' => $assignments->resolveSubmissionStatus($a, $sub),
                'score' => $sub?->score,
            ];
        });

        return Inertia::render('Student/Classrooms/Show', [
            'classroom' => [
                'id' => $classroom->id,
                'title' => $classroom->title,
                'description' => $classroom->description,
                'subject' => $classroom->subject?->name,
            ],
            'announcements' => $classroom->announcements,
            'resources' => $classroom->resources,
            'assignments' => $assignmentRows,
            'exams' => $classroom->onlineExams->map(fn ($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'time_limit_minutes' => $e->time_limit_minutes,
            ]),
        ]);
    }

    public function submitAssignment(
        Request $request,
        StudentContextService $context,
        VirtualClassroom $classroom,
        Assignment $assignment,
        AssignmentService $assignments,
    ): RedirectResponse {
        $this->authorize('submit', $assignment);

        $user = $request->user();
        $student = $context->portalStudentFor($user);
        abort_if($user === null || $student === null, 403);

        $data = $request->validate([
            'student_comment' => ['nullable', 'string', 'max:2000'],
            'file' => ['nullable', 'file', 'max:10240'],
        ]);

        $assignments->submit(
            $user,
            $student,
            $assignment,
            $data['student_comment'] ?? null,
            $request->file('file'),
        );

        return back()->with('success', 'Entrega registrada.');
    }

    public function startExam(
        Request $request,
        StudentContextService $context,
        OnlineExamService $exams,
        OnlineExam $exam,
    ): RedirectResponse {
        $this->authorize('take', $exam);

        $user = $request->user();
        $student = $context->portalStudentFor($user);
        abort_if($user === null || $student === null, 403);

        if (! $exams->canStart($student, $exam)) {
            return back()->with('error', 'No tiene intentos disponibles.');
        }

        $attempt = $exams->startAttempt($student, $user, $exam);

        return redirect()->route('student.classrooms.exam-attempt', $attempt);
    }

    public function examAttempt(
        Request $request,
        OnlineExamAttempt $attempt,
    ): Response {
        $this->authorize('interact', $attempt);
        $attempt->load('onlineExam.questions');

        $completed = $attempt->status->value === 'completed';

        return Inertia::render('Student/Classrooms/ExamAttempt', [
            'attempt' => [
                'id' => $attempt->id,
                'status' => $attempt->status->value,
                'score_percent' => $attempt->score_percent,
                'exam_title' => $attempt->onlineExam->title,
            ],
            'questions' => $attempt->onlineExam->questions->map(fn ($q) => [
                'id' => $q->id,
                'stem' => $q->stem,
                'question_type' => $q->question_type->value,
                'options' => $q->options,
            ]),
            'completed' => $completed,
        ]);
    }

    public function answerExam(
        Request $request,
        OnlineExamService $exams,
        OnlineExamAttempt $attempt,
    ): RedirectResponse {
        $this->authorize('interact', $attempt);

        $answers = $request->validate([
            'answers' => ['required', 'array'],
        ])['answers'];

        $exams->completeAttempt($attempt, $answers);

        return redirect()
            ->route('student.classrooms.exam-attempt', $attempt->fresh())
            ->with('success', 'Evaluación enviada.');
    }
}

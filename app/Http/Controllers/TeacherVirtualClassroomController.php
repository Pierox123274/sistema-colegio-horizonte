<?php

namespace App\Http\Controllers;

use App\Enums\OnlineExamQuestionType;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\VirtualClassroom;
use App\Services\AssignmentService;
use App\Services\LMSService;
use App\Services\OnlineExamService;
use App\Services\TeacherContextService;
use App\Services\VirtualClassroomAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherVirtualClassroomController extends Controller
{
    public function index(Request $request, VirtualClassroomAccessService $access): Response
    {
        $this->authorize('viewAny', VirtualClassroom::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $classrooms = $access->queryClassroomsForTeacher($user)
            ->with(['subject:id,name', 'section:id,name', 'academicYear:id,name'])
            ->paginate(15)
            ->through(fn (VirtualClassroom $c) => [
                'id' => $c->id,
                'title' => $c->title,
                'subject' => $c->subject?->name,
                'section' => $c->section?->name,
                'academic_year' => $c->academicYear?->name,
                'assignments_count' => $c->assignments()->count(),
                'exams_count' => $c->onlineExams()->count(),
            ]);

        return Inertia::render('Teacher/Classrooms/Index', [
            'classrooms' => $classrooms,
            'can_create' => $user->can('create', VirtualClassroom::class),
        ]);
    }

    public function create(Request $request, TeacherContextService $teacherContext): Response
    {
        $this->authorize('create', VirtualClassroom::class);

        return Inertia::render('Teacher/Classrooms/Create', [
            'assignments' => $teacherContext->assignmentsTableFor($request->user()),
        ]);
    }

    public function store(
        Request $request,
        VirtualClassroomAccessService $access,
        LMSService $lms,
    ): RedirectResponse {
        $this->authorize('create', VirtualClassroom::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
        ]);

        if (! $access->teacherMayCreateClassroomWith($user, $data) && ! $access->isAdministrator($user)) {
            abort(403);
        }

        $classroom = $lms->createClassroom($user, [
            ...$data,
            'teacher_user_id' => $user->id,
        ]);

        return redirect()->route('teacher.classrooms.show', $classroom);
    }

    public function show(Request $request, VirtualClassroom $classroom): Response
    {
        $this->authorize('view', $classroom);

        $classroom->load([
            'subject:id,name',
            'section:id,name',
            'academicYear:id,name',
            'announcements' => fn ($q) => $q->with('author:id,name')->latest()->limit(10),
            'resources' => fn ($q) => $q->orderBy('sort_order'),
            'assignments' => fn ($q) => $q->latest(),
            'onlineExams' => fn ($q) => $q->latest(),
        ]);

        $submissionsPending = AssignmentSubmission::query()
            ->where('status', 'submitted')
            ->whereHas('assignment', fn ($q) => $q->where('virtual_classroom_id', $classroom->id))
            ->count();

        return Inertia::render('Teacher/Classrooms/Show', [
            'classroom' => [
                'id' => $classroom->id,
                'title' => $classroom->title,
                'description' => $classroom->description,
                'subject' => $classroom->subject?->name,
                'section' => $classroom->section?->name,
                'academic_year' => $classroom->academicYear?->name,
            ],
            'announcements' => $classroom->announcements->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'body' => $a->body,
                'author' => $a->author?->name,
                'published_at' => $a->published_at?->toIso8601String(),
            ]),
            'resources' => $classroom->resources->map(fn ($r) => [
                'id' => $r->id,
                'title' => $r->title,
                'resource_type' => $r->resource_type->value,
                'external_url' => $r->external_url,
                'topic' => $r->topic,
            ]),
            'assignments' => $classroom->assignments->map(fn ($a) => [
                'id' => $a->id,
                'title' => $a->title,
                'due_at' => $a->due_at?->toIso8601String(),
                'max_score' => $a->max_score,
                'submissions_count' => $a->submissions()->count(),
            ]),
            'exams' => $classroom->onlineExams->map(fn ($e) => [
                'id' => $e->id,
                'title' => $e->title,
                'max_attempts' => $e->max_attempts,
                'is_published' => $e->is_published,
            ]),
            'pending_review_count' => $submissionsPending,
        ]);
    }

    public function storeAssignment(
        Request $request,
        VirtualClassroom $classroom,
        AssignmentService $assignments,
    ): RedirectResponse {
        $this->authorize('update', $classroom);
        $this->authorize('create', Assignment::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'max_score' => ['nullable', 'numeric', 'min:1', 'max:100'],
            'due_at' => ['nullable', 'date'],
        ]);

        $assignments->createAssignment($user, $classroom, $data);

        return redirect()->route('teacher.classrooms.show', $classroom)->with('success', 'Tarea creada.');
    }

    public function gradeSubmission(
        Request $request,
        VirtualClassroom $classroom,
        Assignment $assignment,
        AssignmentSubmission $submission,
        AssignmentService $assignments,
    ): RedirectResponse {
        $this->authorize('view', $classroom);
        $this->authorize('grade', $submission);

        $user = $request->user();
        abort_if($user === null, 403);

        $data = $request->validate([
            'score' => ['required', 'numeric', 'min:0'],
            'teacher_feedback' => ['nullable', 'string'],
        ]);

        $assignments->grade($user, $submission, (float) $data['score'], $data['teacher_feedback'] ?? null);

        return back()->with('success', 'Entrega calificada.');
    }

    public function storeExam(
        Request $request,
        VirtualClassroom $classroom,
        OnlineExamService $exams,
    ): RedirectResponse {
        $this->authorize('update', $classroom);

        $user = $request->user();
        abort_if($user === null, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'time_limit_minutes' => ['nullable', 'integer', 'min:5', 'max:180'],
            'max_attempts' => ['nullable', 'integer', 'min:1', 'max:5'],
            'question_stem' => ['required', 'string'],
            'correct_option' => ['required', 'string', 'max:255'],
        ]);

        $exams->createExam($user, $classroom, $data, [
            [
                'question_type' => OnlineExamQuestionType::MultipleChoice->value,
                'stem' => $data['question_stem'],
                'options' => [
                    ['label' => 'A', 'value' => 'a'],
                    ['label' => 'B', 'value' => 'b'],
                ],
                'correct_answer' => ['value' => 'a'],
                'points' => 1,
            ],
        ]);

        return redirect()->route('teacher.classrooms.show', $classroom)->with('success', 'Evaluación creada.');
    }
}

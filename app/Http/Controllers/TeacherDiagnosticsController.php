<?php

namespace App\Http\Controllers;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Enums\DiagnosticAttemptStatus;
use App\Enums\DiagnosticExamMode;
use App\Models\AcademicYear;
use App\Models\DiagnosticAttempt;
use App\Models\DiagnosticExam;
use App\Models\EducationalLevel;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Subject;
use App\Services\AdaptiveAnalyticsService;
use App\Services\AuditService;
use App\Services\DiagnosticExamAccessService;
use App\Services\TeacherContextService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherDiagnosticsController extends Controller
{
    public function index(Request $request, DiagnosticExamAccessService $access): Response
    {
        $this->authorize('viewAny', DiagnosticExam::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $exams = $access->queryExamsForTeacher($user)
            ->with(['subject:id,name', 'section:id,name', 'academicYear:id,name,year'])
            ->paginate(20)
            ->through(fn (DiagnosticExam $e) => [
                'id' => $e->id,
                'title' => $e->title,
                'mode' => $e->mode->value,
                'is_active' => $e->is_active,
                'subject' => $e->subject ? $e->subject->name : '—',
                'section' => $e->section ? $e->section->name : '—',
                'attempts_count' => $e->attempts()->count(),
                'questions_count' => $e->questions()->count(),
            ]);

        return Inertia::render('Teacher/Diagnostics/Index', [
            'exams' => $exams,
            'can_create' => $request->user()?->can('create', DiagnosticExam::class) ?? false,
        ]);
    }

    public function create(Request $request, TeacherContextService $teacherContext): Response
    {
        $this->authorize('create', DiagnosticExam::class);

        $user = $request->user();
        abort_if($user === null, 403);

        return Inertia::render('Teacher/Diagnostics/Create', [
            'assignments' => $teacherContext->assignmentsTableFor($user),
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name']),
            'sections' => Section::query()->with('grade:id,name')->orderBy('name')->get(['id', 'name', 'grade_id']),
            'grades' => Grade::query()->orderBy('name')->get(['id', 'name', 'educational_level_id']),
            'educational_levels' => EducationalLevel::query()->orderBy('name')->get(['id', 'name']),
            'academic_years' => AcademicYear::query()->orderByDesc('year')->get(['id', 'name', 'year']),
            'modes' => [
                ['value' => DiagnosticExamMode::Fixed->value, 'label' => 'Fijo'],
                ['value' => DiagnosticExamMode::Adaptive->value, 'label' => 'Adaptativo'],
            ],
        ]);
    }

    public function store(Request $request, DiagnosticExamAccessService $access, AuditService $audit): RedirectResponse
    {
        $this->authorize('create', DiagnosticExam::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'educational_level_id' => ['nullable', 'exists:educational_levels,id'],
            'grade_id' => ['nullable', 'exists:grades,id'],
            'section_id' => ['required', 'exists:sections,id'],
            'mode' => ['required', 'string', 'in:'.DiagnosticExamMode::Fixed->value.','.DiagnosticExamMode::Adaptive->value],
            'is_active' => ['boolean'],
            'prevent_retake_after_completion' => ['boolean'],
            'adaptive_question_count' => ['required', 'integer', 'min:1', 'max:100'],
            'threshold_basic_percent' => ['required', 'integer', 'min:0', 'max:98'],
            'threshold_intermediate_percent' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        if (! $access->teacherMayCreateExamWith($user, $data) && ! $access->isAdministrator($user)) {
            abort(403, 'No puede crear un diagnóstico fuera de su asignación.');
        }

        $mode = DiagnosticExamMode::from($data['mode']);
        unset($data['mode']);

        $exam = DiagnosticExam::query()->create([
            ...$data,
            'mode' => $mode,
            'is_active' => $request->boolean('is_active', true),
            'prevent_retake_after_completion' => $request->boolean('prevent_retake_after_completion'),
            'created_by_user_id' => $user->id,
        ]);

        $audit->log(
            AuditAction::Create,
            AuditModule::AdaptiveLearning,
            $user,
            DiagnosticExam::class,
            $exam->id,
            'Creación de examen diagnóstico (docente)',
            null,
            ['title' => $exam->title, 'section_id' => $exam->section_id],
            AuditResult::Success,
        );

        return redirect()
            ->route('teacher.diagnostics.show', $exam)
            ->with('success', 'Diagnóstico creado.');
    }

    public function show(Request $request, DiagnosticExam $exam): Response
    {
        $this->authorize('view', $exam);
        $exam->load(['subject', 'section', 'academicYear', 'questions']);

        return Inertia::render('Teacher/Diagnostics/Show', [
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'description' => $exam->description,
                'mode' => $exam->mode->value,
                'is_active' => $exam->is_active,
                'subject' => $exam->subject?->name,
                'section' => $exam->section?->name,
                'academic_year' => $exam->academicYear?->name,
                'adaptive_question_count' => $exam->adaptive_question_count,
                'threshold_basic_percent' => $exam->threshold_basic_percent,
                'threshold_intermediate_percent' => $exam->threshold_intermediate_percent,
                'questions_count' => $exam->questions->count(),
            ],
            'results_href' => route('teacher.diagnostics.results', $exam, absolute: false),
        ]);
    }

    public function results(
        Request $request,
        DiagnosticExam $exam,
        AdaptiveAnalyticsService $analytics,
        DiagnosticExamAccessService $access,
    ): Response {
        $this->authorize('view', $exam);

        $user = $request->user();
        abort_if($user === null, 403);

        $scopedStudentIds = $access->teacherScopedStudentIdsForDiagnosticResults($user, $exam);

        $attemptsQuery = DiagnosticAttempt::query()
            ->where('diagnostic_exam_id', $exam->id)
            ->where('status', DiagnosticAttemptStatus::Completed)
            ->when(
                $scopedStudentIds !== null,
                function ($q) use ($scopedStudentIds): void {
                    if ($scopedStudentIds === []) {
                        $q->whereRaw('0 = 1');
                    } else {
                        $q->whereIn('student_id', $scopedStudentIds);
                    }
                },
            )
            ->with('student:id,first_name,last_name,code')
            ->orderByDesc('completed_at')
            ->limit(100);

        $attempts = $attemptsQuery
            ->get()
            ->map(fn (DiagnosticAttempt $a) => [
                'student' => $a->student?->fullName(),
                'code' => $a->student?->code,
                'score_percent' => $a->score_percent,
                'classified_level' => $a->classified_level,
                'completed_at' => $a->completed_at?->toIso8601String(),
            ]);

        return Inertia::render('Teacher/Diagnostics/Results', [
            'exam' => ['id' => $exam->id, 'title' => $exam->title],
            'attempts' => $attempts,
            'weak_topics' => $analytics->teacherWeakTopics($user),
        ]);
    }
}

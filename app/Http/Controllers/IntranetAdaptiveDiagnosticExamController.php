<?php

namespace App\Http\Controllers;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Enums\DiagnosticExamMode;
use App\Models\AcademicYear;
use App\Models\DiagnosticExam;
use App\Models\EducationalLevel;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Subject;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntranetAdaptiveDiagnosticExamController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', DiagnosticExam::class);

        $exams = DiagnosticExam::query()
            ->with(['subject:id,name', 'academicYear:id,name,year', 'section:id,name', 'grade:id,name'])
            ->orderByDesc('id')
            ->paginate(20)
            ->through(fn (DiagnosticExam $e) => [
                'id' => $e->id,
                'title' => $e->title,
                'mode' => $e->mode->value,
                'is_active' => $e->is_active,
                'subject' => $e->subject ? ['id' => $e->subject->id, 'name' => $e->subject->name] : null,
                'academic_year' => $e->academicYear ? $e->academicYear->name : null,
                'section' => $e->section ? $e->section->name : null,
                'grade' => $e->grade ? $e->grade->name : null,
                'attempts_count' => $e->attempts()->count(),
                'questions_count' => $e->questions()->count(),
            ]);

        return Inertia::render('Intranet/Adaptive/DiagnosticExams/Index', [
            'exams' => $exams,
            'can_create' => $request->user()?->can('create', DiagnosticExam::class) ?? false,
        ]);
    }

    public function show(Request $request, DiagnosticExam $diagnostic_exam): Response
    {
        $this->authorize('view', $diagnostic_exam);
        $diagnostic_exam->load(['subject', 'academicYear', 'section', 'grade', 'educationalLevel', 'createdBy']);

        return Inertia::render('Intranet/Adaptive/DiagnosticExams/Show', [
            'exam' => [
                'id' => $diagnostic_exam->id,
                'title' => $diagnostic_exam->title,
                'description' => $diagnostic_exam->description,
                'mode' => $diagnostic_exam->mode->value,
                'is_active' => $diagnostic_exam->is_active,
                'adaptive_question_count' => $diagnostic_exam->adaptive_question_count,
                'threshold_basic_percent' => $diagnostic_exam->threshold_basic_percent,
                'threshold_intermediate_percent' => $diagnostic_exam->threshold_intermediate_percent,
                'prevent_retake_after_completion' => $diagnostic_exam->prevent_retake_after_completion,
                'subject' => $diagnostic_exam->subject ? ['id' => $diagnostic_exam->subject->id, 'name' => $diagnostic_exam->subject->name] : null,
                'academic_year_id' => $diagnostic_exam->academic_year_id,
                'section_id' => $diagnostic_exam->section_id,
                'grade_id' => $diagnostic_exam->grade_id,
                'educational_level_id' => $diagnostic_exam->educational_level_id,
                'attempts_count' => $diagnostic_exam->attempts()->count(),
                'questions_count' => $diagnostic_exam->questions()->count(),
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', DiagnosticExam::class);

        return Inertia::render('Intranet/Adaptive/DiagnosticExams/Create', $this->formCatalog());
    }

    /**
     * @return array<string, mixed>
     */
    private function formCatalog(): array
    {
        return [
            'academic_years' => AcademicYear::query()->orderByDesc('year')->get(['id', 'name', 'year']),
            'levels' => EducationalLevel::query()->orderBy('name')->get(['id', 'name']),
            'grades' => Grade::query()->with('educationalLevel:id,name')->orderBy('name')->get(['id', 'name', 'educational_level_id']),
            'sections' => Section::query()->with('grade:id,name')->orderBy('name')->get(['id', 'name', 'grade_id']),
            'subjects' => Subject::query()->orderBy('name')->get(['id', 'name']),
            'modes' => [
                ['value' => DiagnosticExamMode::Fixed->value, 'label' => 'Fijo'],
                ['value' => DiagnosticExamMode::Adaptive->value, 'label' => 'Adaptativo'],
            ],
        ];
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        $this->authorize('create', DiagnosticExam::class);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'educational_level_id' => ['nullable', 'exists:educational_levels,id'],
            'grade_id' => ['nullable', 'exists:grades,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'mode' => ['required', 'string', 'in:'.DiagnosticExamMode::Fixed->value.','.DiagnosticExamMode::Adaptive->value],
            'is_active' => ['boolean'],
            'prevent_retake_after_completion' => ['boolean'],
            'adaptive_question_count' => ['required', 'integer', 'min:1', 'max:100'],
            'threshold_basic_percent' => ['required', 'integer', 'min:0', 'max:98'],
            'threshold_intermediate_percent' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $mode = DiagnosticExamMode::from($data['mode']);
        unset($data['mode']);

        $exam = DiagnosticExam::query()->create([
            ...$data,
            'mode' => $mode,
            'is_active' => $request->boolean('is_active', true),
            'prevent_retake_after_completion' => $request->boolean('prevent_retake_after_completion'),
            'created_by_user_id' => $request->user()?->id,
        ]);

        $audit->log(
            AuditAction::Create,
            AuditModule::AdaptiveLearning,
            $request->user(),
            DiagnosticExam::class,
            $exam->id,
            'Creación de examen diagnóstico (intranet)',
            null,
            ['title' => $exam->title],
            AuditResult::Success,
        );

        return redirect()
            ->route('intranet.adaptive.diagnostic-exams.edit', $exam)
            ->with('success', 'Diagnóstico creado. Asocie preguntas desde el banco.');
    }

    public function edit(Request $request, DiagnosticExam $diagnostic_exam): Response
    {
        $this->authorize('update', $diagnostic_exam);

        return Inertia::render('Intranet/Adaptive/DiagnosticExams/Edit', [
            ...$this->formCatalog(),
            'exam' => [
                'id' => $diagnostic_exam->id,
                'title' => $diagnostic_exam->title,
                'description' => $diagnostic_exam->description,
                'subject_id' => $diagnostic_exam->subject_id,
                'academic_year_id' => $diagnostic_exam->academic_year_id,
                'educational_level_id' => $diagnostic_exam->educational_level_id,
                'grade_id' => $diagnostic_exam->grade_id,
                'section_id' => $diagnostic_exam->section_id,
                'mode' => $diagnostic_exam->mode->value,
                'is_active' => $diagnostic_exam->is_active,
                'prevent_retake_after_completion' => $diagnostic_exam->prevent_retake_after_completion,
                'adaptive_question_count' => $diagnostic_exam->adaptive_question_count,
                'threshold_basic_percent' => $diagnostic_exam->threshold_basic_percent,
                'threshold_intermediate_percent' => $diagnostic_exam->threshold_intermediate_percent,
            ],
        ]);
    }

    public function update(Request $request, DiagnosticExam $diagnostic_exam, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $diagnostic_exam);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'educational_level_id' => ['nullable', 'exists:educational_levels,id'],
            'grade_id' => ['nullable', 'exists:grades,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'mode' => ['required', 'string', 'in:'.DiagnosticExamMode::Fixed->value.','.DiagnosticExamMode::Adaptive->value],
            'is_active' => ['boolean'],
            'prevent_retake_after_completion' => ['boolean'],
            'adaptive_question_count' => ['required', 'integer', 'min:1', 'max:100'],
            'threshold_basic_percent' => ['required', 'integer', 'min:0', 'max:98'],
            'threshold_intermediate_percent' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $mode = DiagnosticExamMode::from($data['mode']);
        unset($data['mode']);

        $old = $diagnostic_exam->getAttributes();
        $diagnostic_exam->fill([
            ...$data,
            'mode' => $mode,
            'is_active' => $request->boolean('is_active', $diagnostic_exam->is_active),
            'prevent_retake_after_completion' => $request->boolean('prevent_retake_after_completion', $diagnostic_exam->prevent_retake_after_completion),
        ]);
        $diagnostic_exam->save();

        $audit->log(
            AuditAction::Update,
            AuditModule::AdaptiveLearning,
            $request->user(),
            DiagnosticExam::class,
            $diagnostic_exam->id,
            'Edición de examen diagnóstico',
            $old,
            $diagnostic_exam->getAttributes(),
            AuditResult::Success,
        );

        return redirect()
            ->route('intranet.adaptive.diagnostic-exams.index')
            ->with('success', 'Diagnóstico actualizado.');
    }
}

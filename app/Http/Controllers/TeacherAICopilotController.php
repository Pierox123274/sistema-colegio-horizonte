<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\VirtualClassroom;
use App\Services\TeacherAICopilotService;
use App\Services\TeacherContextService;
use App\Support\AIDashboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAICopilotController extends Controller
{
    public function index(Request $request, TeacherContextService $teacherContext): Response
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);

        return Inertia::render('Teacher/AICopilot/Index', $this->sharedProps($request, $teacherContext));
    }

    public function exams(Request $request, TeacherContextService $teacherContext): Response
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);

        return Inertia::render('Teacher/AICopilot/Exams', $this->sharedProps($request, $teacherContext));
    }

    public function assignments(Request $request, TeacherContextService $teacherContext): Response
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);

        return Inertia::render('Teacher/AICopilot/Assignments', $this->sharedProps($request, $teacherContext));
    }

    public function rubrics(Request $request, TeacherContextService $teacherContext): Response
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);

        return Inertia::render('Teacher/AICopilot/Rubrics', $this->sharedProps($request, $teacherContext));
    }

    public function generateExam(Request $request, TeacherAICopilotService $copilot): JsonResponse
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        $input = $request->validate([
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'grade' => ['nullable', 'string', 'max:120'],
            'topic' => ['required', 'string', 'max:255'],
            'difficulty' => ['nullable', 'string', 'max:40'],
            'competencies' => ['nullable', 'array'],
            'competencies.*' => ['string', 'max:120'],
            'question_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'question_types' => ['nullable', 'string', 'max:80'],
        ]);

        return response()->json($copilot->generateExam($user, $input));
    }

    public function generateAssignment(Request $request, TeacherAICopilotService $copilot): JsonResponse
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        $input = $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'grade' => ['nullable', 'string', 'max:120'],
            'duration_minutes' => ['nullable', 'integer', 'min:15', 'max:300'],
        ]);

        return response()->json($copilot->generateAssignment($user, $input));
    }

    public function generateRubric(Request $request, TeacherAICopilotService $copilot): JsonResponse
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        $input = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'activity' => ['nullable', 'string', 'max:500'],
        ]);

        return response()->json($copilot->generateRubric($user, $input));
    }

    public function generateSession(Request $request, TeacherAICopilotService $copilot): JsonResponse
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        $input = $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'duration_minutes' => ['nullable', 'integer', 'min:30', 'max:240'],
        ]);

        return response()->json($copilot->generateSessionPlan($user, $input));
    }

    public function exportExam(Request $request, TeacherAICopilotService $copilot): JsonResponse
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        $data = $request->validate([
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'questions' => ['required', 'array', 'min:1'],
        ]);

        $result = $copilot->exportQuestionsToBank($user, (int) $data['subject_id'], $data['questions']);

        return response()->json($result);
    }

    public function exportAssignment(Request $request, TeacherAICopilotService $copilot): JsonResponse
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        $data = $request->validate([
            'virtual_classroom_id' => ['required', 'integer', 'exists:virtual_classrooms,id'],
            'payload' => ['required', 'array'],
        ]);

        $classroom = VirtualClassroom::query()->findOrFail($data['virtual_classroom_id']);
        $this->authorize('update', $classroom);

        $assignment = $copilot->exportToAssignment($user, $classroom, $data['payload']);

        return response()->json([
            'assignment_id' => $assignment->id,
            'redirect' => route('teacher.classrooms.show', $classroom, absolute: false),
        ]);
    }

    public function predictive(Request $request, TeacherAICopilotService $copilot): JsonResponse
    {
        $this->authorize('useTeacherCopilot', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        return response()->json($copilot->predictiveInsights($user));
    }

    /**
     * @return array<string, mixed>
     */
    private function sharedProps(Request $request, TeacherContextService $teacherContext): array
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $assignments = $teacherContext->activeAssignmentsFor($user);
        $subjectIds = $assignments->pluck('subject_id')->filter()->unique()->values();
        $subjects = Subject::query()->whereIn('id', $subjectIds)->orderBy('name')->get(['id', 'name']);

        $classrooms = VirtualClassroom::query()
            ->where('teacher_user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title', 'section_id', 'subject_id']);

        return [
            'subjects' => $subjects,
            'classrooms' => $classrooms,
            'assignments' => $assignments->map(fn ($a) => [
                'section' => $a->section?->name,
                'grade' => $a->section?->grade?->name,
                'subject' => $a->subject?->name,
                'subject_id' => $a->subject_id,
            ])->values(),
            'ai_enabled' => (bool) config('ai.tutor_enabled'),
            'modules' => config('ai.modules', []),
            'provider' => config('ai.provider'),
        ];
    }
}

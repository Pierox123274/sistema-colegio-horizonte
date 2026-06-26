<?php

namespace App\Http\Controllers;

use App\Http\Requests\AI\StoreStudentAiMessageRequest;
use App\Services\AITutorService;
use App\Services\GamificationService;
use App\Services\StudentContextService;
use App\Services\StudentLearningCoachService;
use App\Support\AIDashboard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentAIController extends Controller
{
    public function tutor(Request $request, StudentContextService $context, AITutorService $ai): RedirectResponse|Response
    {
        $this->authorize('useStudentTutor', AIDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $portal = $context->portalContext($user);
        $student = $portal['student'];

        $bundle = null;
        if ($student !== null) {
            $bundle = $ai->studentInsightBundle($student);
        }

        return Inertia::render('Student/AITutor', [
            'portal' => $portal,
            'insight' => $bundle,
            'ai_enabled' => (bool) config('ai.tutor_enabled'),
            'provider' => config('ai.provider'),
        ]);
    }

    public function recommendations(Request $request, StudentContextService $context, AITutorService $ai): RedirectResponse|Response
    {
        $this->authorize('useStudentTutor', AIDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $portal = $context->portalContext($user);
        $student = $portal['student'];

        $bundle = null;
        if ($student !== null) {
            $bundle = $ai->studentInsightBundle($student);
        }

        return Inertia::render('Student/Recommendations', [
            'portal' => $portal,
            'insight' => $bundle,
            'ai_enabled' => (bool) config('ai.tutor_enabled'),
        ]);
    }

    public function message(
        StoreStudentAiMessageRequest $request,
        StudentContextService $context,
        AITutorService $ai,
        GamificationService $gamification
    ): JsonResponse|RedirectResponse {
        $user = $request->user();
        abort_if($user === null, 403);

        $student = $context->portalStudentFor($user);
        if ($student === null) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'No hay ficha estudiante vinculada para usar el tutor IA.',
                ], 422);
            }

            return redirect()
                ->route('student.ai-tutor.index')
                ->with('error', 'No hay ficha estudiante vinculada para usar el tutor IA.');
        }

        $payload = $ai->studentChat($user, $student, (string) $request->validated('message'));
        $gamification->onAiTutorUsage($student);

        if ($request->wantsJson()) {
            return response()->json([
                'reply' => $payload['reply'],
                'success' => $payload['success'],
                'cached' => $payload['cached'],
                'model' => $payload['model'],
                'error_code' => $payload['error_code'],
                'fallback' => $payload['fallback'] ?? false,
            ]);
        }

        return back()->with([
            'ai_reply' => $payload['reply'],
            'ai_meta' => [
                'success' => $payload['success'],
                'cached' => $payload['cached'],
                'model' => $payload['model'],
                'fallback' => $payload['fallback'] ?? false,
            ],
        ]);
    }

    public function coachSummary(
        Request $request,
        StudentContextService $context,
        StudentLearningCoachService $coach,
    ): JsonResponse {
        $this->authorize('useStudentTutor', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        $student = $context->portalStudentFor($user);
        abort_if($student === null, 422, 'Sin ficha estudiante.');

        $data = $request->validate(['topic' => ['required', 'string', 'max:255']]);

        return response()->json($coach->summary($user, $student, $data['topic']));
    }

    public function coachMiniQuiz(
        Request $request,
        StudentContextService $context,
        StudentLearningCoachService $coach,
    ): JsonResponse {
        $this->authorize('useStudentTutor', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        $student = $context->portalStudentFor($user);
        abort_if($student === null, 422, 'Sin ficha estudiante.');

        $data = $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'count' => ['nullable', 'integer', 'min:1', 'max:10'],
        ]);

        return response()->json($coach->miniQuiz($user, $student, $data['topic'], (int) ($data['count'] ?? 3)));
    }

    public function coachPractice(
        Request $request,
        StudentContextService $context,
        StudentLearningCoachService $coach,
    ): JsonResponse {
        $this->authorize('useStudentTutor', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        $student = $context->portalStudentFor($user);
        abort_if($student === null, 422, 'Sin ficha estudiante.');

        $data = $request->validate(['topic' => ['required', 'string', 'max:255']]);

        return response()->json($coach->practicePrompt($user, $student, $data['topic']));
    }

    public function coachExplain(
        Request $request,
        StudentContextService $context,
        StudentLearningCoachService $coach,
    ): JsonResponse {
        $this->authorize('useStudentTutor', AIDashboard::class);
        $user = $request->user();
        abort_if($user === null, 403);

        $student = $context->portalStudentFor($user);
        abort_if($student === null, 422, 'Sin ficha estudiante.');

        $data = $request->validate([
            'topic' => ['required', 'string', 'max:255'],
            'detail' => ['nullable', 'string', 'max:1000'],
        ]);

        return response()->json($coach->adaptiveExplanation($user, $student, $data['topic'], (string) ($data['detail'] ?? '')));
    }
}

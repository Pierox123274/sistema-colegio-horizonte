<?php

namespace App\Http\Controllers;

use App\Services\StudentContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentLearningPathController extends Controller
{
    public function index(Request $request, StudentContextService $context): Response
    {
        $portal = $context->portalContext($request->user());
        $student = $portal['student'];

        $profile = null;
        $recommendations = [];
        if ($student !== null) {
            $p = $student->adaptiveProfile;
            $profile = $p ? [
                'last_classified_level' => $p->last_classified_level,
                'last_diagnostic_score' => $p->last_diagnostic_score,
                'weakness_topics' => $p->weakness_topics ?? [],
                'learning_path' => $p->learning_path ?? [],
                'last_diagnostic_at' => $p->last_diagnostic_at?->toIso8601String(),
            ] : null;
            $recommendations = $student->learningRecommendations()
                ->orderByDesc('priority')
                ->limit(30)
                ->get()
                ->map(fn ($r) => [
                    'id' => $r->id,
                    'title' => $r->title,
                    'body' => $r->body,
                    'topic' => $r->topic,
                    'priority' => $r->priority,
                    'source' => $r->source->value,
                    'estimated_weeks_to_improve' => $r->estimated_weeks_to_improve,
                ])
                ->all();
        }

        return Inertia::render('Student/LearningPath', [
            'portal' => $portal,
            'profile' => $profile,
            'recommendations' => $recommendations,
        ]);
    }
}

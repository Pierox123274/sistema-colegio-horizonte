<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateInstitutionInsightsJob;
use App\Services\AcademicRiskAnalysisService;
use App\Services\AdvancedAIAnalyticsService;
use App\Services\AITutorService;
use App\Support\AIDashboard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class IntranetAIAnalyticsController extends Controller
{
    public function index(
        Request $request,
        AcademicRiskAnalysisService $risk,
        AITutorService $ai,
        AdvancedAIAnalyticsService $analytics,
    ): Response {
        $this->authorize('viewInstitutionAi', AIDashboard::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $key = $ai->institutionInsightCacheKey();
        $payload = Cache::get($key);
        if ($payload === null) {
            $payload = $ai->institutionNarrative($risk->institutionOverview());
            Cache::put($key, $payload, (int) config('ai.cache_ttl_seconds', 3600));
        }

        return Inertia::render('Intranet/AIAnalytics/Index', [
            'payload' => $payload,
            'usage' => $analytics->usageSummary(),
            'ai_enabled' => (bool) config('ai.tutor_enabled'),
            'provider' => config('ai.provider'),
            'modules' => config('ai.modules', []),
        ]);
    }

    public function refresh(Request $request): RedirectResponse
    {
        $this->authorize('viewInstitutionAi', AIDashboard::class);

        dispatch(new GenerateInstitutionInsightsJob);

        return redirect()
            ->route('intranet.ai-analytics.index')
            ->with('success', 'Se encoló la regeneración de métricas IA institucionales.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\AdaptiveAnalyticsService;
use App\Support\AdaptiveLearningDashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntranetAdaptiveAnalyticsController extends Controller
{
    public function index(Request $request, AdaptiveAnalyticsService $analytics): Response
    {
        $this->authorize('viewInstitutionAdaptive', new AdaptiveLearningDashboard);

        return Inertia::render('Intranet/AdaptiveAnalytics/Index', [
            'overview' => $analytics->institutionOverview(),
        ]);
    }
}

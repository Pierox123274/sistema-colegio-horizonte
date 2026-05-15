<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Support\AnalyticsDashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherAnalyticsController extends Controller
{
    public function __construct(
        private readonly AnalyticsService $analytics
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewTeacher', AnalyticsDashboard::class);

        $filters = $this->analytics->normalizeFilters($request->all());
        $user = $request->user();
        abort_if($user === null, 403);

        return Inertia::render('Teacher/Analytics/Index', $this->analytics->teacherPayload($user, $filters));
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\IntranetRole;
use App\Services\LMSDashboardService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntranetLMSOverviewController extends Controller
{
    public function index(Request $request, LMSDashboardService $dashboard): Response
    {
        $user = $request->user();
        abort_if($user === null || ! $user->hasRole(IntranetRole::Administrador->value), 403);

        return Inertia::render('Intranet/LMS/Overview', [
            'overview' => $dashboard->institutionOverview(),
        ]);
    }
}

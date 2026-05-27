<?php

namespace App\Http\Controllers;

use App\Services\GamificationService;
use App\Support\GamificationDashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntranetGamificationController extends Controller
{
    public function index(Request $request, GamificationService $gamification): Response
    {
        $this->authorize('viewInstitution', GamificationDashboard::class);

        return Inertia::render('Intranet/Gamification/Index', [
            'overview' => $gamification->institutionalOverview(),
        ]);
    }
}

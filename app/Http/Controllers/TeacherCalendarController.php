<?php

namespace App\Http\Controllers;

use App\Services\LMSCalendarService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherCalendarController extends Controller
{
    public function index(Request $request, LMSCalendarService $calendar): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $from = now()->startOfMonth()->toDateTimeString();
        $to = now()->endOfMonth()->toDateTimeString();

        $events = $calendar->eventsForTeacher($user, $from, $to)->map(fn ($e) => [
            'id' => $e->id,
            'title' => $e->title,
            'event_type' => $e->event_type->value,
            'starts_at' => $e->starts_at->toIso8601String(),
            'ends_at' => $e->ends_at?->toIso8601String(),
        ]);

        return Inertia::render('Teacher/Calendar/Index', [
            'events' => $events,
            'view' => $request->query('view', 'month'),
        ]);
    }
}

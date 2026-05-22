<?php

namespace App\Http\Controllers;

use App\Services\LMSCalendarService;
use App\Services\StudentContextService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentCalendarController extends Controller
{
    public function index(
        Request $request,
        StudentContextService $context,
        LMSCalendarService $calendar,
    ): Response {
        $student = $context->portalStudentFor($request->user());
        abort_if($student === null, 403);

        $from = now()->startOfMonth()->toDateTimeString();
        $to = now()->endOfMonth()->toDateTimeString();

        $events = $calendar->eventsForStudent($student, $from, $to)->map(fn ($e) => [
            'id' => $e->id,
            'title' => $e->title,
            'event_type' => $e->event_type->value,
            'starts_at' => $e->starts_at->toIso8601String(),
            'ends_at' => $e->ends_at?->toIso8601String(),
        ]);

        return Inertia::render('Student/Calendar/Index', [
            'events' => $events,
            'view' => $request->query('view', 'month'),
        ]);
    }
}

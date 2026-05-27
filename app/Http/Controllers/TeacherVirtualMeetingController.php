<?php

namespace App\Http\Controllers;

use App\Enums\MeetingProvider;
use App\Enums\MeetingType;
use App\Http\Requests\StoreVirtualMeetingRequest;
use App\Models\VirtualClassroom;
use App\Models\VirtualMeeting;
use App\Services\MeetingAccessService;
use App\Services\VirtualClassroomAccessService;
use App\Services\VirtualMeetingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherVirtualMeetingController extends Controller
{
    public function index(
        Request $request,
        MeetingAccessService $access,
        VirtualMeetingService $meetings,
    ): Response {
        $this->authorize('viewAny', VirtualMeeting::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $upcoming = $meetings->upcomingPayloadFor($user, 10);
        $history = $access->queryMeetingsForUser($user)
            ->where('ends_at', '<', now())
            ->with(['host:id,name'])
            ->limit(15)
            ->get()
            ->map(fn (VirtualMeeting $m) => $meetings->presentMeeting($m, $user));

        return Inertia::render('Teacher/Meetings/Index', [
            'upcoming' => $upcoming,
            'history' => $history,
            'can_create' => $user->can('create', VirtualMeeting::class),
        ]);
    }

    public function create(
        Request $request,
        VirtualClassroomAccessService $classroomAccess,
    ): Response {
        $this->authorize('create', VirtualMeeting::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $classrooms = $classroomAccess->queryClassroomsForTeacher($user)
            ->with(['subject:id,name', 'section:id,name'])
            ->get()
            ->map(fn (VirtualClassroom $c) => [
                'id' => $c->id,
                'title' => $c->title,
                'label' => trim(($c->subject?->name ?? '').' — '.($c->section?->name ?? '').' — '.$c->title),
            ]);

        $fallbackCode = config('meetings.google_meet.configured_room_code');

        return Inertia::render('Teacher/Meetings/Create', [
            'classrooms' => $classrooms,
            'catalog' => [
                'providers' => MeetingProvider::options(),
                'types' => MeetingType::options(),
            ],
            'google_meet_fallback_available' => is_string($fallbackCode) && $fallbackCode !== '',
        ]);
    }

    public function store(StoreVirtualMeetingRequest $request, VirtualMeetingService $meetings): RedirectResponse
    {
        $this->authorize('create', VirtualMeeting::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $meeting = $meetings->create($user, $request->validated());

        return redirect()
            ->route('teacher.meetings.show', $meeting)
            ->with('success', 'Videoclase programada correctamente.');
    }

    public function show(
        Request $request,
        VirtualMeeting $meeting,
        VirtualMeetingService $meetings,
    ): Response {
        $this->authorize('view', $meeting);

        $user = $request->user();
        abort_if($user === null, 403);

        return Inertia::render('Teacher/Meetings/Show', [
            'meeting' => $meetings->presentMeeting($meeting, $user),
            'participants_count' => $meeting->participants()->count(),
            'attendances_count' => $meeting->attendances()->count(),
        ]);
    }

    public function join(
        Request $request,
        VirtualMeeting $meeting,
        VirtualMeetingService $meetings,
    ): RedirectResponse {
        $this->authorize('join', $meeting);

        $user = $request->user();
        abort_if($user === null, 403);

        $meetings->recordJoin($user, $meeting);

        return redirect()->away($meeting->join_url);
    }

    public function start(
        Request $request,
        VirtualMeeting $meeting,
        VirtualMeetingService $meetings,
    ): RedirectResponse {
        $this->authorize('update', $meeting);

        $user = $request->user();
        abort_if($user === null, 403);

        $meetings->start($user, $meeting);

        return redirect()
            ->route('teacher.meetings.show', $meeting)
            ->with('success', 'Reunión iniciada.');
    }

    public function cancel(
        Request $request,
        VirtualMeeting $meeting,
        VirtualMeetingService $meetings,
    ): RedirectResponse {
        $this->authorize('cancel', $meeting);

        $user = $request->user();
        abort_if($user === null, 403);

        $meetings->cancel($user, $meeting, $request->input('reason'));

        return redirect()
            ->route('teacher.meetings.index')
            ->with('success', 'Reunión cancelada.');
    }
}

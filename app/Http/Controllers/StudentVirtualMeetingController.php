<?php

namespace App\Http\Controllers;

use App\Models\VirtualMeeting;
use App\Services\MeetingAccessService;
use App\Services\VirtualMeetingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentVirtualMeetingController extends Controller
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
            ->limit(10)
            ->get()
            ->map(fn (VirtualMeeting $m) => $meetings->presentMeeting($m, $user));

        return Inertia::render('Student/Meetings/Index', [
            'upcoming' => $upcoming,
            'history' => $history,
        ]);
    }

    public function show(
        Request $request,
        VirtualMeeting $meeting,
        VirtualMeetingService $meetings,
    ): Response {
        $this->authorize('view', $meeting);

        $user = $request->user();
        abort_if($user === null, 403);

        return Inertia::render('Student/Meetings/Show', [
            'meeting' => $meetings->presentMeeting($meeting, $user),
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
}

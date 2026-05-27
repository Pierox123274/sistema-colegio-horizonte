<?php

namespace App\Http\Controllers\Intranet;

use App\Enums\MeetingProvider;
use App\Enums\MeetingStatus;
use App\Enums\MeetingType;
use App\Http\Controllers\Controller;
use App\Models\VirtualMeeting;
use App\Services\MeetingAccessService;
use App\Services\VirtualMeetingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntranetVirtualMeetingController extends Controller
{
    public function index(
        Request $request,
        MeetingAccessService $access,
        VirtualMeetingService $meetings,
    ): Response {
        $this->authorize('viewAny', VirtualMeeting::class);

        $user = $request->user();
        abort_if($user === null, 403);

        $list = $access->queryMeetingsForUser($user)
            ->with(['host:id,name', 'virtualClassroom:id,title'])
            ->paginate(20)
            ->through(fn (VirtualMeeting $m) => $meetings->presentMeeting($m, $user));

        return Inertia::render('Intranet/Meetings/Index', [
            'meetings' => $list,
            'metrics' => $meetings->institutionMetrics(),
            'catalog' => [
                'providers' => MeetingProvider::options(),
                'types' => MeetingType::options(),
                'statuses' => MeetingStatus::options(),
            ],
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

        $meeting->load(['participants.user:id,name', 'attendances.user:id,name']);

        return Inertia::render('Intranet/Meetings/Show', [
            'meeting' => $meetings->presentMeeting($meeting, $user),
            'participants' => $meeting->participants->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->user?->name,
                'role' => $p->role->value,
            ]),
            'attendances' => $meeting->attendances->map(fn ($a) => [
                'user' => $a->user?->name,
                'joined_at' => $a->joined_at->format('d/m/Y H:i'),
                'duration_seconds' => $a->duration_seconds,
            ]),
        ]);
    }
}

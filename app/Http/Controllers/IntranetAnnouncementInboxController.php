<?php

namespace App\Http\Controllers;

use App\Enums\AnnouncementPriority;
use App\Models\Announcement;
use App\Services\AnnouncementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class IntranetAnnouncementInboxController extends Controller
{
    public function __construct(
        private readonly AnnouncementService $announcements
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $filters = [
            'search' => (string) $request->query('search', ''),
            'priority' => (string) $request->query('priority', ''),
            'unread_only' => (string) $request->query('unread_only', ''),
        ];

        $paginator = $this->announcements->paginateForUser($user, $filters);
        $paginator->getCollection()->transform(
            fn (Announcement $a) => $this->announcements->cardPayload($a, $user)
        );

        return Inertia::render('Intranet/Announcements/Inbox', [
            'announcements' => $paginator,
            'filters' => $filters,
            'unread_count' => $this->announcements->unreadCountFor($user),
            'catalog' => ['priorities' => AnnouncementPriority::options()],
        ]);
    }

    public function show(Request $request, Announcement $announcement): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->authorize('view', $announcement);
        $this->announcements->markAsRead($announcement, $user);

        return Inertia::render('Intranet/Announcements/InboxShow', [
            'announcement' => $this->announcements->detailPayload($announcement, $user),
            'back_href' => route('intranet.announcements.inbox.index', absolute: false),
        ]);
    }

    public function markRead(Request $request, Announcement $announcement): RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->authorize('markRead', $announcement);
        $this->announcements->markAsRead($announcement, $user);

        return back();
    }
}

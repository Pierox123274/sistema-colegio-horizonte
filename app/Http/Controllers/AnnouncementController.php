<?php

namespace App\Http\Controllers;

use App\Enums\AnnouncementAudienceType;
use App\Enums\AnnouncementPriority;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Http\Requests\Intranet\StoreAnnouncementRequest;
use App\Http\Requests\Intranet\UpdateAnnouncementRequest;
use App\Jobs\NotifyAnnouncementPublishedJob;
use App\Models\Announcement;
use App\Models\User;
use App\Services\AnnouncementService;
use App\Services\UserNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AnnouncementController extends Controller
{
    public function __construct(
        private readonly AnnouncementService $announcements
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Announcement::class);

        $paginator = $this->announcements->paginateForAdmin([
            'search' => (string) $request->query('search', ''),
            'priority' => (string) $request->query('priority', ''),
            'audience_type' => (string) $request->query('audience_type', ''),
            'is_active' => (string) $request->query('is_active', ''),
            'status' => (string) $request->query('status', ''),
        ]);

        return Inertia::render('Intranet/Announcements/Index', [
            'announcements' => $paginator->through(fn (Announcement $a): array => [
                'id' => $a->id,
                'title' => $a->title,
                'priority' => $a->priority->value,
                'audience_type' => $a->audience_type->value,
                'starts_at' => $a->starts_at->toIso8601String(),
                'ends_at' => $a->ends_at?->toIso8601String(),
                'is_active' => $a->is_active,
                'reads_count' => $a->reads_count,
                'created_by' => $a->createdBy?->only(['id', 'name']),
            ]),
            'filters' => [
                'search' => $request->query('search', ''),
                'priority' => $request->query('priority', ''),
                'audience_type' => $request->query('audience_type', ''),
                'is_active' => $request->query('is_active', ''),
                'status' => $request->query('status', ''),
            ],
            'catalog' => [
                'priorities' => AnnouncementPriority::options(),
                'audiences' => AnnouncementAudienceType::options(),
                'statuses' => [
                    ['value' => '', 'label' => 'Todos'],
                    ['value' => 'active', 'label' => 'Vigentes'],
                    ['value' => 'scheduled', 'label' => 'Programados'],
                    ['value' => 'expired', 'label' => 'Expirados'],
                    ['value' => 'inactive', 'label' => 'Inactivos'],
                ],
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Announcement::class);

        return Inertia::render('Intranet/Announcements/Create', [
            'catalog' => $this->formCatalog(),
        ]);
    }

    public function store(StoreAnnouncementRequest $request): RedirectResponse
    {
        $data = $this->mapValidated($request->validated());

        $announcement = $this->announcements->create(
            $request->user(),
            $data,
            array_map('intval', $request->input('recipient_user_ids', [])),
            $request->file('attachment'),
        );

        dispatch(new NotifyAnnouncementPublishedJob($announcement->id))->afterCommit();

        app(UserNotificationService::class)->notifyMany(
            users: $this->announcements->audienceUsersForAnnouncement($announcement),
            title: 'Nuevo comunicado institucional',
            message: $announcement->title,
            category: NotificationCategory::System,
            priority: NotificationPriority::High,
            actionUrl: route('intranet.announcements.inbox.show', $announcement, absolute: false),
            actionLabel: 'Ver comunicado',
            mailTemplate: 'institutional-notification',
            meta: ['announcement_id' => $announcement->id]
        );

        return redirect()
            ->route('intranet.announcements.index')
            ->with('success', 'Comunicado publicado correctamente.');
    }

    public function show(Announcement $announcement): Response
    {
        $this->authorize('view', $announcement);

        $announcement->load(['createdBy:id,name,email', 'recipients:id,name,email']);

        $user = request()->user();
        abort_if($user === null, 403);

        return Inertia::render('Intranet/Announcements/Show', [
            'announcement' => $this->serializeAdmin($announcement),
            'statistics' => $this->announcements->statisticsFor($announcement),
            'can_manage' => $user->can('update', $announcement),
        ]);
    }

    public function edit(Announcement $announcement): Response
    {
        $this->authorize('update', $announcement);

        $announcement->load(['recipients:id,name,email']);

        return Inertia::render('Intranet/Announcements/Edit', [
            'announcement' => $this->serializeAdmin($announcement),
            'catalog' => $this->formCatalog(),
        ]);
    }

    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): RedirectResponse
    {
        $data = $this->mapValidated($request->validated());

        $this->announcements->update(
            $announcement,
            $data,
            array_map('intval', $request->input('recipient_user_ids', [])),
            $request->file('attachment'),
            $request->boolean('remove_attachment'),
        );

        return redirect()
            ->route('intranet.announcements.show', $announcement)
            ->with('success', 'Comunicado actualizado correctamente.');
    }

    public function destroy(Announcement $announcement): RedirectResponse
    {
        $this->authorize('delete', $announcement);

        $announcement->delete();

        return redirect()
            ->route('intranet.announcements.index')
            ->with('success', 'Comunicado eliminado.');
    }

    public function deactivate(Announcement $announcement): RedirectResponse
    {
        $this->authorize('update', $announcement);

        $this->announcements->deactivate($announcement);

        return back()->with('success', 'Comunicado desactivado.');
    }

    public function resend(Request $request, Announcement $announcement): RedirectResponse
    {
        $this->authorize('update', $announcement);

        $user = $request->user();
        abort_if($user === null, 403);

        $copy = $this->announcements->resend($announcement, $user);

        return redirect()
            ->route('intranet.announcements.show', $copy)
            ->with('success', 'Comunicado reenviado como nueva publicación.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formCatalog(): array
    {
        return [
            'priorities' => AnnouncementPriority::options(),
            'audiences' => AnnouncementAudienceType::options(),
            'users' => User::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email'])
                ->map(fn (User $u): array => [
                    'value' => (string) $u->id,
                    'label' => $u->name.' · '.$u->email,
                ])
                ->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $validated
     * @return array<string, mixed>
     */
    private function mapValidated(array $validated): array
    {
        return [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'priority' => $validated['priority'],
            'audience_type' => $validated['audience_type'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => (bool) ($validated['is_active'] ?? true),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function serializeAdmin(Announcement $announcement): array
    {
        return [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'content' => $announcement->content,
            'priority' => $announcement->priority->value,
            'audience_type' => $announcement->audience_type->value,
            'starts_at' => $announcement->starts_at->format('Y-m-d\TH:i'),
            'ends_at' => $announcement->ends_at?->format('Y-m-d\TH:i'),
            'is_active' => $announcement->is_active,
            'has_attachment' => $announcement->has_attachment,
            'attachment_url' => $announcement->has_attachment
                ? Storage::disk('public')->url($announcement->attachment_path ?? '')
                : null,
            'attachment_mime' => $announcement->attachment_mime,
            'attachment_original_name' => $announcement->attachment_original_name,
            'created_by' => $announcement->createdBy?->only(['id', 'name', 'email']),
            'recipients' => $announcement->recipients->map(fn (User $u) => $u->only(['id', 'name', 'email']))->all(),
            'reads_count' => $announcement->reads_count ?? $announcement->reads()->count(),
        ];
    }
}

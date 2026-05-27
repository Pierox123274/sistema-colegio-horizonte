<?php

namespace App\Services;

use App\Enums\AnnouncementAudienceType;
use App\Enums\AnnouncementPriority;
use App\Enums\IntranetRole;
use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

final class AnnouncementService
{
    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $recipientUserIds
     */
    public function create(User $author, array $data, array $recipientUserIds = [], ?UploadedFile $attachment = null): Announcement
    {
        return DB::transaction(function () use ($author, $data, $recipientUserIds, $attachment): Announcement {
            $announcement = Announcement::query()->create([
                ...$data,
                'created_by_user_id' => $author->id,
                'has_attachment' => false,
            ]);

            $this->syncRecipients($announcement, $recipientUserIds);
            $this->storeAttachment($announcement, $attachment);

            return $announcement->fresh(['createdBy:id,name', 'recipients:id,name,email']);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     * @param  list<int>  $recipientUserIds
     */
    public function update(Announcement $announcement, array $data, array $recipientUserIds = [], ?UploadedFile $attachment = null, bool $removeAttachment = false): Announcement
    {
        return DB::transaction(function () use ($announcement, $data, $recipientUserIds, $attachment, $removeAttachment): Announcement {
            $announcement->update($data);

            $this->syncRecipients($announcement, $recipientUserIds);

            if ($removeAttachment) {
                $this->deleteAttachment($announcement);
            }

            $this->storeAttachment($announcement, $attachment);

            return $announcement->fresh(['createdBy:id,name', 'recipients:id,name,email']);
        });
    }

    public function deactivate(Announcement $announcement): void
    {
        $announcement->update(['is_active' => false]);
    }

    public function resend(Announcement $announcement, User $author): Announcement
    {
        $copy = $announcement->replicate([
            'is_active',
        ]);
        $copy->starts_at = now();
        $copy->ends_at = $announcement->ends_at;
        $copy->created_by_user_id = $author->id;
        $copy->is_active = true;
        $copy->save();

        if ($announcement->audience_type === AnnouncementAudienceType::CustomUsers) {
            $copy->recipients()->sync($announcement->recipients()->pluck('id'));
        }

        if ($announcement->has_attachment && $announcement->attachment_path !== null) {
            $this->copyAttachment($announcement, $copy);
        }

        return $copy->fresh(['createdBy:id,name', 'recipients:id,name,email']);
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function paginateForAdmin(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Announcement::query()
            ->with('createdBy:id,name')
            ->withCount('reads')
            ->orderByDesc('starts_at')
            ->orderByDesc('id');

        if ($search = trim($filters['search'] ?? '')) {
            $like = '%'.$search.'%';
            $query->where(function (Builder $q) use ($like): void {
                $q->where('title', 'like', $like)
                    ->orWhere('content', 'like', $like);
            });
        }

        if ($priority = $filters['priority'] ?? '') {
            if (in_array($priority, AnnouncementPriority::values(), true)) {
                $query->where('priority', $priority);
            }
        }

        if ($audience = $filters['audience_type'] ?? '') {
            if (in_array($audience, AnnouncementAudienceType::values(), true)) {
                $query->where('audience_type', $audience);
            }
        }

        if (($filters['is_active'] ?? '') !== '') {
            $query->where('is_active', $filters['is_active'] === '1');
        }

        if ($status = $filters['status'] ?? '') {
            $now = now();
            if ($status === 'scheduled') {
                $query->where('is_active', true)->where('starts_at', '>', $now);
            } elseif ($status === 'active') {
                $query->where('is_active', true)
                    ->where('starts_at', '<=', $now)
                    ->where(function (Builder $q) use ($now): void {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
                    });
            } elseif ($status === 'expired') {
                $query->whereNotNull('ends_at')->where('ends_at', '<', $now);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function paginateForUser(User $user, array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->visibleToUserQuery($user)
            ->with('createdBy:id,name')
            ->withExists(['reads as viewer_has_read' => fn (Builder $q) => $q->where('user_id', $user->id)])
            ->orderByDesc('starts_at')
            ->orderByDesc('id');

        $this->applyUserFilters($query, $user, $filters);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @return Collection<int, Announcement>
     */
    public function recentForUser(User $user, int $limit = 5): Collection
    {
        return $this->visibleToUserQuery($user)
            ->with('createdBy:id,name')
            ->withExists(['reads as viewer_has_read' => fn (Builder $q) => $q->where('user_id', $user->id)])
            ->orderByDesc('starts_at')
            ->limit($limit)
            ->get();
    }

    public function unreadCountFor(User $user): int
    {
        return $this->visibleToUserQuery($user)
            ->whereDoesntHave('reads', fn (Builder $q) => $q->where('user_id', $user->id))
            ->count();
    }

    /**
     * @return array{unread_count: int, recent: list<array<string, mixed>>}
     */
    public function headerPayloadFor(User $user): array
    {
        $recent = $this->recentForUser($user, 6);

        return [
            'unread_count' => $this->unreadCountFor($user),
            'recent' => $recent->map(fn (Announcement $a) => $this->cardPayload($a, $user))->all(),
            'index_href' => $this->indexRouteFor($user),
        ];
    }

    public function markAsRead(Announcement $announcement, User $user): void
    {
        if (! $this->userCanView($announcement, $user)) {
            abort(403, 'No tiene acceso a este comunicado.');
        }

        AnnouncementRead::query()->updateOrCreate(
            [
                'announcement_id' => $announcement->id,
                'user_id' => $user->id,
            ],
            ['read_at' => now()],
        );
    }

    public function userCanView(Announcement $announcement, User $user): bool
    {
        if (! $announcement->isPublishedNow()) {
            return $user->hasRole(IntranetRole::Administrador->value);
        }

        return $this->visibleToUserQuery($user)
            ->where('announcements.id', $announcement->id)
            ->exists();
    }

    /**
     * @return array<string, mixed>
     */
    public function statisticsFor(Announcement $announcement): array
    {
        $audienceSize = $this->estimatedAudienceCount($announcement);
        $readCount = $announcement->reads()->count();

        return [
            'audience_size' => $audienceSize,
            'read_count' => $readCount,
            'unread_count' => max(0, $audienceSize - $readCount),
            'read_percentage' => $audienceSize > 0
                ? round(($readCount / $audienceSize) * 100, 1)
                : 0,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function cardPayload(Announcement $announcement, User $viewer): array
    {
        $isRead = isset($announcement->viewer_has_read)
            ? (bool) $announcement->viewer_has_read
            : $announcement->reads()->where('user_id', $viewer->id)->exists();

        return [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'content_excerpt' => Str::limit(strip_tags($announcement->content), 140),
            'priority' => $announcement->priority->value,
            'priority_label' => collect(AnnouncementPriority::options())
                ->firstWhere('value', $announcement->priority->value)['label'] ?? $announcement->priority->value,
            'audience_type' => $announcement->audience_type->value,
            'audience_label' => collect(AnnouncementAudienceType::options())
                ->firstWhere('value', $announcement->audience_type->value)['label'] ?? $announcement->audience_type->value,
            'starts_at' => $announcement->starts_at->toIso8601String(),
            'starts_at_label' => $announcement->starts_at->translatedFormat('d/m/Y H:i'),
            'ends_at' => $announcement->ends_at?->toIso8601String(),
            'ends_at_label' => $announcement->ends_at?->translatedFormat('d/m/Y H:i'),
            'has_attachment' => $announcement->has_attachment,
            'is_read' => $isRead,
            'is_active' => $announcement->is_active,
            'created_by' => $announcement->createdBy?->only(['id', 'name']),
            'show_href' => $this->showRouteFor($viewer, $announcement),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function detailPayload(Announcement $announcement, User $viewer): array
    {
        return [
            ...$this->cardPayload($announcement, $viewer),
            'content' => $announcement->content,
            'attachment_url' => $announcement->has_attachment
                ? Storage::disk('public')->url($announcement->attachment_path ?? '')
                : null,
            'attachment_mime' => $announcement->attachment_mime,
            'attachment_original_name' => $announcement->attachment_original_name,
            'is_image_attachment' => $announcement->attachment_mime !== null
                && str_starts_with($announcement->attachment_mime, 'image/'),
            'is_pdf_attachment' => $announcement->attachment_mime === 'application/pdf',
        ];
    }

    public function indexRouteFor(User $user): string
    {
        if ($user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)) {
            return route('teacher.announcements.index', absolute: false);
        }
        if ($user->hasRole(IntranetRole::Estudiante->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)) {
            return route('student.announcements.index', absolute: false);
        }
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return route('intranet.announcements.index', absolute: false);
        }

        return route('intranet.announcements.inbox.index', absolute: false);
    }

    public function showRouteFor(User $user, Announcement $announcement): string
    {
        if ($user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)) {
            return route('teacher.announcements.show', $announcement, absolute: false);
        }
        if ($user->hasRole(IntranetRole::Estudiante->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)) {
            return route('student.announcements.show', $announcement, absolute: false);
        }
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return route('intranet.announcements.show', $announcement, absolute: false);
        }

        return route('intranet.announcements.inbox.show', $announcement, absolute: false);
    }

    public function markReadRouteFor(User $user, Announcement $announcement): string
    {
        if ($user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)) {
            return route('teacher.announcements.read', $announcement, absolute: false);
        }
        if ($user->hasRole(IntranetRole::Estudiante->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)) {
            return route('student.announcements.read', $announcement, absolute: false);
        }

        return route('intranet.announcements.inbox.read', $announcement, absolute: false);
    }

    /**
     * @return Builder<Announcement>
     */
    public function visibleToUserQuery(User $user): Builder
    {
        $now = now();

        return Announcement::query()
            ->where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where(function (Builder $q) use ($now): void {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
            })
            ->where(function (Builder $q) use ($user): void {
                $q->where('audience_type', AnnouncementAudienceType::All->value);

                foreach (AnnouncementAudienceType::cases() as $audience) {
                    $role = $audience->roleName();
                    if ($role !== null && $user->hasRole($role)) {
                        $q->orWhere('audience_type', $audience->value);
                    }
                }

                $q->orWhere(function (Builder $inner) use ($user): void {
                    $inner->where('audience_type', AnnouncementAudienceType::CustomUsers->value)
                        ->whereHas('recipients', fn (Builder $r) => $r->where('users.id', $user->id));
                });
            });
    }

    public function estimatedAudienceCount(Announcement $announcement): int
    {
        if ($announcement->audience_type === AnnouncementAudienceType::CustomUsers) {
            return $announcement->recipients()->count();
        }

        if ($announcement->audience_type === AnnouncementAudienceType::All) {
            return User::query()->where('is_active', true)->count();
        }

        $role = $announcement->audience_type->roleName();
        if ($role === null) {
            return 0;
        }

        return User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn (Builder $q) => $q->where('name', $role))
            ->count();
    }

    /**
     * @param  Builder<Announcement>  $query
     * @param  array<string, string>  $filters
     */
    private function applyUserFilters(Builder $query, User $user, array $filters): void
    {
        if ($search = trim($filters['search'] ?? '')) {
            $like = '%'.$search.'%';
            $query->where(function (Builder $q) use ($like): void {
                $q->where('title', 'like', $like)
                    ->orWhere('content', 'like', $like);
            });
        }

        if ($priority = $filters['priority'] ?? '') {
            if (in_array($priority, AnnouncementPriority::values(), true)) {
                $query->where('priority', $priority);
            }
        }

        if (($filters['unread_only'] ?? '') === '1') {
            $query->whereDoesntHave('reads', fn (Builder $q) => $q->where('user_id', $user->id));
        }
    }

    /**
     * @param  list<int>  $recipientUserIds
     */
    private function syncRecipients(Announcement $announcement, array $recipientUserIds): void
    {
        if ($announcement->audience_type !== AnnouncementAudienceType::CustomUsers) {
            $announcement->recipients()->detach();

            return;
        }

        $announcement->recipients()->sync(
            collect($recipientUserIds)->map(fn (int $id): int => $id)->unique()->values()->all()
        );
    }

    private function storeAttachment(Announcement $announcement, ?UploadedFile $attachment): void
    {
        if ($attachment === null) {
            return;
        }

        $path = $attachment->store('announcements/'.$announcement->id, 'public');

        $announcement->update([
            'has_attachment' => true,
            'attachment_path' => $path,
            'attachment_mime' => $attachment->getMimeType(),
            'attachment_original_name' => $attachment->getClientOriginalName(),
        ]);
    }

    private function deleteAttachment(Announcement $announcement): void
    {
        if ($announcement->attachment_path !== null) {
            Storage::disk('public')->delete($announcement->attachment_path);
        }

        $announcement->update([
            'has_attachment' => false,
            'attachment_path' => null,
            'attachment_mime' => null,
            'attachment_original_name' => null,
        ]);
    }

    private function copyAttachment(Announcement $source, Announcement $target): void
    {
        if ($source->attachment_path === null) {
            return;
        }

        $extension = pathinfo($source->attachment_path, PATHINFO_EXTENSION);
        $newPath = 'announcements/'.$target->id.'/'.Str::uuid().($extension ? '.'.$extension : '');

        if (Storage::disk('public')->copy($source->attachment_path, $newPath)) {
            $target->update([
                'has_attachment' => true,
                'attachment_path' => $newPath,
                'attachment_mime' => $source->attachment_mime,
                'attachment_original_name' => $source->attachment_original_name,
            ]);
        }
    }

    /**
     * @return Collection<int, User>
     */
    public function audienceUsersForAnnouncement(Announcement $announcement): Collection
    {
        if ($announcement->audience_type === AnnouncementAudienceType::CustomUsers) {
            return $announcement->recipients()->where('is_active', true)->get();
        }

        if ($announcement->audience_type === AnnouncementAudienceType::All) {
            return User::query()->where('is_active', true)->get();
        }

        $role = $announcement->audience_type->roleName();
        if ($role === null) {
            return collect();
        }

        return User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn (Builder $q) => $q->where('name', $role))
            ->get();
    }
}

<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditSeverity;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\User;
use App\Models\UserNotificationPreference;
use App\Notifications\InstitutionalCommunicationNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;

final class UserNotificationService
{
    public function __construct(
        private readonly AuditService $audit
    ) {}

    public function preferenceFor(User $user): UserNotificationPreference
    {
        return UserNotificationPreference::query()->firstOrCreate(
            ['user_id' => $user->id],
            [
                'in_app_enabled' => true,
                'email_enabled' => true,
                'frequency' => 'immediate',
                'category_settings' => $this->defaultCategorySettings(),
            ]
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updatePreference(User $user, array $data): UserNotificationPreference
    {
        $preference = $this->preferenceFor($user);
        $preference->fill([
            'in_app_enabled' => (bool) ($data['in_app_enabled'] ?? true),
            'email_enabled' => (bool) ($data['email_enabled'] ?? true),
            'frequency' => (string) ($data['frequency'] ?? 'immediate'),
            'category_settings' => $data['category_settings'] ?? $this->defaultCategorySettings(),
        ]);
        $preference->save();

        return $preference;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public function notifyUser(
        User $user,
        string $title,
        string $message,
        NotificationCategory $category,
        NotificationPriority $priority = NotificationPriority::Medium,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        bool $forceEmail = false,
        bool $forceInApp = false,
        array $meta = [],
        ?string $mailTemplate = null,
    ): void {
        $preference = $this->preferenceFor($user);
        $channels = $this->resolveChannels($preference, $category, $forceEmail, $forceInApp);
        if ($channels === []) {
            return;
        }

        $user->notify(new InstitutionalCommunicationNotification(
            title: $title,
            message: $message,
            category: $category,
            priority: $priority,
            actionUrl: $actionUrl,
            actionLabel: $actionLabel,
            channels: $channels,
            meta: $meta,
            mailTemplate: $mailTemplate,
        ));

        if ($priority === NotificationPriority::Critical) {
            $this->audit->log(
                AuditAction::Notification,
                AuditModule::Notifications,
                $user,
                DatabaseNotification::class,
                null,
                'Notificación crítica enviada: '.$title,
                null,
                ['category' => $category->value, 'channels' => $channels],
                severity: AuditSeverity::Critical
            );
        }
    }

    /**
     * @param  iterable<User>  $users
     * @param  array<string, mixed>  $meta
     */
    public function notifyMany(
        iterable $users,
        string $title,
        string $message,
        NotificationCategory $category,
        NotificationPriority $priority = NotificationPriority::Medium,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        bool $forceEmail = false,
        bool $forceInApp = false,
        array $meta = [],
        ?string $mailTemplate = null,
    ): void {
        foreach ($users as $user) {
            $this->notifyUser(
                user: $user,
                title: $title,
                message: $message,
                category: $category,
                priority: $priority,
                actionUrl: $actionUrl,
                actionLabel: $actionLabel,
                forceEmail: $forceEmail,
                forceInApp: $forceInApp,
                meta: $meta,
                mailTemplate: $mailTemplate,
            );
        }
    }

    /**
     * @return array{unread_count: int, recent: list<array<string, mixed>>, center_href: string}
     */
    public function headerPayloadFor(User $user, int $limit = 8): array
    {
        $recent = $user->notifications()
            ->latest()
            ->limit($limit)
            ->get();

        return [
            'unread_count' => $user->unreadNotifications()->count(),
            'recent' => $recent->map(fn (DatabaseNotification $notification): array => $this->serialize($notification))->all(),
            'center_href' => route('notifications.index', absolute: false),
        ];
    }

    public function markRead(User $user, string $notificationId): void
    {
        $notification = $user->notifications()->whereKey($notificationId)->firstOrFail();
        if ($notification->read_at === null) {
            $notification->markAsRead();
        }
    }

    public function markAllRead(User $user): void
    {
        $user->unreadNotifications->markAsRead();
    }

    /**
     * @return Collection<int, DatabaseNotification>
     */
    public function listFor(User $user, ?string $category = null, ?string $status = null, int $limit = 40): Collection
    {
        $query = $user->notifications()->latest();
        if ($category !== null && $category !== '') {
            $query->where('data->category', $category);
        }
        if ($status === 'unread') {
            $query->whereNull('read_at');
        }
        if ($status === 'read') {
            $query->whereNotNull('read_at');
        }

        return $query->limit($limit)->get();
    }

    /**
     * @return array<string, mixed>
     */
    public function serialize(DatabaseNotification $notification): array
    {
        $data = $notification->data;

        return [
            'id' => $notification->id,
            'title' => (string) ($data['title'] ?? 'Notificación'),
            'message' => (string) ($data['message'] ?? ''),
            'category' => (string) ($data['category'] ?? NotificationCategory::System->value),
            'priority' => (string) ($data['priority'] ?? NotificationPriority::Medium->value),
            'action_url' => $data['action_url'] ?? null,
            'action_label' => $data['action_label'] ?? null,
            'read_at' => $notification->read_at?->toIso8601String(),
            'is_read' => $notification->read_at !== null,
            'created_at' => $notification->created_at?->toIso8601String(),
            'created_at_label' => $notification->created_at?->diffForHumans(),
        ];
    }

    /**
     * @return array<string, bool>
     */
    private function defaultCategorySettings(): array
    {
        return collect(NotificationCategory::cases())
            ->mapWithKeys(fn (NotificationCategory $category): array => [$category->value => true])
            ->all();
    }

    /**
     * @return list<string>
     */
    private function resolveChannels(
        UserNotificationPreference $preference,
        NotificationCategory $category,
        bool $forceEmail,
        bool $forceInApp,
    ): array {
        $settings = $preference->category_settings ?? $this->defaultCategorySettings();
        $categoryEnabled = (bool) ($settings[$category->value] ?? true);
        if (! $categoryEnabled && ! $forceEmail && ! $forceInApp) {
            return [];
        }

        $channels = [];
        if ($forceInApp || $preference->in_app_enabled) {
            $channels[] = 'database';
        }
        if ($forceEmail || $preference->email_enabled) {
            $channels[] = 'mail';
        }

        return array_values(array_unique($channels));
    }
}

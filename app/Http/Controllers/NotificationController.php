<?php

namespace App\Http\Controllers;

use App\Enums\NotificationCategory;
use App\Services\UserNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function __construct(
        private readonly UserNotificationService $notifications
    ) {}

    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $category = (string) $request->query('category', '');
        $status = (string) $request->query('status', '');
        $rows = $this->notifications->listFor(
            user: $user,
            category: $category !== '' ? $category : null,
            status: $status !== '' ? $status : null
        );

        return Inertia::render('Notifications/Center', [
            'notifications' => $rows->map(fn ($row): array => $this->notifications->serialize($row))->all(),
            'unread_count' => $user->unreadNotifications()->count(),
            'filters' => [
                'category' => $category,
                'status' => $status,
            ],
            'catalog' => [
                'categories' => NotificationCategory::options(),
                'statuses' => [
                    ['value' => '', 'label' => 'Todas'],
                    ['value' => 'unread', 'label' => 'No leídas'],
                    ['value' => 'read', 'label' => 'Leídas'],
                ],
            ],
        ]);
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->notifications->markRead($user, $notification);

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $this->notifications->markAllRead($user);

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }

    public function settings(Request $request): Response
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $preference = $this->notifications->preferenceFor($user);

        return Inertia::render('Notifications/Settings', [
            'preferences' => [
                'in_app_enabled' => $preference->in_app_enabled,
                'email_enabled' => $preference->email_enabled,
                'frequency' => $preference->frequency,
                'category_settings' => $preference->category_settings,
            ],
            'catalog' => [
                'categories' => NotificationCategory::options(),
                'frequencies' => [
                    ['value' => 'immediate', 'label' => 'Inmediata'],
                    ['value' => 'daily_digest', 'label' => 'Resumen diario'],
                ],
            ],
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if($user === null, 403);

        $validated = $request->validate([
            'in_app_enabled' => ['required', 'boolean'],
            'email_enabled' => ['required', 'boolean'],
            'frequency' => ['required', 'string', 'in:immediate,daily_digest'],
            'category_settings' => ['required', 'array'],
        ]);

        $this->notifications->updatePreference($user, $validated);

        return back()->with('success', 'Preferencias de notificación actualizadas.');
    }
}

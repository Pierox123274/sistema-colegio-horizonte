<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class SessionSecurityService
{
    private const DATETIME_DISPLAY_FORMAT = 'd/m/Y H:i';

    public function registerSession(User $user, Request $request): UserSession
    {
        $sessionId = (string) $request->session()->getId();
        $lifetime = config('security.session_lifetime_minutes', 120);

        return UserSession::query()->updateOrCreate(
            ['session_id' => $sessionId],
            [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'device_label' => $this->deviceLabel($request->userAgent()),
                'device_fingerprint' => $this->fingerprint($request),
                'logged_in_at' => now(),
                'last_activity_at' => now(),
                'logged_out_at' => null,
                'expires_at' => now()->addMinutes($lifetime),
                'is_active' => true,
                'is_suspicious' => app(SecurityService::class)->isIpSuspicious($request->ip()),
            ]
        );
    }

    public function touchSession(Request $request): void
    {
        $sessionId = (string) $request->session()->getId();
        $lifetime = config('security.session_lifetime_minutes', 120);

        UserSession::query()
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->update([
                'last_activity_at' => now(),
                'expires_at' => now()->addMinutes($lifetime),
            ]);
    }

    public function terminateSession(Request $request): void
    {
        UserSession::query()
            ->where('session_id', (string) $request->session()->getId())
            ->update([
                'is_active' => false,
                'logged_out_at' => now(),
            ]);
    }

    public function invalidateOtherSessions(User $user, ?string $exceptSessionId = null): int
    {
        $query = UserSession::query()
            ->where('user_id', $user->id)
            ->where('is_active', true);

        if ($exceptSessionId !== null) {
            $query->where('session_id', '!=', $exceptSessionId);
        }

        $ids = (clone $query)->pluck('session_id')->all();

        if ($ids !== []) {
            DB::table('sessions')->whereIn('id', $ids)->delete();
        }

        return $query->update([
            'is_active' => false,
            'logged_out_at' => now(),
        ]);
    }

    public function invalidateAllSessions(User $user): void
    {
        $ids = UserSession::query()
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->pluck('session_id')
            ->all();

        if ($ids !== []) {
            DB::table('sessions')->whereIn('id', $ids)->delete();
        }

        UserSession::query()
            ->where('user_id', $user->id)
            ->update([
                'is_active' => false,
                'logged_out_at' => now(),
            ]);
    }

    public function isSessionValid(Request $request): bool
    {
        $user = $request->user();
        if ($user === null) {
            return true;
        }

        $record = UserSession::query()
            ->where('session_id', (string) $request->session()->getId())
            ->where('user_id', $user->id)
            ->first();

        if ($record === null) {
            return true;
        }

        if (! $record->is_active) {
            return false;
        }

        if ($record->expires_at !== null && $record->expires_at->isPast()) {
            $record->update(['is_active' => false, 'logged_out_at' => now()]);

            return false;
        }

        return true;
    }

    public function activeSessionsCount(): int
    {
        return UserSession::query()->where('is_active', true)->count();
    }

    /**
     * @return array{active: int, suspicious: int, expiring_soon: int}
     */
    public function sessionDashboardStats(): array
    {
        $soon = now()->addMinutes(30);

        return [
            'active' => $this->activeSessionsCount(),
            'suspicious' => UserSession::query()
                ->where('is_active', true)
                ->where('is_suspicious', true)
                ->count(),
            'expiring_soon' => UserSession::query()
                ->where('is_active', true)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<=', $soon)
                ->count(),
        ];
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function paginateActiveSessions(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = UserSession::query()
            ->with('user:id,name,email')
            ->where('is_active', true)
            ->orderByDesc('last_activity_at');

        if ($userId = $filters['user_id'] ?? '') {
            $query->where('user_id', (int) $userId);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeSession(UserSession $session): array
    {
        return [
            'id' => $session->id,
            'user' => $session->user?->only(['id', 'name', 'email']),
            'ip_address' => $session->ip_address,
            'user_agent' => $session->user_agent,
            'device_label' => $session->device_label,
            'is_suspicious' => $session->is_suspicious,
            'logged_in_at' => $session->logged_in_at?->translatedFormat(self::DATETIME_DISPLAY_FORMAT),
            'last_activity_at' => $session->last_activity_at?->translatedFormat(self::DATETIME_DISPLAY_FORMAT),
            'expires_at' => $session->expires_at?->translatedFormat(self::DATETIME_DISPLAY_FORMAT),
        ];
    }

    private function fingerprint(Request $request): string
    {
        return hash('sha256', implode('|', [
            (string) $request->userAgent(),
            (string) $request->ip(),
        ]));
    }

    private function deviceLabel(?string $userAgent): string
    {
        if ($userAgent === null) {
            return 'Dispositivo desconocido';
        }

        $browser = match (true) {
            str_contains($userAgent, 'Edg') => 'Edge',
            str_contains($userAgent, 'Chrome') => 'Chrome',
            str_contains($userAgent, 'Firefox') => 'Firefox',
            str_contains($userAgent, 'Safari') => 'Safari',
            default => 'Navegador',
        };

        $os = match (true) {
            str_contains($userAgent, 'Windows') => 'Windows',
            str_contains($userAgent, 'Android') => 'Android',
            str_contains($userAgent, 'iPhone') => 'iOS',
            str_contains($userAgent, 'Mac') => 'macOS',
            str_contains($userAgent, 'Linux') => 'Linux',
            default => 'SO',
        };

        return "{$browser} · {$os}";
    }
}

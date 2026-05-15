<?php

namespace App\Services;

use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class SecurityService
{
    public function __construct(
        private readonly AuditService $audit
    ) {}

    public function recordLoginAttempt(
        string $email,
        bool $successful,
        ?string $failureReason = null,
        ?User $user = null,
        ?Request $request = null,
    ): LoginAttempt {
        $request ??= request();

        return LoginAttempt::query()->create([
            'email' => $email,
            'user_id' => $user?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'successful' => $successful,
            'failure_reason' => $failureReason,
            'attempted_at' => now(),
        ]);
    }

    /**
     * @throws ValidationException
     */
    public function ensureLoginNotLocked(string $email, ?Request $request = null): void
    {
        $request ??= request();

        if ($this->isIpSuspicious($request->ip())) {
            throw ValidationException::withMessages([
                'email' => 'Acceso temporalmente restringido por actividad sospechosa. Intente más tarde.',
            ]);
        }

        $max = config('security.login_max_attempts', 5);
        $minutes = config('security.login_lockout_minutes', 15);

        $recentFailures = LoginAttempt::query()
            ->where('email', $email)
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subMinutes($minutes))
            ->count();

        if ($recentFailures >= $max) {
            throw ValidationException::withMessages([
                'email' => "Demasiados intentos fallidos. Espere {$minutes} minutos antes de reintentar.",
            ]);
        }
    }

    public function isIpSuspicious(?string $ip): bool
    {
        if ($ip === null) {
            return false;
        }

        $window = config('security.suspicious_ip_window_minutes', 60);
        $threshold = config('security.suspicious_ip_attempts', 20);

        $failures = LoginAttempt::query()
            ->where('ip_address', $ip)
            ->where('successful', false)
            ->where('attempted_at', '>=', now()->subMinutes($window))
            ->count();

        return $failures >= $threshold;
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function paginateLoginAttempts(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = LoginAttempt::query()
            ->with('user:id,name,email')
            ->orderByDesc('attempted_at');

        if ($email = trim($filters['email'] ?? '')) {
            $query->where('email', 'like', '%'.$email.'%');
        }

        if (($filters['successful'] ?? '') !== '') {
            $query->where('successful', $filters['successful'] === '1');
        }

        if ($ip = trim($filters['ip'] ?? '')) {
            $query->where('ip_address', 'like', '%'.$ip.'%');
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @return array{total: int, failed_today: int, successful_today: int, failed_24h: int}
     */
    public function loginAttemptStats(): array
    {
        return [
            'total' => LoginAttempt::query()->count(),
            'failed_today' => LoginAttempt::query()
                ->where('successful', false)
                ->whereDate('attempted_at', today())
                ->count(),
            'successful_today' => LoginAttempt::query()
                ->where('successful', true)
                ->whereDate('attempted_at', today())
                ->count(),
            'failed_24h' => LoginAttempt::query()
                ->where('successful', false)
                ->where('attempted_at', '>=', now()->subHours(24))
                ->count(),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function accessMonitorSummary(): array
    {
        $since = now()->subHours(24);

        $failed = LoginAttempt::query()
            ->where('successful', false)
            ->where('attempted_at', '>=', $since)
            ->count();

        $successful = LoginAttempt::query()
            ->where('successful', true)
            ->where('attempted_at', '>=', $since)
            ->count();

        $suspiciousIps = LoginAttempt::query()
            ->select('ip_address')
            ->where('successful', false)
            ->where('attempted_at', '>=', $since)
            ->whereNotNull('ip_address')
            ->groupBy('ip_address')
            ->havingRaw('COUNT(*) >= ?', [config('security.suspicious_ip_attempts', 20)])
            ->pluck('ip_address')
            ->all();

        return [
            'failed_last_24h' => $failed,
            'successful_last_24h' => $successful,
            'suspicious_ips' => $suspiciousIps,
            'active_sessions' => app(SessionSecurityService::class)->activeSessionsCount(),
        ];
    }

    /**
     * @return Collection<int, LoginAttempt>
     */
    public function recentAccesses(int $limit = 15): Collection
    {
        return LoginAttempt::query()
            ->with('user:id,name,email')
            ->orderByDesc('attempted_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @return array<string, mixed>
     */
    public function serializeLoginAttempt(LoginAttempt $attempt): array
    {
        return [
            'id' => $attempt->id,
            'email' => $attempt->email,
            'user' => $attempt->user?->only(['id', 'name', 'email']),
            'ip_address' => $attempt->ip_address,
            'user_agent' => $attempt->user_agent,
            'successful' => $attempt->successful,
            'failure_reason' => $attempt->failure_reason,
            'attempted_at' => $attempt->attempted_at?->toIso8601String(),
            'attempted_at_label' => $attempt->attempted_at?->translatedFormat('d/m/Y H:i:s'),
        ];
    }
}

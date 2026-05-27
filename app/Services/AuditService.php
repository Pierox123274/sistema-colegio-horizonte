<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Enums\AuditSeverity;
use App\Enums\IntranetRole;
use App\Models\AuditLog;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

final class AuditService
{
    /**
     * @param  array<string, mixed>  $context
     */
    public function log(
        AuditAction $action,
        AuditModule $module,
        ?User $user = null,
        ?string $entityType = null,
        ?int $entityId = null,
        ?string $description = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        AuditResult $result = AuditResult::Success,
        AuditSeverity $severity = AuditSeverity::Info,
        ?array $context = null,
        ?Request $request = null,
    ): AuditLog {
        $request ??= request();

        return AuditLog::query()->create([
            'user_id' => $user?->id,
            'user_role' => $this->primaryRole($user),
            'action' => $action,
            'module' => $module,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'result' => $result,
            'severity' => $severity,
            'metadata' => $context,
            'created_at' => now(),
        ]);
    }

    /**
     * @param  array<string, string>  $filters
     */
    public function paginate(array $filters, User $viewer, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->queryForViewer($viewer, $filters)
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, string>  $filters
     * @return Collection<int, AuditLog>
     */
    public function recentTimeline(array $filters, User $viewer, int $limit = 12): Collection
    {
        return $this->queryForViewer($viewer, $filters)
            ->with('user:id,name,email')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  array<string, string>  $filters
     * @return array{total_events: int, critical_events: int, events_today: int, failed_logins_24h: int, active_sessions: int}
     */
    public function dashboardStats(User $viewer, array $filters): array
    {
        $query = $this->queryForViewer($viewer, $filters);

        return [
            'total_events' => (clone $query)->count(),
            'critical_events' => (clone $query)->where('severity', AuditSeverity::Critical->value)->count(),
            'events_today' => (clone $query)->whereDate('created_at', today())->count(),
            'failed_logins_24h' => LoginAttempt::query()
                ->where('successful', false)
                ->where('attempted_at', '>=', now()->subHours(24))
                ->count(),
            'active_sessions' => app(SessionSecurityService::class)->activeSessionsCount(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function serialize(AuditLog $log): array
    {
        return [
            'id' => $log->id,
            'user' => $log->user?->only(['id', 'name', 'email']),
            'user_role' => $log->user_role,
            'action' => $log->action->value,
            'action_label' => $this->labelFor(AuditAction::options(), $log->action->value),
            'module' => $log->module->value,
            'module_label' => $this->labelFor(AuditModule::options(), $log->module->value),
            'entity_type' => $log->entity_type,
            'entity_id' => $log->entity_id,
            'description' => $log->description,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'browser' => $this->browserLabel($log->user_agent),
            'old_values' => $log->old_values,
            'new_values' => $log->new_values,
            'result' => $log->result->value,
            'severity' => $log->severity->value,
            'created_at' => $log->created_at?->toIso8601String(),
            'created_at_label' => $log->created_at?->translatedFormat('d/m/Y H:i:s'),
        ];
    }

    public function logFromRoute(Request $request, int $statusCode): void
    {
        $user = $request->user();
        if ($user === null) {
            return;
        }

        $route = $request->route();
        if ($route === null) {
            return;
        }

        $routeName = (string) $route->getName();
        if ($routeName === '' || str_contains($routeName, 'security.')) {
            return;
        }

        $mapping = $this->resolveRouteMapping($request, $routeName);
        if ($mapping === null) {
            return;
        }

        $this->log(
            $mapping['action'],
            $mapping['module'],
            $user,
            $mapping['entity_type'],
            $mapping['entity_id'],
            $mapping['description'],
            result: $statusCode >= 400 ? AuditResult::Error : AuditResult::Success,
            severity: $mapping['severity'],
            request: $request,
        );
    }

    /**
     * @param  array<string, string>  $filters
     * @return Builder<AuditLog>
     */
    private function queryForViewer(User $viewer, array $filters): Builder
    {
        $query = AuditLog::query();

        if ($viewer->hasRole(IntranetRole::Docente->value)
            && ! $viewer->hasRole(IntranetRole::Administrador->value)
        ) {
            $query->where('user_id', $viewer->id);
        } elseif ($viewer->hasRole(IntranetRole::Secretaria->value)
            && ! $viewer->hasRole(IntranetRole::Administrador->value)
        ) {
            $query->whereNotIn('module', [
                AuditModule::Users->value,
                AuditModule::Security->value,
            ]);
        }

        if ($search = trim($filters['search'] ?? '')) {
            $like = '%'.$search.'%';
            $query->where(function (Builder $q) use ($like): void {
                $q->where('description', 'like', $like)
                    ->orWhere('ip_address', 'like', $like)
                    ->orWhereHas('user', fn (Builder $u) => $u
                        ->where('name', 'like', $like)
                        ->orWhere('email', 'like', $like));
            });
        }

        if ($module = $filters['module'] ?? '') {
            $query->where('module', $module);
        }

        if ($action = $filters['action'] ?? '') {
            $query->where('action', $action);
        }

        if ($severity = $filters['severity'] ?? '') {
            $query->where('severity', $severity);
        }

        if ($userId = $filters['user_id'] ?? '') {
            $query->where('user_id', (int) $userId);
        }

        if ($from = $filters['date_from'] ?? '') {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $filters['date_to'] ?? '') {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    /**
     * @return array{action: AuditAction, module: AuditModule, entity_type: ?string, entity_id: ?int, description: string, severity: AuditSeverity}|null
     */
    private function resolveRouteMapping(Request $request, string $routeName): ?array
    {
        $method = $request->method();
        $entityId = $this->routeEntityId($request);

        if (str_contains($routeName, 'export')) {
            return [
                'action' => AuditAction::Export,
                'module' => $this->moduleFromRoute($routeName),
                'entity_type' => null,
                'entity_id' => null,
                'description' => 'Exportación: '.$routeName,
                'severity' => AuditSeverity::Info,
            ];
        }

        $action = match (true) {
            in_array($method, ['POST'], true) => AuditAction::Create,
            in_array($method, ['PUT', 'PATCH'], true) => AuditAction::Update,
            in_array($method, ['DELETE'], true) => AuditAction::Delete,
            default => null,
        };

        if ($action === null) {
            return null;
        }

        if (str_contains($routeName, 'deactivate') || str_contains($routeName, 'anul')) {
            $action = AuditAction::Cancel;
        }

        if (str_contains($routeName, 'attendance')) {
            $action = AuditAction::Attendance;
        } elseif (str_contains($routeName, 'grades') || str_contains($routeName, 'grade')) {
            $action = AuditAction::Grade;
        } elseif (str_contains($routeName, 'payment')) {
            $action = AuditAction::Payment;
        } elseif (str_contains($routeName, 'enrollment')) {
            $action = AuditAction::Enrollment;
        } elseif (str_contains($routeName, 'announcement')) {
            $action = AuditAction::Announcement;
        } elseif (str_contains($routeName, 'sales') || str_contains($routeName, 'sale')) {
            $action = AuditAction::Sale;
        } elseif (str_contains($routeName, 'cash-register')) {
            $action = str_contains($routeName, 'close') ? AuditAction::CashClose : AuditAction::CashOpen;
        }

        return [
            'action' => $action,
            'module' => $this->moduleFromRoute($routeName),
            'entity_type' => $routeName,
            'entity_id' => $entityId,
            'description' => ucfirst($action->value).' vía '.$routeName,
            'severity' => in_array($action, [AuditAction::Delete, AuditAction::Cancel], true)
                ? AuditSeverity::Warning
                : AuditSeverity::Info,
        ];
    }

    private function moduleFromRoute(string $routeName): AuditModule
    {
        return match (true) {
            str_contains($routeName, 'adaptive-analytics')
                || str_contains($routeName, 'adaptive-learning')
                || str_contains($routeName, 'diagnostic')
                || str_contains($routeName, 'learning-path') => AuditModule::AdaptiveLearning,
            str_contains($routeName, 'gamification') => AuditModule::Gamification,
            str_contains($routeName, 'student') => AuditModule::Students,
            str_contains($routeName, 'guardian') => AuditModule::Guardians,
            str_contains($routeName, 'enrollment') => AuditModule::Enrollment,
            str_contains($routeName, 'payment') || str_contains($routeName, 'pension') => AuditModule::Finance,
            str_contains($routeName, 'inventory') || str_contains($routeName, 'product') => AuditModule::Inventory,
            str_contains($routeName, 'sales') || str_contains($routeName, 'cash') => AuditModule::Sales,
            str_contains($routeName, 'attendance') => AuditModule::Attendance,
            str_contains($routeName, 'grade') || str_contains($routeName, 'evaluation') => AuditModule::Grades,
            str_contains($routeName, 'announcement') => AuditModule::Announcements,
            str_contains($routeName, 'analytics') || str_contains($routeName, 'report') => AuditModule::Analytics,
            str_contains($routeName, 'admin.user') => AuditModule::Users,
            str_contains($routeName, 'academic') => AuditModule::Academic,
            default => AuditModule::Security,
        };
    }

    private function routeEntityId(Request $request): ?int
    {
        foreach ($request->route()?->parameters() ?? [] as $parameter) {
            if (is_object($parameter) && method_exists($parameter, 'getKey')) {
                return (int) $parameter->getKey();
            }
            if (is_numeric($parameter)) {
                return (int) $parameter;
            }
        }

        return null;
    }

    private function primaryRole(?User $user): ?string
    {
        return $user?->roles->first()?->name;
    }

    /**
     * @param  list<array{value: string, label: string}>  $options
     */
    private function labelFor(array $options, string $value): string
    {
        return collect($options)->firstWhere('value', $value)['label'] ?? $value;
    }

    private function browserLabel(?string $userAgent): string
    {
        if ($userAgent === null) {
            return 'Desconocido';
        }

        return match (true) {
            str_contains($userAgent, 'Edg') => 'Microsoft Edge',
            str_contains($userAgent, 'Chrome') => 'Chrome',
            str_contains($userAgent, 'Firefox') => 'Firefox',
            str_contains($userAgent, 'Safari') => 'Safari',
            default => 'Navegador',
        };
    }
}

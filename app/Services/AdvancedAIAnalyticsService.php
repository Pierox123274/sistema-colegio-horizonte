<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Models\AuditLog;
use Illuminate\Support\Carbon;

/**
 * Métricas de uso e impacto IA desde auditoría (sin contenido de prompts).
 */
final class AdvancedAIAnalyticsService
{
    /**
     * @return array<string, mixed>
     */
    public function usageSummary(int $days = 30): array
    {
        $since = Carbon::now()->subDays($days);

        $base = AuditLog::query()
            ->where('module', AuditModule::Ai->value)
            ->where('action', AuditAction::AiQuery->value)
            ->where('created_at', '>=', $since);

        $rows = (clone $base)->get(['result', 'context']);
        $total = $rows->count();
        $cacheHits = $rows->filter(fn ($r) => ($r->context['cache_hit'] ?? false) === true)->count();
        $success = $rows->where('result', 'success')->count();
        $actions = [];
        foreach ($rows as $row) {
            $action = (string) ($row->context['action'] ?? 'unknown');
            $actions[$action] = ($actions[$action] ?? 0) + 1;
        }

        return [
            'period_days' => $days,
            'total_queries' => $total,
            'success_rate' => $total > 0 ? round($success / $total * 100, 1) : null,
            'cache_hit_rate' => $total > 0 ? round($cacheHits / $total * 100, 1) : null,
            'by_action' => $actions,
            'provider' => config('ai.provider'),
            'tutor_enabled' => (bool) config('ai.tutor_enabled'),
            'modules' => config('ai.modules', []),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Enums\AuditSeverity;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'user_role' => 'Administrador',
            'action' => AuditAction::View,
            'module' => AuditModule::Security,
            'entity_type' => null,
            'entity_id' => null,
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'old_values' => null,
            'new_values' => null,
            'result' => AuditResult::Success,
            'severity' => AuditSeverity::Info,
            'metadata' => null,
            'created_at' => now(),
        ];
    }
}

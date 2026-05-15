<?php

namespace App\Models;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Enums\AuditSeverity;
use Database\Factories\AuditLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    /** @use HasFactory<AuditLogFactory> */
    use HasFactory;

    public $timestamps = false;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'user_role',
        'action',
        'module',
        'entity_type',
        'entity_id',
        'description',
        'ip_address',
        'user_agent',
        'old_values',
        'new_values',
        'result',
        'severity',
        'metadata',
        'created_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'action' => AuditAction::class,
            'module' => AuditModule::class,
            'result' => AuditResult::class,
            'severity' => AuditSeverity::class,
            'old_values' => 'array',
            'new_values' => 'array',
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

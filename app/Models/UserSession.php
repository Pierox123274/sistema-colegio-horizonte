<?php

namespace App\Models;

use Database\Factories\UserSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSession extends Model
{
    /** @use HasFactory<UserSessionFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'device_label',
        'device_fingerprint',
        'logged_in_at',
        'last_activity_at',
        'logged_out_at',
        'expires_at',
        'is_active',
        'is_suspicious',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'logged_in_at' => 'datetime',
            'last_activity_at' => 'datetime',
            'logged_out_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
            'is_suspicious' => 'boolean',
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

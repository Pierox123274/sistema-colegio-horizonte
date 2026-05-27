<?php

namespace App\Models;

use App\Enums\ChallengeType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Challenge extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'type',
        'title',
        'description',
        'target_value',
        'xp_reward',
        'starts_at',
        'ends_at',
        'is_active',
        'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ChallengeType::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
            'meta' => 'array',
        ];
    }

    /**
     * @return HasMany<StudentChallenge, $this>
     */
    public function studentChallenges(): HasMany
    {
        return $this->hasMany(StudentChallenge::class);
    }
}

<?php

namespace App\Models;

use App\Enums\AchievementType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Achievement extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'type',
        'title',
        'description',
        'icon',
        'color',
        'rarity',
        'xp_reward',
        'criteria',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => AchievementType::class,
            'criteria' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<StudentAchievement, $this>
     */
    public function studentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class);
    }
}

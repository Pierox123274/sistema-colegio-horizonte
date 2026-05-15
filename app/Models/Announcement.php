<?php

namespace App\Models;

use App\Enums\AnnouncementAudienceType;
use App\Enums\AnnouncementPriority;
use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    /** @use HasFactory<AnnouncementFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'priority',
        'audience_type',
        'starts_at',
        'ends_at',
        'created_by_user_id',
        'has_attachment',
        'attachment_path',
        'attachment_mime',
        'attachment_original_name',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'priority' => AnnouncementPriority::class,
            'audience_type' => AnnouncementAudienceType::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'has_attachment' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_recipients')
            ->withTimestamps();
    }

    /**
     * @return HasMany<AnnouncementRead, $this>
     */
    public function reads(): HasMany
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    public function isPublishedNow(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at !== null && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }
}

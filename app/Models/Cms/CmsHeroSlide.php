<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class CmsHeroSlide extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'subtitle',
        'image_path',
        'badge',
        'cta_primary_label',
        'cta_primary_url',
        'cta_secondary_label',
        'cta_secondary_url',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function isVisibleNow(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->starts_at !== null && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->ends_at !== null && $this->ends_at->isPast()) {
            return false;
        }

        return true;
    }
}

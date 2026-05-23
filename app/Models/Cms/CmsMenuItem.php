<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsMenuItem extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'menu_id',
        'parent_id',
        'label',
        'url',
        'route_name',
        'route_params',
        'target',
        'sort_order',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'route_params' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<CmsMenu, $this>
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(CmsMenu::class, 'menu_id');
    }

    /**
     * @return BelongsTo<CmsMenuItem, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return HasMany<CmsMenuItem, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order');
    }
}

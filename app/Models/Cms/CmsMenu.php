<?php

namespace App\Models\Cms;

use App\Enums\CmsMenuLocation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsMenu extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'location',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'location' => CmsMenuLocation::class,
        ];
    }

    /**
     * @return HasMany<CmsMenuItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CmsMenuItem::class, 'menu_id')->orderBy('sort_order');
    }

    /**
     * @return HasMany<CmsMenuItem, $this>
     */
    public function rootItems(): HasMany
    {
        return $this->items()->whereNull('parent_id');
    }
}

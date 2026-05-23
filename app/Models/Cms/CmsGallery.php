<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsGallery extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'category',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * @return HasMany<CmsGalleryImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(CmsGalleryImage::class, 'gallery_id')->orderBy('sort_order');
    }
}

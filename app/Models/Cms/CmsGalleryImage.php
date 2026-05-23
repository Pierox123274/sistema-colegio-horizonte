<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsGalleryImage extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'gallery_id',
        'image_path',
        'caption',
        'category',
        'sort_order',
        'is_active',
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
     * @return BelongsTo<CmsGallery, $this>
     */
    public function gallery(): BelongsTo
    {
        return $this->belongsTo(CmsGallery::class, 'gallery_id');
    }
}

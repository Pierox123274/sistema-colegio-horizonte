<?php

namespace App\Models\Cms;

use App\Enums\CmsPublicationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CmsNews extends Model
{
    protected $table = 'cms_news';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category_id',
        'slug',
        'title',
        'excerpt',
        'body',
        'featured_image',
        'is_featured',
        'status',
        'published_at',
        'meta_title',
        'meta_description',
        'og_image',
        'robots_index',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CmsPublicationStatus::class,
            'published_at' => 'datetime',
            'is_featured' => 'boolean',
            'robots_index' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<CmsNewsCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CmsNewsCategory::class, 'category_id');
    }

    public function isPublished(): bool
    {
        return $this->status === CmsPublicationStatus::Published
            && ($this->published_at === null || $this->published_at->isPast());
    }
}

<?php

namespace App\Models\Cms;

use App\Enums\CmsPublicationStatus;
use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'hero_image',
        'hero_title',
        'hero_subtitle',
        'body',
        'meta_title',
        'meta_description',
        'og_image',
        'robots_index',
        'status',
        'published_at',
        'template',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => CmsPublicationStatus::class,
            'published_at' => 'datetime',
            'robots_index' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function isPublished(): bool
    {
        return $this->status === CmsPublicationStatus::Published
            && ($this->published_at === null || $this->published_at->isPast());
    }
}

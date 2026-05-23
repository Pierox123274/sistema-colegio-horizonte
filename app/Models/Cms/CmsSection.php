<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class CmsSection extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'page_key',
        'section_key',
        'title',
        'payload',
        'is_active',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}

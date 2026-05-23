<?php

namespace App\Models\Cms;

use Illuminate\Database\Eloquent\Model;

class CmsTestimonial extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'role',
        'org',
        'quote',
        'photo_path',
        'is_visible',
        'sort_order',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}

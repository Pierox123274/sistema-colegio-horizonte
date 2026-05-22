<?php

namespace App\Models;

use App\Enums\VirtualResourceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualResource extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'virtual_classroom_id',
        'title',
        'resource_type',
        'file_path',
        'external_url',
        'topic',
        'competency',
        'sort_order',
        'created_by_user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'resource_type' => VirtualResourceType::class,
        ];
    }

    /**
     * @return BelongsTo<VirtualClassroom, $this>
     */
    public function virtualClassroom(): BelongsTo
    {
        return $this->belongsTo(VirtualClassroom::class);
    }
}

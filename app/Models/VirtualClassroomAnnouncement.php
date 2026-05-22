<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualClassroomAnnouncement extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'virtual_classroom_id',
        'user_id',
        'title',
        'body',
        'published_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<VirtualClassroom, $this>
     */
    public function virtualClassroom(): BelongsTo
    {
        return $this->belongsTo(VirtualClassroom::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

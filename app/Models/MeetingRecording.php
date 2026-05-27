<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingRecording extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'virtual_meeting_id',
        'title',
        'url',
        'recorded_at',
        'duration_seconds',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<VirtualMeeting, $this>
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(VirtualMeeting::class, 'virtual_meeting_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingAttendance extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'virtual_meeting_id',
        'user_id',
        'joined_at',
        'left_at',
        'duration_seconds',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'joined_at' => 'datetime',
            'left_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<VirtualMeeting, $this>
     */
    public function meeting(): BelongsTo
    {
        return $this->belongsTo(VirtualMeeting::class, 'virtual_meeting_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

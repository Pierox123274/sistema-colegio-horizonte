<?php

namespace App\Models;

use App\Enums\MeetingParticipantRole;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MeetingParticipant extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'virtual_meeting_id',
        'user_id',
        'student_id',
        'email',
        'role',
        'invited_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => MeetingParticipantRole::class,
            'invited_at' => 'datetime',
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

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

<?php

namespace App\Models;

use App\Enums\MeetingProvider;
use App\Enums\MeetingStatus;
use App\Enums\MeetingType;
use Database\Factories\VirtualMeetingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VirtualMeeting extends Model
{
    /** @use HasFactory<VirtualMeetingFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'virtual_classroom_id',
        'academic_year_id',
        'section_id',
        'created_by_user_id',
        'host_user_id',
        'title',
        'description',
        'meeting_type',
        'provider',
        'status',
        'scheduled_at',
        'ends_at',
        'duration_minutes',
        'join_url',
        'external_meeting_id',
        'join_password',
        'waiting_room_enabled',
        'recording_allowed',
        'is_recurring',
        'recurrence_rule',
        'is_private',
        'cancelled_at',
        'started_at',
        'completed_at',
        'provider_metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meeting_type' => MeetingType::class,
            'provider' => MeetingProvider::class,
            'status' => MeetingStatus::class,
            'scheduled_at' => 'datetime',
            'ends_at' => 'datetime',
            'waiting_room_enabled' => 'boolean',
            'recording_allowed' => 'boolean',
            'is_recurring' => 'boolean',
            'is_private' => 'boolean',
            'recurrence_rule' => 'array',
            'provider_metadata' => 'array',
            'join_password' => 'encrypted',
            'cancelled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
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
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<Section, $this>
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * @return HasMany<MeetingParticipant, $this>
     */
    public function participants(): HasMany
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    /**
     * @return HasMany<MeetingAttendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(MeetingAttendance::class);
    }

    /**
     * @return HasMany<MeetingRecording, $this>
     */
    public function recordings(): HasMany
    {
        return $this->hasMany(MeetingRecording::class);
    }

    public function isJoinable(): bool
    {
        if ($this->status === MeetingStatus::Cancelled) {
            return false;
        }

        if ($this->status === MeetingStatus::Completed) {
            return false;
        }

        $windowStart = $this->scheduled_at->copy()->subMinutes(15);
        $windowEnd = $this->ends_at->copy()->addMinutes(30);

        return now()->between($windowStart, $windowEnd);
    }
}

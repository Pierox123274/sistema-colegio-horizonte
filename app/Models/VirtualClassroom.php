<?php

namespace App\Models;

use Database\Factories\VirtualClassroomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VirtualClassroom extends Model
{
    /** @use HasFactory<VirtualClassroomFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'academic_year_id',
        'section_id',
        'subject_id',
        'teacher_user_id',
        'title',
        'description',
        'is_active',
        'created_by_user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
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
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_user_id');
    }

    /**
     * @return HasMany<VirtualClassroomAnnouncement, $this>
     */
    public function announcements(): HasMany
    {
        return $this->hasMany(VirtualClassroomAnnouncement::class);
    }

    /**
     * @return HasMany<VirtualResource, $this>
     */
    public function resources(): HasMany
    {
        return $this->hasMany(VirtualResource::class);
    }

    /**
     * @return HasMany<Assignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * @return HasMany<OnlineExam, $this>
     */
    public function onlineExams(): HasMany
    {
        return $this->hasMany(OnlineExam::class);
    }

    /**
     * @return HasMany<VirtualMeeting, $this>
     */
    public function meetings(): HasMany
    {
        return $this->hasMany(VirtualMeeting::class);
    }
}

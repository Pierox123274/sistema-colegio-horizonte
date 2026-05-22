<?php

namespace App\Models;

use App\Enums\AcademicCalendarEventType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AcademicCalendarEvent extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'academic_year_id',
        'section_id',
        'subject_id',
        'student_id',
        'event_type',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'related_type',
        'related_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'event_type' => AcademicCalendarEventType::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
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
     * @return MorphTo<Model, $this>
     */
    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use Database\Factories\EvaluationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evaluation extends Model
{
    /** @use HasFactory<EvaluationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'subject_id',
        'academic_year_id',
        'educational_level_id',
        'grade_id',
        'section_id',
        'title',
        'period',
        'evaluated_at',
        'max_score',
        'weight',
        'is_active',
        'created_by_user_id',
    ];

    /**
     * @return array<string,string>
     */
    protected function casts(): array
    {
        return [
            'evaluated_at' => 'date',
            'max_score' => 'decimal:2',
            'weight' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * @return BelongsTo<EducationalLevel, $this>
     */
    public function educationalLevel(): BelongsTo
    {
        return $this->belongsTo(EducationalLevel::class);
    }

    /**
     * @return BelongsTo<Grade, $this>
     */
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
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
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * @return HasMany<GradeRecord, $this>
     */
    public function gradeRecords(): HasMany
    {
        return $this->hasMany(GradeRecord::class);
    }
}

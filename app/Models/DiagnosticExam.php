<?php

namespace App\Models;

use App\Enums\DiagnosticExamMode;
use Database\Factories\DiagnosticExamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiagnosticExam extends Model
{
    /** @use HasFactory<DiagnosticExamFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'academic_year_id',
        'educational_level_id',
        'grade_id',
        'section_id',
        'created_by_user_id',
        'mode',
        'is_active',
        'prevent_retake_after_completion',
        'adaptive_question_count',
        'threshold_basic_percent',
        'threshold_intermediate_percent',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'mode' => DiagnosticExamMode::class,
            'is_active' => 'boolean',
            'prevent_retake_after_completion' => 'boolean',
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
     * @return BelongsToMany<QuestionBank, $this>
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(QuestionBank::class, 'diagnostic_exam_question_bank')
            ->withPivot(['sort_order', 'points'])
            ->orderByPivot('sort_order', 'asc');
    }

    /**
     * @return HasMany<DiagnosticAttempt, $this>
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(DiagnosticAttempt::class);
    }
}

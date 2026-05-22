<?php

namespace App\Models;

use App\Enums\OnlineExamGradingMode;
use Database\Factories\OnlineExamFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnlineExam extends Model
{
    /** @use HasFactory<OnlineExamFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'virtual_classroom_id',
        'title',
        'description',
        'grading_mode',
        'time_limit_minutes',
        'max_attempts',
        'shuffle_questions',
        'show_results_after',
        'is_published',
        'available_from',
        'available_until',
        'created_by_user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'grading_mode' => OnlineExamGradingMode::class,
            'shuffle_questions' => 'boolean',
            'show_results_after' => 'boolean',
            'is_published' => 'boolean',
            'available_from' => 'datetime',
            'available_until' => 'datetime',
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
     * @return HasMany<OnlineExamQuestion, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(OnlineExamQuestion::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<OnlineExamAttempt, $this>
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(OnlineExamAttempt::class);
    }
}

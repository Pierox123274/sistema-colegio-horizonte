<?php

namespace App\Models;

use App\Enums\OnlineExamAttemptStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineExamAttempt extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'online_exam_id',
        'student_id',
        'user_id',
        'attempt_number',
        'status',
        'answers',
        'score_percent',
        'started_at',
        'completed_at',
        'expires_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OnlineExamAttemptStatus::class,
            'answers' => 'array',
            'score_percent' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<OnlineExam, $this>
     */
    public function onlineExam(): BelongsTo
    {
        return $this->belongsTo(OnlineExam::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

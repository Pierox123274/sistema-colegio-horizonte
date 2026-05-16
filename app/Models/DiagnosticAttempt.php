<?php

namespace App\Models;

use App\Enums\DiagnosticAttemptStatus;
use App\Enums\DiagnosticExamMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiagnosticAttempt extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'user_id',
        'diagnostic_exam_id',
        'status',
        'mode_snapshot',
        'score_percent',
        'classified_level',
        'answers',
        'weakness_by_topic',
        'adaptive_state',
        'started_at',
        'completed_at',
        'duration_seconds',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => DiagnosticAttemptStatus::class,
            'mode_snapshot' => DiagnosticExamMode::class,
            'answers' => 'array',
            'weakness_by_topic' => 'array',
            'adaptive_state' => 'array',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<DiagnosticExam, $this>
     */
    public function diagnosticExam(): BelongsTo
    {
        return $this->belongsTo(DiagnosticExam::class);
    }
}

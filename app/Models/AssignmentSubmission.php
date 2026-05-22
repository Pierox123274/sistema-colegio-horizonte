<?php

namespace App\Models;

use App\Enums\AssignmentSubmissionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'assignment_id',
        'student_id',
        'user_id',
        'status',
        'file_path',
        'student_comment',
        'teacher_feedback',
        'score',
        'submitted_at',
        'reviewed_at',
        'reviewed_by_user_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => AssignmentSubmissionStatus::class,
            'score' => 'decimal:2',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Assignment, $this>
     */
    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

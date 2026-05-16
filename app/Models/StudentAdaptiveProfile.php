<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAdaptiveProfile extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'last_classified_level',
        'last_diagnostic_score',
        'weakness_topics',
        'learning_path',
        'last_diagnostic_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weakness_topics' => 'array',
            'learning_path' => 'array',
            'last_diagnostic_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}

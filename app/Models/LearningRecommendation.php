<?php

namespace App\Models;

use App\Enums\LearningRecommendationSource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningRecommendation extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'student_id',
        'source',
        'title',
        'body',
        'topic',
        'priority',
        'estimated_weeks_to_improve',
        'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'source' => LearningRecommendationSource::class,
            'meta' => 'array',
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

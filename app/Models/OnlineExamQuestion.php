<?php

namespace App\Models;

use App\Enums\OnlineExamQuestionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineExamQuestion extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'online_exam_id',
        'question_type',
        'stem',
        'options',
        'correct_answer',
        'points',
        'sort_order',
        'topic',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'question_type' => OnlineExamQuestionType::class,
            'options' => 'array',
            'correct_answer' => 'array',
        ];
    }

    /**
     * @return BelongsTo<OnlineExam, $this>
     */
    public function onlineExam(): BelongsTo
    {
        return $this->belongsTo(OnlineExam::class);
    }
}

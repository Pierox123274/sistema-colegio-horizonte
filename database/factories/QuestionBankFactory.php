<?php

namespace Database\Factories;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use App\Models\QuestionBank;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionBank>
 */
class QuestionBankFactory extends Factory
{
    protected $model = QuestionBank::class;

    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'topic' => fake()->words(2, true),
            'question_type' => QuestionType::MultipleChoice,
            'difficulty' => QuestionDifficulty::Intermediate,
            'competencies' => ['competencia_demo'],
            'stem' => fake()->sentence(),
            'explanation' => null,
            'true_false_answer' => null,
            'short_answer_expected' => null,
            'is_active' => true,
        ];
    }
}

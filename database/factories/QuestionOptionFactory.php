<?php

namespace Database\Factories;

use App\Models\QuestionBank;
use App\Models\QuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<QuestionOption>
 */
class QuestionOptionFactory extends Factory
{
    protected $model = QuestionOption::class;

    public function definition(): array
    {
        return [
            'question_bank_id' => QuestionBank::factory(),
            'sort_order' => 0,
            'label' => 'A',
            'body' => fake()->sentence(),
            'is_correct' => false,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\DiagnosticExamMode;
use App\Models\DiagnosticExam;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiagnosticExam>
 */
class DiagnosticExamFactory extends Factory
{
    protected $model = DiagnosticExam::class;

    public function definition(): array
    {
        return [
            'title' => 'Diagnóstico '.fake()->word(),
            'description' => fake()->sentence(),
            'subject_id' => Subject::factory(),
            'mode' => DiagnosticExamMode::Fixed,
            'is_active' => true,
            'prevent_retake_after_completion' => false,
            'adaptive_question_count' => 6,
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Evaluation;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Evaluation>
 */
class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    public function definition(): array
    {
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);

        return [
            'subject_id' => Subject::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'title' => 'Evaluación '.fake()->numberBetween(1, 99),
            'period' => fake()->randomElement(['Bimestre 1', 'Bimestre 2', 'Bimestre 3', 'Bimestre 4']),
            'evaluated_at' => now()->toDateString(),
            'max_score' => 20,
            'weight' => 1,
            'is_active' => true,
            'created_by_user_id' => User::factory(),
        ];
    }
}

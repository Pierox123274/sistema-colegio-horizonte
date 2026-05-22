<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Section;
use App\Models\Subject;
use App\Models\User;
use App\Models\VirtualClassroom;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VirtualClassroom>
 */
class VirtualClassroomFactory extends Factory
{
    protected $model = VirtualClassroom::class;

    public function definition(): array
    {
        return [
            'academic_year_id' => AcademicYear::factory(),
            'section_id' => Section::factory(),
            'subject_id' => Subject::factory(),
            'teacher_user_id' => User::factory(),
            'title' => 'Aula virtual '.fake()->word(),
            'description' => fake()->optional()->sentence(),
            'is_active' => true,
            'created_by_user_id' => null,
        ];
    }
}

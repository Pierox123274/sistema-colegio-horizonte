<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Grade;
use App\Models\Section;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeacherAssignment>
 */
class TeacherAssignmentFactory extends Factory
{
    protected $model = TeacherAssignment::class;

    public function definition(): array
    {
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);

        return [
            'user_id' => User::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'subject_id' => null,
            'is_tutor' => false,
            'is_active' => true,
        ];
    }
}

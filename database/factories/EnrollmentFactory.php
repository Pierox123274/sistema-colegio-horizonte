<?php

namespace Database\Factories;

use App\Enums\EnrollmentStatus;
use App\Models\AcademicYear;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Enrollment>
 */
class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);

        return [
            'enrollment_code' => 'MAT-TST-'.Str::upper(Str::random(8)),
            'student_id' => Student::factory(),
            'guardian_id' => null,
            'academic_year_id' => AcademicYear::factory(),
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'classroom_id' => null,
            'enrollment_date' => now()->toDateString(),
            'amount' => fake()->randomFloat(2, 0, 5000),
            'status' => EnrollmentStatus::Matriculado->value,
            'observations' => null,
        ];
    }
}

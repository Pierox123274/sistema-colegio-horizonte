<?php

namespace Database\Factories;

use App\Enums\AttendanceStatus;
use App\Enums\EnrollmentStatus;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\EducationalLevel;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        $academicYear = AcademicYear::factory()->create();
        $level = EducationalLevel::factory()->create();
        $grade = Grade::factory()->create(['educational_level_id' => $level->id]);
        $section = Section::factory()->create(['grade_id' => $grade->id]);
        $enrollment = Enrollment::factory()->create([
            'academic_year_id' => $academicYear->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'status' => EnrollmentStatus::Matriculado->value,
        ]);

        return [
            'student_id' => $enrollment->student_id,
            'academic_year_id' => $academicYear->id,
            'educational_level_id' => $level->id,
            'grade_id' => $grade->id,
            'section_id' => $section->id,
            'attendance_date' => now()->toDateString(),
            'status' => AttendanceStatus::Presente->value,
            'observation' => fake()->optional()->sentence(),
            'recorded_by_user_id' => User::factory(),
        ];
    }
}

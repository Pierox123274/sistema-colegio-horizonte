<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Enums\EducationalLevel;
use App\Enums\Gender;
use App\Enums\StudentStatus;
use App\Models\Student;
use App\Support\StudentGradeCatalog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $level = fake()->randomElement(EducationalLevel::cases());
        $grade = fake()->randomElement(StudentGradeCatalog::gradesFor($level));

        return [
            'code' => 'EST-'.fake()->unique()->numerify('######'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'document_type' => DocumentType::Dni->value,
            'document_number' => fake()->unique()->numerify('########'),
            'birth_date' => fake()->dateTimeBetween('-17 years', '-3 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(Gender::cases())->value,
            'educational_level' => $level->value,
            'grade' => $grade,
            'section' => fake()->randomElement(['A', 'B', 'C']),
            'status' => StudentStatus::Activo->value,
            'address' => fake()->streetAddress(),
            'phone' => fake()->optional(0.8)->numerify('9########'),
            'email' => fake()->optional(0.5)->safeEmail(),
            'medical_observations' => fake()->optional(0.2)->sentence(),
        ];
    }
}

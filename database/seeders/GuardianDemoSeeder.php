<?php

namespace Database\Seeders;

use App\Enums\GuardianRelationshipType;
use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Opcional: php artisan db:seed --class=GuardianDemoSeeder
 */
class GuardianDemoSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::query()->take(8)->get();
        if ($students->isEmpty()) {
            $students = Student::factory()->count(6)->create();
        }

        Guardian::factory()
            ->count(5)
            ->create()
            ->each(function (Guardian $guardian) use ($students): void {
                $subset = $students->random(min(3, $students->count()));
                foreach ($subset as $student) {
                    $guardian->students()->attach($student->id, [
                        'relationship' => GuardianRelationshipType::Padre->value,
                        'is_primary' => fake()->boolean(40),
                        'is_financial_responsible' => fake()->boolean(30),
                        'emergency_priority' => fake()->optional(0.7)->numberBetween(1, 5),
                        'observations' => null,
                    ]);
                }
            });
    }
}

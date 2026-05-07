<?php

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Enums\GuardianRelationshipType;
use App\Models\Guardian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Guardian>
 */
class GuardianFactory extends Factory
{
    protected $model = Guardian::class;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'document_type' => DocumentType::Dni->value,
            'document_number' => fake()->unique()->numerify('########'),
            'relationship_type' => fake()->randomElement(GuardianRelationshipType::cases())->value,
            'phone' => fake()->numerify('9########'),
            'secondary_phone' => fake()->optional(0.4)->numerify('9########'),
            'email' => fake()->optional(0.7)->safeEmail(),
            'occupation' => fake()->optional(0.8)->jobTitle(),
            'address' => fake()->optional(0.9)->streetAddress(),
            'workplace' => fake()->optional(0.3)->company(),
            'is_emergency_contact' => fake()->boolean(35),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\LoginAttempt;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoginAttempt>
 */
class LoginAttemptFactory extends Factory
{
    protected $model = LoginAttempt::class;

    public function definition(): array
    {
        return [
            'email' => fake()->safeEmail(),
            'user_id' => null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'successful' => false,
            'failure_reason' => 'invalid_credentials',
            'attempted_at' => now(),
        ];
    }

    public function successful(): static
    {
        return $this->state(fn (): array => [
            'successful' => true,
            'failure_reason' => null,
        ]);
    }
}

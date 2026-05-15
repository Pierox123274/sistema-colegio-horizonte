<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserSession;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<UserSession>
 */
class UserSessionFactory extends Factory
{
    protected $model = UserSession::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'session_id' => Str::random(40),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'device_label' => 'Chrome · Windows',
            'device_fingerprint' => hash('sha256', fake()->uuid()),
            'logged_in_at' => now(),
            'last_activity_at' => now(),
            'logged_out_at' => null,
            'expires_at' => now()->addHours(2),
            'is_active' => true,
            'is_suspicious' => false,
        ];
    }
}

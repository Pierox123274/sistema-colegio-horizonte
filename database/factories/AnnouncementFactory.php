<?php

namespace Database\Factories;

use App\Enums\AnnouncementAudienceType;
use App\Enums\AnnouncementPriority;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Announcement>
 */
class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(6),
            'content' => fake()->paragraphs(2, true),
            'priority' => fake()->randomElement(AnnouncementPriority::values()),
            'audience_type' => AnnouncementAudienceType::All->value,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addDays(7),
            'created_by_user_id' => User::factory(),
            'has_attachment' => false,
            'attachment_path' => null,
            'attachment_mime' => null,
            'attachment_original_name' => null,
            'is_active' => true,
        ];
    }

    public function forTeachers(): static
    {
        return $this->state(fn (): array => [
            'audience_type' => AnnouncementAudienceType::Teachers->value,
        ]);
    }

    public function forStudents(): static
    {
        return $this->state(fn (): array => [
            'audience_type' => AnnouncementAudienceType::Students->value,
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (): array => [
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
        ]);
    }
}

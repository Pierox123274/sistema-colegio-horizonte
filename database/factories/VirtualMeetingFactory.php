<?php

namespace Database\Factories;

use App\Enums\MeetingProvider;
use App\Enums\MeetingStatus;
use App\Enums\MeetingType;
use App\Models\User;
use App\Models\VirtualMeeting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VirtualMeeting>
 */
class VirtualMeetingFactory extends Factory
{
    protected $model = VirtualMeeting::class;

    public function definition(): array
    {
        $start = now()->addDay()->setMinute(0)->setSecond(0);
        $duration = 60;

        return [
            'virtual_classroom_id' => null,
            'academic_year_id' => null,
            'section_id' => null,
            'created_by_user_id' => User::factory(),
            'host_user_id' => User::factory(),
            'title' => 'Videoclase '.fake()->words(2, true),
            'description' => fake()->optional()->sentence(),
            'meeting_type' => MeetingType::VirtualClass,
            'provider' => MeetingProvider::GoogleMeet,
            'status' => MeetingStatus::Scheduled,
            'scheduled_at' => $start,
            'ends_at' => $start->copy()->addMinutes($duration),
            'duration_minutes' => $duration,
            'join_url' => 'https://meet.google.com/abc-defg-hij',
            'waiting_room_enabled' => true,
            'recording_allowed' => false,
            'is_recurring' => false,
            'is_private' => true,
        ];
    }
}

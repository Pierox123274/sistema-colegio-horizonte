<?php

namespace App\Jobs;

use App\Enums\MeetingStatus;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Models\User;
use App\Models\VirtualMeeting;
use App\Services\UserNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendMeetingRemindersJob implements ShouldQueue
{
    use Queueable;

    public function handle(UserNotificationService $notifications): void
    {
        $minutes = max(5, (int) config('meetings.reminder_minutes_before', 30));
        $from = now();
        $to = now()->addMinutes($minutes);

        $meetings = VirtualMeeting::query()
            ->where('status', MeetingStatus::Scheduled)
            ->whereBetween('scheduled_at', [$from, $to])
            ->with('participants')
            ->get();

        foreach ($meetings as $meeting) {
            $users = User::query()
                ->whereIn('id', $meeting->participants()->pluck('user_id')->filter())
                ->get();

            foreach ($users as $user) {
                $actionUrl = $user->hasRole('Estudiante')
                    ? route('student.meetings.join', $meeting, absolute: false)
                    : route('teacher.meetings.join', $meeting, absolute: false);

                $notifications->notifyUser(
                    user: $user,
                    title: 'Videoclase próxima',
                    message: $meeting->title.' comienza a las '.$meeting->scheduled_at->format('H:i'),
                    category: NotificationCategory::Lms,
                    priority: NotificationPriority::High,
                    actionUrl: $actionUrl,
                    actionLabel: 'Unirse',
                    mailTemplate: 'institutional-notification',
                    meta: ['meeting_id' => $meeting->id, 'reminder' => true],
                );
            }
        }
    }
}

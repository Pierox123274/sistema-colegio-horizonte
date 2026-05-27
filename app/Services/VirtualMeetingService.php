<?php

namespace App\Services;

use App\Enums\AcademicCalendarEventType;
use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Enums\MeetingProvider;
use App\Enums\MeetingStatus;
use App\Enums\MeetingType;
use App\Enums\NotificationCategory;
use App\Enums\NotificationPriority;
use App\Meetings\Contracts\MeetingProviderInterface;
use App\Meetings\DTO\MeetingLinkResult;
use App\Meetings\Providers\GoogleMeetProvider;
use App\Meetings\Providers\NullMeetingProvider;
use App\Meetings\Providers\TeamsProvider;
use App\Meetings\Providers\ZoomProvider;
use App\Models\AcademicCalendarEvent;
use App\Models\MeetingAttendance;
use App\Models\User;
use App\Models\VirtualClassroom;
use App\Models\VirtualMeeting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

final class VirtualMeetingService
{
    public function __construct(
        private readonly AuditService $audit,
        private readonly MeetingAccessService $access,
        private readonly UserNotificationService $notifications,
    ) {}

    /**
     * Enlace pegado por el docente tiene prioridad. Fallback automático solo Google Meet sin URL.
     */
    private function resolveJoinLink(MeetingProvider $provider, array $data, VirtualMeeting $meeting): MeetingLinkResult
    {
        $manualUrl = trim((string) ($data['join_url'] ?? ''));

        if ($manualUrl !== '') {
            return new MeetingLinkResult(
                joinUrl: $manualUrl,
                metadata: [
                    'mode' => 'teacher_provided',
                    'provider' => $provider->value,
                ],
            );
        }

        if ($provider === MeetingProvider::GoogleMeet && $this->googleMeetFallbackEnabled()) {
            return $this->providerFor($provider)->createLink($meeting);
        }

        throw ValidationException::withMessages([
            'join_url' => 'Debe pegar el enlace de la reunión creado en Google Meet, Zoom o Teams.',
        ]);
    }

    private function googleMeetFallbackEnabled(): bool
    {
        if (! config('meetings.google_meet.allow_generated_fallback', true)) {
            return false;
        }

        $code = config('meetings.google_meet.configured_room_code');

        return is_string($code) && $code !== '';
    }

    public function providerFor(MeetingProvider $provider): MeetingProviderInterface
    {
        return match ($provider) {
            MeetingProvider::GoogleMeet => new GoogleMeetProvider,
            MeetingProvider::Zoom => new ZoomProvider,
            MeetingProvider::Teams => new TeamsProvider,
            MeetingProvider::Manual => new NullMeetingProvider,
        };
    }

    public function create(User $user, array $data): VirtualMeeting
    {
        $provider = isset($data['provider'])
            ? (is_string($data['provider']) ? MeetingProvider::from($data['provider']) : $data['provider'])
            : MeetingProvider::Manual;
        $scheduledAt = Carbon::parse($data['scheduled_at']);
        $duration = (int) ($data['duration_minutes'] ?? 60);
        $classroom = null;

        if (! empty($data['virtual_classroom_id'])) {
            $classroom = VirtualClassroom::query()->findOrFail($data['virtual_classroom_id']);
        }

        $meeting = VirtualMeeting::query()->create([
            'virtual_classroom_id' => $classroom?->id,
            'academic_year_id' => $classroom?->academic_year_id ?? $data['academic_year_id'] ?? null,
            'section_id' => $classroom?->section_id ?? $data['section_id'] ?? null,
            'created_by_user_id' => $user->id,
            'host_user_id' => $data['host_user_id'] ?? $user->id,
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'meeting_type' => MeetingType::from($data['meeting_type']),
            'provider' => $provider,
            'status' => MeetingStatus::Scheduled,
            'scheduled_at' => $scheduledAt,
            'ends_at' => $scheduledAt->copy()->addMinutes($duration),
            'duration_minutes' => $duration,
            'join_url' => '#',
            'waiting_room_enabled' => $data['waiting_room_enabled'] ?? true,
            'recording_allowed' => $data['recording_allowed'] ?? false,
            'is_recurring' => $data['is_recurring'] ?? false,
            'recurrence_rule' => $data['recurrence_rule'] ?? null,
            'is_private' => $data['is_private'] ?? true,
        ]);

        $link = $this->resolveJoinLink($provider, $data, $meeting);
        $meeting->update([
            'join_url' => $link->joinUrl,
            'external_meeting_id' => $link->externalMeetingId,
            'join_password' => $link->password,
            'provider_metadata' => $link->metadata,
        ]);

        $participantIds = $this->access->resolveParticipantUserIds(
            $meeting,
            $data['participant_user_ids'] ?? []
        );
        $this->access->syncParticipants($meeting, $participantIds);
        $this->syncCalendar($meeting);
        $this->notifyParticipants($meeting, 'Videoclase programada', 'Se ha programado: '.$meeting->title);

        $this->audit->log(
            AuditAction::Create,
            AuditModule::Meetings,
            $user,
            VirtualMeeting::class,
            $meeting->id,
            'Creación de videoclase / reunión',
            null,
            ['title' => $meeting->title, 'provider' => $meeting->provider->value],
            AuditResult::Success,
        );

        return $meeting->fresh(['host', 'virtualClassroom']);
    }

    public function cancel(User $user, VirtualMeeting $meeting, ?string $reason = null): VirtualMeeting
    {
        $meeting->update([
            'status' => MeetingStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        $this->notifyParticipants(
            $meeting,
            'Reunión cancelada',
            ($reason ?? 'La reunión fue cancelada.').' — '.$meeting->title,
            NotificationPriority::High,
        );

        $this->audit->log(
            AuditAction::Cancel,
            AuditModule::Meetings,
            $user,
            VirtualMeeting::class,
            $meeting->id,
            'Cancelación de videoclase',
            null,
            ['reason' => $reason],
            AuditResult::Success,
        );

        return $meeting->fresh();
    }

    public function start(User $user, VirtualMeeting $meeting): VirtualMeeting
    {
        $meeting->update([
            'status' => MeetingStatus::Live,
            'started_at' => now(),
        ]);

        $this->notifyParticipants(
            $meeting,
            'La reunión ha comenzado',
            'Puedes unirte ahora: '.$meeting->title,
            NotificationPriority::High,
        );

        $this->audit->log(
            AuditAction::Update,
            AuditModule::Meetings,
            $user,
            VirtualMeeting::class,
            $meeting->id,
            'Inicio de videoclase',
            null,
            null,
            AuditResult::Success,
        );

        return $meeting->fresh();
    }

    public function complete(User $user, VirtualMeeting $meeting): VirtualMeeting
    {
        $meeting->update([
            'status' => MeetingStatus::Completed,
            'completed_at' => now(),
        ]);

        $this->audit->log(
            AuditAction::Update,
            AuditModule::Meetings,
            $user,
            VirtualMeeting::class,
            $meeting->id,
            'Finalización de videoclase',
            null,
            null,
            AuditResult::Success,
        );

        return $meeting->fresh();
    }

    public function recordJoin(User $user, VirtualMeeting $meeting): MeetingAttendance
    {
        $attendance = MeetingAttendance::query()->create([
            'virtual_meeting_id' => $meeting->id,
            'user_id' => $user->id,
            'joined_at' => now(),
        ]);

        if ($meeting->status === MeetingStatus::Scheduled) {
            $meeting->update([
                'status' => MeetingStatus::Live,
                'started_at' => $meeting->started_at ?? now(),
            ]);
        }

        $this->audit->log(
            AuditAction::Attendance,
            AuditModule::Meetings,
            $user,
            VirtualMeeting::class,
            $meeting->id,
            'Ingreso a videoclase',
            null,
            ['user_id' => $user->id],
            AuditResult::Success,
        );

        return $attendance;
    }

    public function syncCalendar(VirtualMeeting $meeting): void
    {
        AcademicCalendarEvent::query()->updateOrCreate(
            [
                'related_type' => VirtualMeeting::class,
                'related_id' => $meeting->id,
            ],
            [
                'academic_year_id' => $meeting->academic_year_id,
                'section_id' => $meeting->section_id,
                'student_id' => null,
                'event_type' => AcademicCalendarEventType::Meeting,
                'title' => $meeting->title,
                'description' => $meeting->description,
                'starts_at' => $meeting->scheduled_at,
                'ends_at' => $meeting->ends_at,
            ],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function presentMeeting(VirtualMeeting $meeting, User $viewer): array
    {
        $meeting->load(['host:id,name', 'virtualClassroom.subject:id,name', 'virtualClassroom.section:id,name']);

        return [
            'id' => $meeting->id,
            'title' => $meeting->title,
            'description' => $meeting->description,
            'meeting_type' => $meeting->meeting_type->value,
            'meeting_type_label' => collect(MeetingType::options())->firstWhere('value', $meeting->meeting_type->value)['label'] ?? $meeting->meeting_type->value,
            'provider' => $meeting->provider->value,
            'provider_label' => collect(MeetingProvider::options())->firstWhere('value', $meeting->provider->value)['label'] ?? $meeting->provider->value,
            'status' => $meeting->status->value,
            'scheduled_at' => $meeting->scheduled_at->toIso8601String(),
            'scheduled_at_label' => $meeting->scheduled_at->format('d/m/Y H:i'),
            'ends_at' => $meeting->ends_at->toIso8601String(),
            'duration_minutes' => $meeting->duration_minutes,
            'join_url' => $this->access->userIsParticipant($viewer, $meeting) ? $meeting->join_url : null,
            'can_join' => $this->access->userIsParticipant($viewer, $meeting) && $meeting->isJoinable(),
            'can_manage' => $this->access->userMayManage($viewer, $meeting),
            'show_href' => $this->showRouteFor($viewer, $meeting),
            'join_href' => $this->joinRouteFor($viewer, $meeting),
            'waiting_room_enabled' => $meeting->waiting_room_enabled,
            'recording_allowed' => $meeting->recording_allowed,
            'classroom' => $meeting->virtualClassroom ? [
                'id' => $meeting->virtualClassroom->id,
                'title' => $meeting->virtualClassroom->title,
                'subject' => $meeting->virtualClassroom->subject?->name,
                'section' => $meeting->virtualClassroom->section?->name,
            ] : null,
            'host' => $meeting->host?->only(['id', 'name']),
            'participants_count' => $meeting->participants()->count(),
            'attendances_count' => $meeting->attendances()->count(),
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function upcomingPayloadFor(User $user, int $limit = 5): Collection
    {
        return $this->access->queryUpcomingForUser($user, $limit)
            ->with(['host:id,name', 'virtualClassroom:id,title'])
            ->get()
            ->map(fn (VirtualMeeting $m) => $this->presentMeeting($m, $user));
    }

    private function notifyParticipants(
        VirtualMeeting $meeting,
        string $title,
        string $message,
        NotificationPriority $priority = NotificationPriority::Medium,
    ): void {
        $users = User::query()
            ->whereIn('id', $meeting->participants()->pluck('user_id')->filter())
            ->get();

        foreach ($users as $participant) {
            $actionUrl = $participant->hasRole('Estudiante')
                ? route('student.meetings.join', $meeting, absolute: false)
                : route('teacher.meetings.join', $meeting, absolute: false);

            $this->notifications->notifyUser(
                user: $participant,
                title: $title,
                message: $message,
                category: NotificationCategory::Lms,
                priority: $priority,
                actionUrl: $actionUrl,
                actionLabel: 'Unirse',
                mailTemplate: 'institutional-notification',
                meta: ['meeting_id' => $meeting->id],
            );
        }
    }

    private function showRouteFor(User $viewer, VirtualMeeting $meeting): string
    {
        if ($viewer->hasRole('Estudiante')) {
            return route('student.meetings.show', $meeting, absolute: false);
        }

        if ($viewer->hasRole('Administrador')) {
            return route('intranet.meetings.show', $meeting, absolute: false);
        }

        return route('teacher.meetings.show', $meeting, absolute: false);
    }

    private function joinRouteFor(User $viewer, VirtualMeeting $meeting): ?string
    {
        if (! $this->access->userIsParticipant($viewer, $meeting)) {
            return null;
        }

        if ($viewer->hasRole('Estudiante')) {
            return route('student.meetings.join', $meeting, absolute: false);
        }

        return route('teacher.meetings.join', $meeting, absolute: false);
    }

    /**
     * @return array<string, int|float>
     */
    public function institutionMetrics(): array
    {
        return [
            'total' => VirtualMeeting::query()->count(),
            'scheduled' => VirtualMeeting::query()->where('status', MeetingStatus::Scheduled)->count(),
            'live' => VirtualMeeting::query()->where('status', MeetingStatus::Live)->count(),
            'completed' => VirtualMeeting::query()->where('status', MeetingStatus::Completed)->count(),
            'attendances' => MeetingAttendance::query()->count(),
            'avg_attendees' => round(
                (float) MeetingAttendance::query()
                    ->selectRaw('virtual_meeting_id, count(*) as c')
                    ->groupBy('virtual_meeting_id')
                    ->get()
                    ->avg('c') ?? 0,
                1
            ),
        ];
    }
}

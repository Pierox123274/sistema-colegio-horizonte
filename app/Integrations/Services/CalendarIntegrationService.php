<?php

namespace App\Integrations\Services;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditResult;
use App\Integrations\DTO\CalendarEventDTO;
use App\Models\AcademicCalendarEvent;
use App\Models\User;
use App\Models\VirtualMeeting;
use App\Services\AuditService;
use Illuminate\Support\Facades\Log;

final class CalendarIntegrationService
{
    public function __construct(
        private readonly IntegrationRegistry $registry,
        private readonly AuditService $audit,
    ) {}

    public function syncMeeting(VirtualMeeting $meeting, ?User $actor = null): void
    {
        if (! config('integrations.calendar.enabled')) {
            return;
        }

        $event = new CalendarEventDTO(
            title: $meeting->title,
            description: trim(($meeting->description ?? '')."\n\nEnlace: ".($meeting->join_url ?? '')),
            startsAt: $meeting->scheduled_at,
            endsAt: $meeting->ends_at ?? $meeting->scheduled_at->copy()->addMinutes($meeting->duration_minutes ?? 60),
            location: $meeting->join_url,
            joinUrl: $meeting->join_url,
            metadata: ['meeting_id' => $meeting->id],
        );

        $result = $this->registry->calendar()->exportEvent($event);

        $metadata = array_merge($meeting->provider_metadata ?? [], [
            'calendar' => [
                'add_url' => $result->addToCalendarUrl,
                'google_url' => $result->googleCalendarUrl,
                'external_event_id' => $result->externalEventId,
                'synced_at' => now()->toIso8601String(),
            ],
        ]);

        $meeting->update(['provider_metadata' => $metadata]);

        Log::channel('integrations')->info('meeting_calendar_sync', [
            'meeting_id' => $meeting->id,
            'success' => $result->success,
        ]);

        if ($actor !== null) {
            $this->audit->log(
                AuditAction::Create,
                AuditModule::Integrations,
                $actor,
                VirtualMeeting::class,
                $meeting->id,
                'Sincronización calendario externo',
                null,
                ['calendar_url' => $result->addToCalendarUrl],
                AuditResult::Success,
            );
        }
    }

    public function syncAcademicEvent(AcademicCalendarEvent $event): void
    {
        if (! config('integrations.calendar.enabled') || $event->starts_at === null) {
            return;
        }

        $dto = new CalendarEventDTO(
            title: $event->title,
            description: $event->description,
            startsAt: $event->starts_at,
            endsAt: $event->ends_at ?? $event->starts_at->copy()->addHour(),
        );

        $result = $this->registry->calendar()->exportEvent($dto);

        Log::channel('integrations')->debug('academic_event_calendar', [
            'event_id' => $event->id,
            'url' => $result->addToCalendarUrl,
        ]);
    }
}

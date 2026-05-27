<?php

namespace App\Integrations\Providers\Calendar;

use App\Integrations\Contracts\CalendarProviderInterface;
use App\Integrations\DTO\CalendarEventDTO;
use App\Integrations\DTO\CalendarExportResult;
use Illuminate\Support\Facades\Log;

/**
 * Exportación por enlace Google Calendar; OAuth/API reservado para futura sincronización bidireccional.
 */
final class GoogleCalendarProvider implements CalendarProviderInterface
{
    public function name(): string
    {
        return 'google_calendar';
    }

    public function isConfigured(): bool
    {
        if (! config('calendar.google.enabled')) {
            return false;
        }

        return config('calendar.google.oauth_ready')
            || (config('calendar.google.client_id') && config('calendar.google.client_secret'));
    }

    public function exportEvent(CalendarEventDTO $event): CalendarExportResult
    {
        $addUrl = $this->buildAddToCalendarUrl($event);
        $ics = $this->buildIcs($event);

        Log::channel('integrations')->info('calendar_export', [
            'provider' => $this->name(),
            'title' => $event->title,
            'oauth_ready' => (bool) config('calendar.google.oauth_ready'),
        ]);

        if (config('calendar.google.oauth_ready')) {
            return new CalendarExportResult(
                success: true,
                externalEventId: null,
                addToCalendarUrl: $addUrl,
                googleCalendarUrl: $addUrl,
                icsContent: $ics,
                metadata: ['mode' => 'oauth_pending', 'api_sync' => 'future'],
            );
        }

        return CalendarExportResult::fallback($addUrl, $ics);
    }

    public function buildAddToCalendarUrl(CalendarEventDTO $event): string
    {
        return (new NullCalendarProvider)->buildAddToCalendarUrl($event);
    }

    private function buildIcs(CalendarEventDTO $event): string
    {
        $uid = md5($event->title.$event->startsAt->timestamp);
        $dtStart = $event->startsAt->utc()->format('Ymd\THis\Z');
        $dtEnd = $event->endsAt->utc()->format('Ymd\THis\Z');
        $summary = str_replace(["\r", "\n", ',', ';'], ' ', $event->title);

        return implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//IEP Horizonte//ES',
            'BEGIN:VEVENT',
            'UID:'.$uid.'@horizonte.edu.pe',
            'DTSTAMP:'.$dtStart,
            'DTSTART:'.$dtStart,
            'DTEND:'.$dtEnd,
            'SUMMARY:'.$summary,
            'DESCRIPTION:'.str_replace(["\r", "\n"], '\\n', (string) $event->description),
            'LOCATION:'.($event->location ?? $event->joinUrl ?? ''),
            'END:VEVENT',
            'END:VCALENDAR',
        ]);
    }
}

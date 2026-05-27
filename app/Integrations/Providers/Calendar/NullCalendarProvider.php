<?php

namespace App\Integrations\Providers\Calendar;

use App\Integrations\Contracts\CalendarProviderInterface;
use App\Integrations\DTO\CalendarEventDTO;
use App\Integrations\DTO\CalendarExportResult;

final class NullCalendarProvider implements CalendarProviderInterface
{
    public function name(): string
    {
        return 'null';
    }

    public function isConfigured(): bool
    {
        return false;
    }

    public function exportEvent(CalendarEventDTO $event): CalendarExportResult
    {
        $url = $this->buildAddToCalendarUrl($event);

        return CalendarExportResult::fallback($url);
    }

    public function buildAddToCalendarUrl(CalendarEventDTO $event): string
    {
        $params = http_build_query([
            'action' => 'TEMPLATE',
            'text' => $event->title,
            'dates' => $event->startsAt->utc()->format('Ymd\THis\Z').'/'
                .$event->endsAt->utc()->format('Ymd\THis\Z'),
            'details' => $event->description ?? '',
            'location' => $event->location ?? $event->joinUrl ?? '',
        ]);

        return config('calendar.export.template_url', 'https://calendar.google.com/calendar/render').'?'.$params;
    }
}

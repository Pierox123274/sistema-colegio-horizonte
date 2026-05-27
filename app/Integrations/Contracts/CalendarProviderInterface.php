<?php

namespace App\Integrations\Contracts;

use App\Integrations\DTO\CalendarEventDTO;
use App\Integrations\DTO\CalendarExportResult;

interface CalendarProviderInterface
{
    public function name(): string;

    public function isConfigured(): bool;

    public function exportEvent(CalendarEventDTO $event): CalendarExportResult;

    public function buildAddToCalendarUrl(CalendarEventDTO $event): string;
}

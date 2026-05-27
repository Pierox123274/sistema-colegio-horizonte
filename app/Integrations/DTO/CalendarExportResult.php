<?php

namespace App\Integrations\DTO;

final readonly class CalendarExportResult
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public bool $success,
        public ?string $externalEventId = null,
        public ?string $addToCalendarUrl = null,
        public ?string $googleCalendarUrl = null,
        public ?string $icsContent = null,
        public array $metadata = [],
        public ?string $errorCode = null,
    ) {}

    public static function fallback(string $addUrl, ?string $ics = null): self
    {
        return new self(
            success: true,
            addToCalendarUrl: $addUrl,
            googleCalendarUrl: $addUrl,
            icsContent: $ics,
            metadata: ['mode' => 'link_only'],
        );
    }
}

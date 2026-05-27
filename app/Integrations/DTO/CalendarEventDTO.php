<?php

namespace App\Integrations\DTO;

use Carbon\CarbonInterface;

final readonly class CalendarEventDTO
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public string $title,
        public ?string $description,
        public CarbonInterface $startsAt,
        public CarbonInterface $endsAt,
        public ?string $location = null,
        public ?string $joinUrl = null,
        public array $metadata = [],
    ) {}
}

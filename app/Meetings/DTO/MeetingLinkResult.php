<?php

namespace App\Meetings\DTO;

final readonly class MeetingLinkResult
{
    /**
     * @param  array<string, mixed>|null  $metadata
     */
    public function __construct(
        public string $joinUrl,
        public ?string $externalMeetingId = null,
        public ?string $password = null,
        public ?array $metadata = null,
    ) {}
}

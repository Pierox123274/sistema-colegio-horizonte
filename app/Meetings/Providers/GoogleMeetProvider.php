<?php

namespace App\Meetings\Providers;

use App\Enums\MeetingProvider;
use App\Meetings\Contracts\MeetingProviderInterface;
use App\Meetings\DTO\MeetingLinkResult;
use App\Models\VirtualMeeting;

/**
 * Fallback opcional cuando el docente no pegó enlace (MEETING_GOOGLE_ROOM_CODE).
 * El flujo principal es el enlace manual por reunión.
 */
final class GoogleMeetProvider implements MeetingProviderInterface
{
    public function provider(): MeetingProvider
    {
        return MeetingProvider::GoogleMeet;
    }

    public function createLink(VirtualMeeting $meeting): MeetingLinkResult
    {
        $configured = config('meetings.google_meet.configured_room_code');
        if (! is_string($configured) || $configured === '') {
            throw new \RuntimeException('MEETING_GOOGLE_ROOM_CODE no configurado; use enlace manual.');
        }

        $code = $configured;

        $base = rtrim((string) config('meetings.google_meet.base_url', 'https://meet.google.com'), '/');
        $joinUrl = $base.'/'.$code;

        return new MeetingLinkResult(
            joinUrl: $joinUrl,
            externalMeetingId: $code,
            metadata: ['mode' => 'generated_link', 'provider' => 'google_meet'],
        );
    }

    public function supportsApiIntegration(): bool
    {
        return false;
    }
}

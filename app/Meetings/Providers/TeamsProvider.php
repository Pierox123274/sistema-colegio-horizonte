<?php

namespace App\Meetings\Providers;

use App\Enums\MeetingProvider;
use App\Meetings\Contracts\MeetingProviderInterface;
use App\Meetings\DTO\MeetingLinkResult;
use App\Models\VirtualMeeting;
use Illuminate\Support\Str;

/** Stub preparado para integración Microsoft Graph / Teams. */
final class TeamsProvider implements MeetingProviderInterface
{
    public function provider(): MeetingProvider
    {
        return MeetingProvider::Teams;
    }

    public function createLink(VirtualMeeting $meeting): MeetingLinkResult
    {
        $token = Str::uuid()->toString();
        $base = rtrim((string) config('meetings.teams.base_url', 'https://teams.microsoft.com/l/meetup-join'), '/');

        return new MeetingLinkResult(
            joinUrl: $base.'/'.$token,
            externalMeetingId: $token,
            metadata: ['mode' => 'stub', 'api_ready' => false],
        );
    }

    public function supportsApiIntegration(): bool
    {
        return false;
    }
}

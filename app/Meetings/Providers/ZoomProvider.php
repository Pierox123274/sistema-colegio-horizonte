<?php

namespace App\Meetings\Providers;

use App\Enums\MeetingProvider;
use App\Meetings\Contracts\MeetingProviderInterface;
use App\Meetings\DTO\MeetingLinkResult;
use App\Models\VirtualMeeting;
use Illuminate\Support\Str;

/** Stub preparado para integración API Zoom. */
final class ZoomProvider implements MeetingProviderInterface
{
    public function provider(): MeetingProvider
    {
        return MeetingProvider::Zoom;
    }

    public function createLink(VirtualMeeting $meeting): MeetingLinkResult
    {
        $id = (string) random_int(100000000, 999999999);
        $password = Str::upper(Str::random(6));
        $base = rtrim((string) config('meetings.zoom.base_url', 'https://zoom.us/j'), '/');

        return new MeetingLinkResult(
            joinUrl: $base.'/'.$id.'?pwd='.$password,
            externalMeetingId: $id,
            password: $password,
            metadata: ['mode' => 'stub', 'api_ready' => false],
        );
    }

    public function supportsApiIntegration(): bool
    {
        return false;
    }
}

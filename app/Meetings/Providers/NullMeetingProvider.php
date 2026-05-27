<?php

namespace App\Meetings\Providers;

use App\Enums\MeetingProvider;
use App\Meetings\Contracts\MeetingProviderInterface;
use App\Meetings\DTO\MeetingLinkResult;
use App\Models\VirtualMeeting;

final class NullMeetingProvider implements MeetingProviderInterface
{
    public function provider(): MeetingProvider
    {
        return MeetingProvider::Manual;
    }

    public function createLink(VirtualMeeting $meeting): MeetingLinkResult
    {
        return new MeetingLinkResult(
            joinUrl: $meeting->join_url ?: '#',
            metadata: ['mode' => 'manual'],
        );
    }

    public function supportsApiIntegration(): bool
    {
        return false;
    }
}

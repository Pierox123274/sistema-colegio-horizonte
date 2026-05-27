<?php

namespace App\Meetings\Contracts;

use App\Enums\MeetingProvider;
use App\Meetings\DTO\MeetingLinkResult;
use App\Models\VirtualMeeting;

interface MeetingProviderInterface
{
    public function provider(): MeetingProvider;

    public function createLink(VirtualMeeting $meeting): MeetingLinkResult;

    public function supportsApiIntegration(): bool;
}

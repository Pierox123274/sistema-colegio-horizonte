<?php

namespace App\Enums;

enum MeetingParticipantRole: string
{
    case Host = 'host';
    case CoHost = 'co_host';
    case Participant = 'participant';
}

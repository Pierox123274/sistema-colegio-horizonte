<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\User;
use App\Models\VirtualMeeting;
use App\Services\MeetingAccessService;

class VirtualMeetingPolicy
{
    public function __construct(
        private readonly MeetingAccessService $access,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Docente->value,
            IntranetRole::Estudiante->value,
            IntranetRole::Secretaria->value,
        ]);
    }

    public function view(User $user, VirtualMeeting $meeting): bool
    {
        if ($user->hasRole(IntranetRole::Secretaria->value)) {
            return true;
        }

        return $this->access->userIsParticipant($user, $meeting);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function update(User $user, VirtualMeeting $meeting): bool
    {
        return $this->access->userMayManage($user, $meeting);
    }

    public function cancel(User $user, VirtualMeeting $meeting): bool
    {
        return $this->access->userMayManage($user, $meeting);
    }

    public function join(User $user, VirtualMeeting $meeting): bool
    {
        return $this->access->userIsParticipant($user, $meeting) && $meeting->isJoinable();
    }
}

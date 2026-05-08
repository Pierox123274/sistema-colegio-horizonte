<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user);
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $this->canView($user);
    }

    public function create(User $user): bool
    {
        return $this->canWrite($user);
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $this->canWrite($user);
    }

    private function canView(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Docente->value,
            IntranetRole::Secretaria->value,
        ]);
    }

    private function canWrite(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Docente->value,
        ]);
    }
}

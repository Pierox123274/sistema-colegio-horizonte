<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function view(User $user, Subject $subject): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function update(User $user, Subject $subject): bool
    {
        return $this->create($user);
    }
}

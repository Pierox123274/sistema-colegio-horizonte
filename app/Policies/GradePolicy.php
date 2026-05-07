<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function view(User $user, Grade $grade): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function update(User $user, Grade $grade): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function delete(User $user, Grade $grade): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }
}

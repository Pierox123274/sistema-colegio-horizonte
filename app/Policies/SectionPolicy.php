<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\Section;
use App\Models\User;

class SectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function view(User $user, Section $section): bool
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

    public function update(User $user, Section $section): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function delete(User $user, Section $section): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }
}

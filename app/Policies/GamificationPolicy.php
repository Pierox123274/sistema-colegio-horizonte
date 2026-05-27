<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\User;

class GamificationPolicy
{
    public function viewStudent(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Estudiante->value,
            IntranetRole::Administrador->value,
        ]);
    }

    public function viewTeacherSummary(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Docente->value,
            IntranetRole::Administrador->value,
        ]);
    }

    public function viewInstitution(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }
}

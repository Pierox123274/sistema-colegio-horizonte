<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\User;

class AnalyticsPolicy
{
    public function viewExecutive(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
    }

    public function viewFinancial(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
    }

    public function viewInventory(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function viewUsersMetrics(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function viewTeacher(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Docente->value,
            IntranetRole::Administrador->value,
        ]);
    }

    public function export(User $user): bool
    {
        return $this->viewExecutive($user) || $this->viewTeacher($user);
    }
}

<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\User;

class SystemOperationsPolicy
{
    public function viewHealth(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function viewJobs(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function viewBackups(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function dispatchBackup(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function viewIntegrations(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }
}

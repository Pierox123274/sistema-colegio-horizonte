<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\Pension;
use App\Models\User;

class PensionPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->financeRoles($user);
    }

    public function view(User $user, Pension $pension): bool
    {
        return $this->financeRoles($user);
    }

    public function create(User $user): bool
    {
        return $this->financeRoles($user);
    }

    public function update(User $user, Pension $pension): bool
    {
        return $this->financeRoles($user);
    }

    private function financeRoles(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
    }
}

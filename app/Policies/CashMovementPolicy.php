<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\CashMovement;
use App\Models\User;

class CashMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, CashMovement $cashMovement): bool
    {
        return $this->canManage($user);
    }

    private function canManage(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
    }
}

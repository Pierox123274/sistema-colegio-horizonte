<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\CashRegister;
use App\Models\User;

class CashRegisterPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canManage($user);
    }

    public function view(User $user, CashRegister $cashRegister): bool
    {
        return $this->canManage($user);
    }

    public function create(User $user): bool
    {
        return $this->canManage($user);
    }

    public function close(User $user, CashRegister $cashRegister): bool
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

<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\InventoryMovement;
use App\Models\User;

class InventoryMovementPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user);
    }

    public function view(User $user, InventoryMovement $inventoryMovement): bool
    {
        return $this->canView($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    private function canView(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
    }

    private function isAdmin(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }
}

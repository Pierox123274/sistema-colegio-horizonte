<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\ProductCategory;
use App\Models\User;

class ProductCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->canView($user);
    }

    public function view(User $user, ProductCategory $productCategory): bool
    {
        return $this->canView($user);
    }

    public function create(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function update(User $user, ProductCategory $productCategory): bool
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

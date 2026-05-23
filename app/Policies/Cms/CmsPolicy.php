<?php

namespace App\Policies\Cms;

use App\Enums\IntranetRole;
use App\Models\User;

abstract class CmsPolicy
{
    protected function isAdmin(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    protected function isEditor(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
    }

    protected function canAccessCms(User $user): bool
    {
        return $this->isEditor($user);
    }
}

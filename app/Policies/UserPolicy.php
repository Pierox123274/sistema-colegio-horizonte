<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value)
            || $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function update(User $user, User $model): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}

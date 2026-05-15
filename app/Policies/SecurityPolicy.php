<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\User;

class SecurityPolicy
{
    public function viewAuditLogs(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function viewSessions(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function viewLoginAttempts(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function viewAccessMonitor(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
    }

    public function revokeSessions(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function revokeOwnSessions(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);
    }
}

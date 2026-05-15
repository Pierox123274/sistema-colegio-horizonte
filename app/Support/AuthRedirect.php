<?php

namespace App\Support;

use App\Enums\IntranetRole;
use App\Models\User;

final class AuthRedirect
{
    /**
     * Ruta de destino tras autenticación según rol (Spatie).
     *
     * Prioridad: Administrador → intranet; Docente (sin administración) → portal docente;
     * resto de roles intranet hasta existan dashboards dedicados.
     */
    public static function redirectPathForUser(User $user): string
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return route('dashboard', absolute: false);
        }

        if ($user->hasRole(IntranetRole::Docente->value)) {
            return route('teacher.dashboard', absolute: false);
        }

        return route('dashboard', absolute: false);
    }
}

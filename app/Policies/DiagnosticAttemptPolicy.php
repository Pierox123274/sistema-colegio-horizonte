<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\DiagnosticAttempt;
use App\Models\User;

class DiagnosticAttemptPolicy
{
    public function interact(User $user, DiagnosticAttempt $attempt): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        return $attempt->user_id === $user->id;
    }
}

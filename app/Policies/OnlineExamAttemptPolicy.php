<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\OnlineExamAttempt;
use App\Models\User;

class OnlineExamAttemptPolicy
{
    public function interact(User $user, OnlineExamAttempt $attempt): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        return (int) $attempt->user_id === (int) $user->id;
    }
}

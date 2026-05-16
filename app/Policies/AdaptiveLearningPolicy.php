<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\User;
use App\Support\AdaptiveLearningDashboard;

class AdaptiveLearningPolicy
{
    public function viewInstitutionAdaptive(User $user, AdaptiveLearningDashboard $dashboard): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }
}

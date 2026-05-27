<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\User;

class AIPolicy
{
    public function useStudentTutor(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Estudiante->value,
            IntranetRole::Administrador->value,
        ]);
    }

    public function useTeacherInsights(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Docente->value,
            IntranetRole::Administrador->value,
        ]);
    }

    public function viewInstitutionAi(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function useTeacherCopilot(User $user): bool
    {
        return $this->useTeacherInsights($user);
    }
}

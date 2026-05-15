<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\GradeRecord;
use App\Models\User;

class GradeRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function view(User $user, GradeRecord $gradeRecord): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function update(User $user, GradeRecord $gradeRecord): bool
    {
        return $this->create($user);
    }
}

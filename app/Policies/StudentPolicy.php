<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\Student;
use App\Models\User;
use App\Services\TeacherContextService;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ])) {
            return true;
        }

        if (! $user->hasRole(IntranetRole::Docente->value)) {
            return false;
        }

        return app(TeacherContextService::class)->canDocenteViewStudent($user, $student);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
    }

    public function update(User $user, Student $student): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
    }
}

<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\TeacherAssignment;
use App\Models\User;

class TeacherAssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function view(User $user, TeacherAssignment $teacherAssignment): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function update(User $user, TeacherAssignment $teacherAssignment): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function delete(User $user, TeacherAssignment $teacherAssignment): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }
}

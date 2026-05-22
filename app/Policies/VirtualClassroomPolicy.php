<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\Student;
use App\Models\User;
use App\Models\VirtualClassroom;
use App\Services\VirtualClassroomAccessService;

class VirtualClassroomPolicy
{
    public function __construct(
        private readonly VirtualClassroomAccessService $access,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Docente->value,
            IntranetRole::Estudiante->value,
        ]);
    }

    public function view(User $user, VirtualClassroom $classroom): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        if ($user->hasRole(IntranetRole::Docente->value)) {
            return $this->access->teacherMayViewClassroom($user, $classroom);
        }

        if ($user->hasRole(IntranetRole::Estudiante->value)) {
            $student = Student::query()->where('user_id', $user->id)->first();

            return $student !== null && $this->access->studentCanAccessClassroom($student, $classroom);
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function update(User $user, VirtualClassroom $classroom): bool
    {
        return $this->access->teacherMayManageClassroom($user, $classroom)
            || $user->hasRole(IntranetRole::Administrador->value);
    }
}

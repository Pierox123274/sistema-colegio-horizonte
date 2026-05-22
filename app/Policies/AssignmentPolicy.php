<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\User;
use App\Services\VirtualClassroomAccessService;

class AssignmentPolicy
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

    public function view(User $user, Assignment $assignment): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        if ($user->hasRole(IntranetRole::Docente->value)) {
            return $this->access->teacherMayViewAssignment($user, $assignment);
        }

        if ($user->hasRole(IntranetRole::Estudiante->value)) {
            $student = Student::query()->where('user_id', $user->id)->first();

            return $student !== null && $this->access->studentCanAccessAssignment($student, $assignment);
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

    public function submit(User $user, Assignment $assignment): bool
    {
        if (! $user->hasRole(IntranetRole::Estudiante->value)) {
            return $user->hasRole(IntranetRole::Administrador->value);
        }

        $student = Student::query()->where('user_id', $user->id)->first();

        return $student !== null && $this->access->studentCanAccessAssignment($student, $assignment);
    }
}

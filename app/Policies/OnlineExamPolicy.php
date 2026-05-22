<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\OnlineExam;
use App\Models\Student;
use App\Models\User;
use App\Services\VirtualClassroomAccessService;

class OnlineExamPolicy
{
    public function __construct(
        private readonly VirtualClassroomAccessService $access,
    ) {}

    public function view(User $user, OnlineExam $exam): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        if ($user->hasRole(IntranetRole::Docente->value)) {
            return $this->access->teacherMayViewOnlineExam($user, $exam);
        }

        if ($user->hasRole(IntranetRole::Estudiante->value)) {
            $student = Student::query()->where('user_id', $user->id)->first();

            return $student !== null && $this->access->studentCanAccessOnlineExam($student, $exam);
        }

        return false;
    }

    public function take(User $user, OnlineExam $exam): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        if (! $user->hasRole(IntranetRole::Estudiante->value)) {
            return false;
        }

        $student = Student::query()->where('user_id', $user->id)->first();

        return $student !== null && $this->access->studentCanAccessOnlineExam($student, $exam);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Docente->value,
        ]);
    }
}

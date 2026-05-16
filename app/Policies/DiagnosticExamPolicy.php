<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\DiagnosticExam;
use App\Models\Student;
use App\Models\User;
use App\Services\DiagnosticExamAccessService;

class DiagnosticExamPolicy
{
    public function __construct(
        private readonly DiagnosticExamAccessService $access,
    ) {}

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);
    }

    public function view(User $user, DiagnosticExam $exam): bool
    {
        return $this->access->teacherMayViewExam($user, $exam);
    }

    /**
     * Rendición en portal estudiante (o vista previa administrador).
     */
    public function take(User $user, DiagnosticExam $exam): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        if (! $user->hasRole(IntranetRole::Estudiante->value)) {
            return false;
        }

        $student = Student::query()->where('user_id', $user->id)->first();

        return $student !== null && $this->access->studentCanAccessExam($student, $exam);
    }

    public function create(User $user): bool
    {
        if ($user->hasRole(IntranetRole::Secretaria->value)) {
            return false;
        }

        return $this->access->isAdministrator($user) || $this->access->teacherMayCreateExam($user);
    }

    public function update(User $user, DiagnosticExam $exam): bool
    {
        if ($user->hasRole(IntranetRole::Secretaria->value)) {
            return false;
        }

        return $this->access->teacherMayUpdateExam($user, $exam);
    }

    public function delete(User $user, DiagnosticExam $exam): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }
}

<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\QuestionBank;
use App\Models\User;
use App\Services\DiagnosticExamAccessService;

class QuestionBankPolicy
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

    public function view(User $user, QuestionBank $questionBank): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        if ($user->hasRole(IntranetRole::Secretaria->value)) {
            return false;
        }

        return $user->hasRole(IntranetRole::Administrador->value)
            || $user->hasRole(IntranetRole::Docente->value);
    }

    public function update(User $user, QuestionBank $questionBank): bool
    {
        if ($user->hasRole(IntranetRole::Secretaria->value)) {
            return false;
        }

        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        return $this->access->teacherMayEditQuestionBank($user, $questionBank);
    }

    public function delete(User $user, QuestionBank $questionBank): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }
}

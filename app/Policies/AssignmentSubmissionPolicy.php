<?php

namespace App\Policies;

use App\Enums\IntranetRole;
use App\Models\AssignmentSubmission;
use App\Models\Student;
use App\Models\User;
use App\Services\VirtualClassroomAccessService;

class AssignmentSubmissionPolicy
{
    public function __construct(
        private readonly VirtualClassroomAccessService $access,
    ) {}

    public function grade(User $user, AssignmentSubmission $submission): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        if (! $user->hasRole(IntranetRole::Docente->value)) {
            return false;
        }

        $submission->loadMissing('assignment.virtualClassroom');

        return $this->access->teacherMayViewClassroom($user, $submission->assignment->virtualClassroom);
    }

    public function view(User $user, AssignmentSubmission $submission): bool
    {
        if ($user->hasRole(IntranetRole::Administrador->value)) {
            return true;
        }

        if ($user->hasRole(IntranetRole::Docente->value)) {
            return $this->grade($user, $submission);
        }

        if ($user->hasRole(IntranetRole::Estudiante->value)) {
            $student = Student::query()->where('user_id', $user->id)->first();

            return $student !== null && $this->access->studentOwnsSubmission($student, $submission);
        }

        return false;
    }
}

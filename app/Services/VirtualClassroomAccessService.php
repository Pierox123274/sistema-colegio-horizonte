<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\OnlineExam;
use App\Models\Student;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Models\VirtualClassroom;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Visibilidad de aulas virtuales por matrícula y TeacherAssignment.
 */
final class VirtualClassroomAccessService
{
    public function __construct(
        private readonly TeacherContextService $teacherContext,
    ) {}

    public function isAdministrator(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    /**
     * @return Collection<int, TeacherAssignment>
     */
    public function activeAssignmentsFor(User $user): Collection
    {
        return $this->teacherContext->activeAssignmentsFor($user);
    }

    public function classroomMatchesAssignment(VirtualClassroom $classroom, TeacherAssignment $a): bool
    {
        if (! $a->is_active) {
            return false;
        }

        if ((int) $classroom->academic_year_id !== (int) $a->academic_year_id) {
            return false;
        }

        if ((int) $classroom->section_id !== (int) $a->section_id) {
            return false;
        }

        if ($a->subject_id !== null && (int) $classroom->subject_id !== (int) $a->subject_id) {
            return false;
        }

        return true;
    }

    /**
     * @return Builder<VirtualClassroom>
     */
    public function queryClassroomsForTeacher(User $user): Builder
    {
        if ($this->isAdministrator($user)) {
            return VirtualClassroom::query()->where('is_active', true)->orderByDesc('id');
        }

        if (! $user->hasRole(IntranetRole::Docente->value)) {
            return VirtualClassroom::query()->whereRaw('0=1');
        }

        $assignments = $this->activeAssignmentsFor($user);
        if ($assignments->isEmpty()) {
            return VirtualClassroom::query()->whereRaw('0=1');
        }

        return VirtualClassroom::query()
            ->where('is_active', true)
            ->where(function (Builder $q) use ($user, $assignments): void {
                $q->where('teacher_user_id', $user->id);
                foreach ($assignments as $a) {
                    $q->orWhere(function (Builder $w) use ($a): void {
                        $w->where('academic_year_id', $a->academic_year_id)
                            ->where('section_id', $a->section_id)
                            ->where('subject_id', $a->subject_id);
                    });
                }
            })
            ->orderByDesc('id');
    }

    /**
     * @return Builder<VirtualClassroom>
     */
    public function queryClassroomsForStudent(Student $student): Builder
    {
        return VirtualClassroom::query()
            ->where('is_active', true)
            ->whereExists(function ($sub) use ($student): void {
                $sub->from('enrollments as e')
                    ->where('e.student_id', $student->id)
                    ->where('e.status', EnrollmentStatus::Matriculado->value)
                    ->whereColumn('e.academic_year_id', 'virtual_classrooms.academic_year_id')
                    ->whereColumn('e.section_id', 'virtual_classrooms.section_id');
            })
            ->orderBy('title');
    }

    public function teacherMayViewClassroom(User $user, VirtualClassroom $classroom): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        if (! $user->hasRole(IntranetRole::Docente->value)) {
            return false;
        }

        if ((int) $classroom->teacher_user_id === (int) $user->id) {
            return true;
        }

        foreach ($this->activeAssignmentsFor($user) as $a) {
            if ($this->classroomMatchesAssignment($classroom, $a)) {
                return true;
            }
        }

        return false;
    }

    public function studentCanAccessClassroom(Student $student, VirtualClassroom $classroom): bool
    {
        if (! $classroom->is_active) {
            return false;
        }

        return $this->queryClassroomsForStudent($student)->whereKey($classroom->id)->exists();
    }

    public function teacherMayManageClassroom(User $user, VirtualClassroom $classroom): bool
    {
        return $this->teacherMayViewClassroom($user, $classroom);
    }

    /**
     * @param  array{academic_year_id: int, section_id: int, subject_id: int}  $attrs
     */
    public function teacherMayCreateClassroomWith(User $user, array $attrs): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        if (! $user->hasRole(IntranetRole::Docente->value)) {
            return false;
        }

        $temp = new VirtualClassroom([
            'academic_year_id' => (int) $attrs['academic_year_id'],
            'section_id' => (int) $attrs['section_id'],
            'subject_id' => (int) $attrs['subject_id'],
        ]);

        foreach ($this->activeAssignmentsFor($user) as $a) {
            if ($this->classroomMatchesAssignment($temp, $a)) {
                return true;
            }
        }

        return false;
    }

    public function teacherMayViewAssignment(User $user, Assignment $assignment): bool
    {
        $assignment->loadMissing('virtualClassroom');

        return $this->teacherMayViewClassroom($user, $assignment->virtualClassroom);
    }

    public function studentCanAccessAssignment(Student $student, Assignment $assignment): bool
    {
        if (! $assignment->is_published) {
            return false;
        }

        $assignment->loadMissing('virtualClassroom');

        return $this->studentCanAccessClassroom($student, $assignment->virtualClassroom);
    }

    public function teacherMayViewOnlineExam(User $user, OnlineExam $exam): bool
    {
        $exam->loadMissing('virtualClassroom');

        return $this->teacherMayViewClassroom($user, $exam->virtualClassroom);
    }

    public function studentCanAccessOnlineExam(Student $student, OnlineExam $exam): bool
    {
        if (! $exam->is_published) {
            return false;
        }

        $now = now();
        if ($exam->available_from !== null && $now->lt($exam->available_from)) {
            return false;
        }
        if ($exam->available_until !== null && $now->gt($exam->available_until)) {
            return false;
        }

        $exam->loadMissing('virtualClassroom');

        return $this->studentCanAccessClassroom($student, $exam->virtualClassroom);
    }

    public function studentOwnsSubmission(Student $student, AssignmentSubmission $submission): bool
    {
        return (int) $submission->student_id === (int) $student->id;
    }
}

<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\DiagnosticExam;
use App\Models\Enrollment;
use App\Models\QuestionBank;
use App\Models\Student;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * Visibilidad de diagnósticos para estudiantes y permisos de gestión por rol / TeacherAssignment.
 */
final class DiagnosticExamAccessService
{
    public function __construct(
        private readonly TeacherContextService $teacherContext,
    ) {}

    public function isAdministrator(User $user): bool
    {
        return $user->hasRole(IntranetRole::Administrador->value);
    }

    public function isSecretary(User $user): bool
    {
        return $user->hasRole(IntranetRole::Secretaria->value);
    }

    public function isTeacher(User $user): bool
    {
        return $user->hasRole(IntranetRole::Docente->value);
    }

    /**
     * @return Collection<int, TeacherAssignment>
     */
    public function activeAssignmentsFor(User $user): Collection
    {
        return $this->teacherContext->activeAssignmentsFor($user);
    }

    /**
     * @return Builder<DiagnosticExam>
     */
    public function queryExamsForStudent(Student $student): Builder
    {
        return DiagnosticExam::query()
            ->where('is_active', true)
            ->where(function (Builder $outer) use ($student): void {
                $outer->where(function (Builder $global): void {
                    $global->whereNull('academic_year_id')
                        ->whereNull('section_id')
                        ->whereNull('grade_id');
                })->orWhereExists(function ($sub) use ($student): void {
                    $sub->from('enrollments as e')
                        ->where('e.student_id', $student->id)
                        ->where('e.status', EnrollmentStatus::Matriculado->value)
                        ->where(function ($w): void {
                            $w->whereNull('diagnostic_exams.academic_year_id')
                                ->orWhereColumn('e.academic_year_id', 'diagnostic_exams.academic_year_id');
                        })
                        ->where(function ($w): void {
                            $w->whereNull('diagnostic_exams.section_id')
                                ->orWhereColumn('e.section_id', 'diagnostic_exams.section_id');
                        })
                        ->where(function ($w): void {
                            $w->whereNull('diagnostic_exams.grade_id')
                                ->orWhereColumn('e.grade_id', 'diagnostic_exams.grade_id');
                        })
                        ->where(function ($w): void {
                            $w->whereNull('diagnostic_exams.educational_level_id')
                                ->orWhereColumn('e.educational_level_id', 'diagnostic_exams.educational_level_id');
                        });
                });
            });
    }

    public function studentCanAccessExam(Student $student, DiagnosticExam $exam): bool
    {
        if (! $exam->is_active) {
            return false;
        }

        return $this->queryExamsForStudent($student)->whereKey($exam->id)->exists();
    }

    public function examMatchesAssignment(DiagnosticExam $exam, TeacherAssignment $a): bool
    {
        if (! $a->is_active) {
            return false;
        }

        if ($exam->academic_year_id !== null && (int) $exam->academic_year_id !== (int) $a->academic_year_id) {
            return false;
        }

        if ($exam->section_id !== null && (int) $exam->section_id !== (int) $a->section_id) {
            return false;
        }

        if ($a->is_tutor) {
            return $exam->section_id === null || (int) $exam->section_id === (int) $a->section_id;
        }

        if ($exam->subject_id !== null && $a->subject_id !== null
            && (int) $exam->subject_id !== (int) $a->subject_id) {
            return false;
        }

        return true;
    }

    public function teacherMayViewExam(User $user, DiagnosticExam $exam): bool
    {
        if ($this->isAdministrator($user) || $this->isSecretary($user)) {
            return true;
        }

        if (! $this->isTeacher($user)) {
            return false;
        }

        if ((int) $exam->created_by_user_id === (int) $user->id) {
            return true;
        }

        foreach ($this->activeAssignmentsFor($user) as $a) {
            if ($this->examMatchesAssignment($exam, $a)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return Builder<DiagnosticExam>
     */
    public function queryExamsForTeacher(User $user): Builder
    {
        if ($this->isAdministrator($user) || $this->isSecretary($user)) {
            return DiagnosticExam::query()->orderByDesc('id');
        }

        if (! $this->isTeacher($user)) {
            return DiagnosticExam::query()->whereRaw('0=1');
        }

        $userId = $user->id;

        return DiagnosticExam::query()
            ->where(function (Builder $q) use ($userId, $user): void {
                $q->where('created_by_user_id', $userId);
                foreach ($this->activeAssignmentsFor($user) as $a) {
                    $q->orWhere(function (Builder $w) use ($a): void {
                        $w->where('academic_year_id', $a->academic_year_id)
                            ->where('section_id', $a->section_id);
                        if (! $a->is_tutor) {
                            $w->where(function ($x) use ($a): void {
                                $x->whereNull('subject_id')
                                    ->orWhere('subject_id', $a->subject_id);
                            });
                        }
                    });
                }
            })
            ->orderByDesc('id');
    }

    /**
     * @param  array{academic_year_id?: int|null, section_id?: int|null, grade_id?: int|null, educational_level_id?: int|null, subject_id?: int|null}  $attrs
     */
    public function teacherMayCreateExamWith(User $user, array $attrs): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        if (! $this->isTeacher($user)) {
            return false;
        }

        $yearId = isset($attrs['academic_year_id']) ? (int) $attrs['academic_year_id'] : 0;
        $sectionId = isset($attrs['section_id']) ? (int) $attrs['section_id'] : 0;
        if ($yearId < 1 || $sectionId < 1) {
            return false;
        }

        $temp = new DiagnosticExam([
            'academic_year_id' => $yearId,
            'section_id' => $sectionId,
            'grade_id' => $attrs['grade_id'] ?? null,
            'educational_level_id' => $attrs['educational_level_id'] ?? null,
            'subject_id' => $attrs['subject_id'] ?? null,
        ]);

        foreach ($this->activeAssignmentsFor($user) as $a) {
            if ((int) $a->academic_year_id !== $yearId || (int) $a->section_id !== $sectionId) {
                continue;
            }
            if ($this->examMatchesAssignment($temp, $a)) {
                return true;
            }
        }

        return false;
    }

    public function teacherMayUpdateExam(User $user, DiagnosticExam $exam): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        if ($this->isSecretary($user)) {
            return false;
        }

        if (! $this->isTeacher($user)) {
            return false;
        }

        return $this->teacherMayViewExam($user, $exam);
    }

    public function teacherMayCreateExam(User $user): bool
    {
        return $this->isAdministrator($user)
            || ($this->isTeacher($user) && $this->activeAssignmentsFor($user)->isNotEmpty());
    }

    public function teacherMayManageQuestionBank(User $user, ?int $subjectId, ?int $sectionId, ?int $academicYearId): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        if ($this->isSecretary($user)) {
            return false;
        }

        if (! $this->isTeacher($user) || $subjectId === null || $sectionId === null || $academicYearId === null) {
            return false;
        }

        return $this->teacherMayCreateExamWith($user, [
            'academic_year_id' => $academicYearId,
            'section_id' => $sectionId,
            'subject_id' => $subjectId,
        ]);
    }

    public function teacherMayEditQuestionBank(User $user, QuestionBank $questionBank): bool
    {
        if ($this->isAdministrator($user)) {
            return true;
        }

        if (! $this->isTeacher($user) || $questionBank->subject_id === null) {
            return false;
        }

        foreach ($this->activeAssignmentsFor($user) as $a) {
            if ($a->subject_id !== null && (int) $a->subject_id === (int) $questionBank->subject_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Para listados de intentos en portal docente: restringe a estudiantes de secciones asignadas.
     * null = sin filtro (administrador en portal docente).
     *
     * @return list<int>|null
     */
    public function teacherScopedStudentIdsForDiagnosticResults(User $user, DiagnosticExam $exam): ?array
    {
        if ($this->isAdministrator($user)) {
            return null;
        }

        if (! $this->isTeacher($user)) {
            return [];
        }

        $assignments = $this->activeAssignmentsFor($user);
        if ($assignments->isEmpty()) {
            return [];
        }

        $yearId = $exam->academic_year_id ?? $this->teacherContext->activeAcademicYear()?->id;
        if ($yearId === null) {
            return [];
        }

        $relevantSectionIds = $assignments
            ->filter(fn (TeacherAssignment $a): bool => (int) $a->academic_year_id === (int) $yearId)
            ->pluck('section_id')
            ->unique()
            ->values();

        if ($exam->section_id !== null) {
            $sid = (int) $exam->section_id;
            if (! $relevantSectionIds->contains($sid)) {
                return [];
            }
            $sectionIds = collect([$sid]);
        } else {
            $sectionIds = $relevantSectionIds;
        }

        return Enrollment::query()
            ->where('academic_year_id', $yearId)
            ->whereIn('section_id', $sectionIds->all())
            ->where('status', EnrollmentStatus::Matriculado->value)
            ->distinct()
            ->pluck('student_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->values()
            ->all();
    }
}

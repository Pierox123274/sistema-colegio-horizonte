<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use App\Models\Student;
use App\Models\TeacherAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final class TeacherContextService
{
    public function activeAcademicYear(): ?AcademicYear
    {
        return AcademicYear::query()->where('is_active', true)->first();
    }

    /**
     * Secciones asignadas al docente en el año académico activo (asignaciones activas).
     *
     * @return list<int>
     */
    public function activeSectionIdsFor(User $user): array
    {
        $year = $this->activeAcademicYear();
        if ($year === null) {
            return [];
        }

        return TeacherAssignment::query()
            ->where('user_id', $user->id)
            ->where('academic_year_id', $year->id)
            ->where('is_active', true)
            ->pluck('section_id')
            ->unique()
            ->values()
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    /**
     * @return Collection<int, TeacherAssignment>
     */
    public function activeAssignmentsFor(User $user): Collection
    {
        $year = $this->activeAcademicYear();
        if ($year === null) {
            return new Collection;
        }

        return TeacherAssignment::query()
            ->where('user_id', $user->id)
            ->where('academic_year_id', $year->id)
            ->where('is_active', true)
            ->with([
                'section:id,name,grade_id',
                'section.grade:id,name,educational_level_id',
                'section.grade.educationalLevel:id,name',
                'subject:id,name',
            ])
            ->orderBy('is_tutor', 'desc')
            ->orderBy('id')
            ->get();
    }

    public function canDocenteViewStudent(User $user, Student $student): bool
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

        $sectionIds = $this->activeSectionIdsFor($user);
        if ($sectionIds === []) {
            return false;
        }

        $year = $this->activeAcademicYear();
        if ($year === null) {
            return false;
        }

        return Enrollment::query()
            ->where('student_id', $student->id)
            ->where('academic_year_id', $year->id)
            ->where('status', EnrollmentStatus::Matriculado->value)
            ->whereIn('section_id', $sectionIds)
            ->exists();
    }

    /**
     * @param  list<int>  $sectionIds
     * @return array<string, int>
     */
    public function dashboardStats(User $user, array $sectionIds): array
    {
        $year = $this->activeAcademicYear();
        if ($year === null || $sectionIds === []) {
            return [
                'enrolled_students' => 0,
                'attendance_records_week' => 0,
                'subjects_count' => 0,
                'evaluations_count' => 0,
                'grade_records_count' => 0,
            ];
        }

        $enrolledCount = Student::query()
            ->whereHas('enrollments', function ($q) use ($year, $sectionIds): void {
                $q->where('academic_year_id', $year->id)
                    ->where('status', EnrollmentStatus::Matriculado->value)
                    ->whereIn('section_id', $sectionIds);
            })
            ->count();

        $attendanceWeek = Attendance::query()
            ->whereIn('section_id', $sectionIds)
            ->where('attendance_date', '>=', now()->subDays(7)->toDateString())
            ->count();

        $subjectIdsFromAssignments = TeacherAssignment::query()
            ->where('user_id', $user->id)
            ->where('academic_year_id', $year->id)
            ->where('is_active', true)
            ->whereNotNull('subject_id')
            ->distinct()
            ->pluck('subject_id');

        $evalQuery = Evaluation::query()->where('academic_year_id', $year->id)->whereIn('section_id', $sectionIds);
        $evaluationSubjectIds = (clone $evalQuery)->pluck('subject_id')->unique()->filter();

        $subjectsCount = $subjectIdsFromAssignments->merge($evaluationSubjectIds)->unique()->count();

        $evaluationsCount = (clone $evalQuery)->count();

        $gradeRecordsCount = GradeRecord::query()
            ->whereHas('evaluation', function ($q) use ($year, $sectionIds): void {
                $q->where('academic_year_id', $year->id)
                    ->whereIn('section_id', $sectionIds);
            })
            ->count();

        return [
            'enrolled_students' => $enrolledCount,
            'attendance_records_week' => $attendanceWeek,
            'subjects_count' => $subjectsCount,
            'evaluations_count' => $evaluationsCount,
            'grade_records_count' => $gradeRecordsCount,
        ];
    }
}

<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\AcademicCalendarEvent;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;

final class LMSCalendarService
{
    public function __construct(
        private readonly VirtualClassroomAccessService $access,
        private readonly TeacherContextService $teacherContext,
    ) {}

    /**
     * @return Collection<int, AcademicCalendarEvent>
     */
    public function eventsForTeacher(User $user, ?string $from = null, ?string $to = null): Collection
    {
        $classroomIds = $this->access->queryClassroomsForTeacher($user)->pluck('id');
        $sectionIds = $this->teacherContext->activeSectionIdsFor($user);

        $year = $this->teacherContext->activeAcademicYear();

        $query = AcademicCalendarEvent::query();
        if ($year !== null) {
            $query->where('academic_year_id', $year->id);
        }
        if ($sectionIds !== []) {
            $query->whereIn('section_id', $sectionIds);
        } else {
            $query->whereRaw('0=1');
        }

        if ($from !== null) {
            $query->where('starts_at', '>=', $from);
        }
        if ($to !== null) {
            $query->where('starts_at', '<=', $to);
        }

        return $query->orderBy('starts_at')->limit(200)->get();
    }

    /**
     * @return Collection<int, AcademicCalendarEvent>
     */
    public function eventsForStudent(Student $student, ?string $from = null, ?string $to = null): Collection
    {
        $classroomIds = $this->access->queryClassroomsForStudent($student)->pluck('id');

        $sectionIds = Enrollment::query()
            ->where('student_id', $student->id)
            ->where('status', EnrollmentStatus::Matriculado->value)
            ->pluck('section_id');

        $query = AcademicCalendarEvent::query()
            ->where(function ($q) use ($student, $sectionIds): void {
                $q->where('student_id', $student->id)
                    ->orWhereIn('section_id', $sectionIds);
            });

        if ($from !== null) {
            $query->where('starts_at', '>=', $from);
        }
        if ($to !== null) {
            $query->where('starts_at', '<=', $to);
        }

        return $query->orderBy('starts_at')->limit(200)->get();
    }
}

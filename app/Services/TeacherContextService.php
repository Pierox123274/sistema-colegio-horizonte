<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Enums\IntranetRole;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Evaluation;
use App\Models\GradeRecord;
use App\Models\Section;
use App\Models\Student;
use App\Models\TeacherAssignment;
use App\Models\User;
use App\Support\EncryptedPersonalDataSearch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

final class TeacherContextService
{
    private const CLASSROOM_TUTOR_LABEL = 'Tutor de aula';

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
                'academicYear:id,name,year',
                'educationalLevel:id,name',
                'grade:id,name',
                'section:id,name,grade_id',
                'section.grade:id,name,educational_level_id',
                'section.grade.educationalLevel:id,name',
                'subject:id,name',
            ])
            ->orderBy('is_tutor', 'desc')
            ->orderBy('id')
            ->get();
    }

    /**
     * Filas para tabla «Mis asignaciones» en el portal docente.
     *
     * @return list<array<string, mixed>>
     */
    public function assignmentsTableFor(User $user): array
    {
        $year = $this->activeAcademicYear();
        if ($year === null) {
            return [];
        }

        return $this->activeAssignmentsFor($user)->map(function (TeacherAssignment $a) use ($year): array {
            $studentsCount = Enrollment::query()
                ->where('academic_year_id', $year->id)
                ->where('section_id', $a->section_id)
                ->where('status', EnrollmentStatus::Matriculado->value)
                ->distinct()
                ->count('student_id');

            return [
                'id' => $a->id,
                'academic_year' => $a->academicYear?->name ?? $year->name,
                'academic_year_year' => $a->academicYear?->year ?? $year->year,
                'level' => $a->educationalLevel?->name ?? $a->section?->grade?->educationalLevel?->name,
                'grade' => $a->grade?->name ?? $a->section?->grade?->name,
                'section' => $a->section?->name,
                'section_id' => $a->section_id,
                'subject' => $a->subject?->name,
                'subject_id' => $a->subject_id,
                'is_tutor' => $a->is_tutor,
                'role_label' => $this->assignmentRoleLabel($a),
                'students_count' => $studentsCount,
            ];
        })->values()->all();
    }

    /**
     * Vista agrupada: secciones, cursos y alumnos para «Mis asignaciones».
     *
     * @return array<string, mixed>
     */
    public function assignmentsOverviewFor(User $user): array
    {
        $year = $this->activeAcademicYear();
        if ($year === null) {
            return $this->emptyAssignmentsOverview();
        }

        $assignments = $this->activeAssignmentsFor($user);
        if ($assignments->isEmpty()) {
            return array_merge($this->emptyAssignmentsOverview(), [
                'academic_year' => $year->only(['id', 'name', 'year']),
            ]);
        }

        $sectionIds = $assignments->pluck('section_id')->unique()->values()->all();

        $enrollmentsBySection = Enrollment::query()
            ->where('academic_year_id', $year->id)
            ->where('status', EnrollmentStatus::Matriculado->value)
            ->whereIn('section_id', $sectionIds)
            ->with(['student:id,code,first_name,last_name'])
            ->get()
            ->groupBy('section_id');

        $sectionsMap = [];
        $courseAssignments = [];

        foreach ($assignments as $assignment) {
            $sectionId = (int) $assignment->section_id;
            $levelName = $assignment->educationalLevel?->name
                ?? $assignment->section?->grade?->educationalLevel?->name;
            $gradeName = $assignment->grade?->name ?? $assignment->section?->grade?->name;
            $sectionName = $assignment->section?->name ?? '—';
            $sectionLabel = trim(implode(' · ', array_filter([$levelName, $gradeName, $sectionName])));

            $courseAssignments[] = [
                'id' => $assignment->id,
                'section_id' => $sectionId,
                'section_label' => $sectionLabel,
                'level' => $levelName,
                'grade' => $gradeName,
                'section' => $sectionName,
                'subject' => $assignment->subject?->name,
                'subject_id' => $assignment->subject_id,
                'is_tutor' => $assignment->is_tutor,
                'role_label' => $this->assignmentRoleLabel($assignment),
            ];

            if (! isset($sectionsMap[$sectionId])) {
                $students = ($enrollmentsBySection->get($sectionId) ?? collect())
                    ->map(fn (Enrollment $enrollment): array => [
                        'id' => $enrollment->student?->id,
                        'code' => $enrollment->student?->code,
                        'first_name' => $enrollment->student?->first_name,
                        'last_name' => $enrollment->student?->last_name,
                    ])
                    ->filter(fn (array $s): bool => $s['id'] !== null)
                    ->sortBy('last_name')
                    ->values()
                    ->all();

                $sectionsMap[$sectionId] = [
                    'section_id' => $sectionId,
                    'academic_year' => $assignment->academicYear?->name ?? $year->name,
                    'level' => $levelName,
                    'grade' => $gradeName,
                    'section' => $sectionName,
                    'is_tutor' => false,
                    'courses' => [],
                    'students_count' => count($students),
                    'students' => $students,
                    'links' => [
                        'students' => route('teacher.students.index', ['section_id' => $sectionId], false),
                        'attendance' => route('teacher.attendance.create', ['section_id' => $sectionId], false),
                        'grades' => route('teacher.grades.records', ['section_id' => $sectionId], false),
                    ],
                ];
            }

            if ($assignment->is_tutor) {
                $sectionsMap[$sectionId]['is_tutor'] = true;
            }

            if ($assignment->subject_id !== null && $assignment->subject !== null) {
                $sectionsMap[$sectionId]['courses'][$assignment->subject_id] = [
                    'id' => $assignment->subject_id,
                    'name' => $assignment->subject->name,
                ];
            }
        }

        $sections = array_values(array_map(function (array $section): array {
            $section['courses'] = array_values($section['courses']);
            $section['role_label'] = $this->sectionRoleLabel($section);

            return $section;
        }, $sectionsMap));

        $coursesGrouped = $this->groupCoursesForAssignments($assignments, $year, $enrollmentsBySection);
        $studentsRows = $this->studentsRowsForAssignments($assignments, $enrollmentsBySection);
        $uniqueStudentIds = collect($studentsRows)->pluck('id')->unique();

        $tutoriasCount = collect($sections)->where('is_tutor', true)->count();
        $uniqueCourseIds = $assignments->pluck('subject_id')->filter()->unique()->count();

        return [
            'academic_year' => $year->only(['id', 'name', 'year']),
            'summary' => [
                'sections_count' => count($sections),
                'courses_count' => $uniqueCourseIds,
                'students_count' => $uniqueStudentIds->count(),
                'tutorias_count' => $tutoriasCount,
            ],
            'sections' => $sections,
            'sections_grouped' => $this->groupSectionsByHierarchy($sections),
            'courses_grouped' => $coursesGrouped,
            'students' => $studentsRows,
            'course_assignments' => $courseAssignments,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $sections
     * @return list<array<string, mixed>>
     */
    private function groupSectionsByHierarchy(array $sections): array
    {
        $levels = [];

        foreach ($sections as $section) {
            $levelKey = (string) ($section['level'] ?? 'general');
            $gradeKey = (string) ($section['grade'] ?? 'general');

            if (! isset($levels[$levelKey])) {
                $levels[$levelKey] = [
                    'level' => $section['level'] ?? '—',
                    'grades' => [],
                ];
            }

            if (! isset($levels[$levelKey]['grades'][$gradeKey])) {
                $levels[$levelKey]['grades'][$gradeKey] = [
                    'grade' => $section['grade'] ?? '—',
                    'sections' => [],
                ];
            }

            $levels[$levelKey]['grades'][$gradeKey]['sections'][] = $section;
        }

        return array_values(array_map(function (array $levelGroup): array {
            $levelGroup['grades'] = array_values($levelGroup['grades']);

            return $levelGroup;
        }, $levels));
    }

    /**
     * @param  Collection<int, TeacherAssignment>  $assignments
     * @param  \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, Enrollment>>  $enrollmentsBySection
     * @return list<array<string, mixed>>
     */
    private function groupCoursesForAssignments(Collection $assignments, AcademicYear $year, $enrollmentsBySection): array
    {
        $groups = [];

        foreach ($assignments as $assignment) {
            if ($assignment->subject_id === null || $assignment->subject === null) {
                continue;
            }

            $subjectId = (int) $assignment->subject_id;
            $sectionId = (int) $assignment->section_id;
            $levelName = $assignment->educationalLevel?->name
                ?? $assignment->section?->grade?->educationalLevel?->name;
            $gradeName = $assignment->grade?->name ?? $assignment->section?->grade?->name;
            $sectionName = $assignment->section?->name ?? '—';

            if (! isset($groups[$subjectId])) {
                $groups[$subjectId] = [
                    'subject_id' => $subjectId,
                    'subject_name' => $assignment->subject->name,
                    'items' => [],
                ];
            }

            $studentsCount = ($enrollmentsBySection->get($sectionId) ?? collect())->count();

            $evaluationsCount = Evaluation::query()
                ->where('academic_year_id', $year->id)
                ->where('section_id', $sectionId)
                ->where('subject_id', $subjectId)
                ->where('is_active', true)
                ->count();

            $groups[$subjectId]['items'][] = [
                'assignment_id' => $assignment->id,
                'section_id' => $sectionId,
                'level' => $levelName,
                'grade' => $gradeName,
                'section' => $sectionName,
                'section_label' => trim(implode(' · ', array_filter([$levelName, $gradeName, $sectionName]))),
                'is_tutor' => $assignment->is_tutor,
                'role_label' => $this->assignmentRoleLabel($assignment),
                'students_count' => $studentsCount,
                'evaluations_count' => $evaluationsCount,
                'links' => [
                    'grades' => route('teacher.grades.records', [
                        'section_id' => $sectionId,
                        'subject_id' => $subjectId,
                    ], false),
                    'students' => route('teacher.students.index', ['section_id' => $sectionId], false),
                ],
            ];
        }

        return array_values(array_map(function (array $group): array {
            usort($group['items'], fn (array $a, array $b): int => strcmp($a['section_label'], $b['section_label']));

            return $group;
        }, $groups));
    }

    /**
     * @param  Collection<int, TeacherAssignment>  $assignments
     * @param  \Illuminate\Support\Collection<int, \Illuminate\Support\Collection<int, Enrollment>>  $enrollmentsBySection
     * @return list<array<string, mixed>>
     */
    private function studentsRowsForAssignments(Collection $assignments, $enrollmentsBySection): array
    {
        $subjectsBySection = $this->subjectsBySectionFromAssignments($assignments);
        $rows = [];

        foreach ($enrollmentsBySection as $sectionId => $enrollments) {
            $assignment = $assignments->firstWhere('section_id', $sectionId);
            $sectionMeta = $this->sectionMetaForAssignment($assignment);
            $courseNames = array_values($subjectsBySection[$sectionId] ?? []);

            foreach ($enrollments as $enrollment) {
                $row = $this->studentRowFromEnrollment($enrollment, (int) $sectionId, $sectionMeta, $courseNames);
                if ($row !== null) {
                    $rows[$row['id']] = $row;
                }
            }
        }

        return collect($rows)
            ->sortBy(['last_name', 'first_name'])
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, TeacherAssignment>  $assignments
     * @return array<int, array<int, string>>
     */
    private function subjectsBySectionFromAssignments(Collection $assignments): array
    {
        $subjectsBySection = [];
        foreach ($assignments as $assignment) {
            $sectionId = (int) $assignment->section_id;
            if ($assignment->subject_id !== null && $assignment->subject !== null) {
                $subjectsBySection[$sectionId][$assignment->subject_id] = $assignment->subject->name;
            }
        }

        return $subjectsBySection;
    }

    /**
     * @return array{section_label: string}
     */
    private function sectionMetaForAssignment(?TeacherAssignment $assignment): array
    {
        $levelName = $assignment?->educationalLevel?->name
            ?? $assignment?->section?->grade?->educationalLevel?->name;
        $gradeName = $assignment?->grade?->name ?? $assignment?->section?->grade?->name;
        $sectionName = $assignment?->section?->name ?? '—';

        return [
            'section_label' => trim(implode(' · ', array_filter([$levelName, $gradeName, $sectionName]))),
        ];
    }

    /**
     * @param  list<string>  $courseNames
     * @return array<string, mixed>|null
     */
    private function studentRowFromEnrollment($enrollment, int $sectionId, array $sectionMeta, array $courseNames): ?array
    {
        $student = $enrollment->student;
        if ($student === null) {
            return null;
        }

        $status = $enrollment->status instanceof EnrollmentStatus
            ? $enrollment->status->value
            : (string) $enrollment->status;

        return [
            'id' => $student->id,
            'code' => $student->code,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'section_id' => $sectionId,
            'section_label' => $sectionMeta['section_label'],
            'courses' => $courseNames,
            'courses_label' => $courseNames !== [] ? implode(', ', $courseNames) : '—',
            'enrollment_status' => $status,
            'enrollment_status_label' => Str::ucfirst($status),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     */
    private function sectionRoleLabel(array $section): string
    {
        if ($section['is_tutor'] && $section['courses'] !== []) {
            return self::CLASSROOM_TUTOR_LABEL;
        }

        if ($section['is_tutor']) {
            return self::CLASSROOM_TUTOR_LABEL;
        }

        if ($section['courses'] !== []) {
            return 'Docente de curso';
        }

        return 'Docente asignado';
    }

    /**
     * Opciones de sección para filtros del portal docente.
     *
     * @return list<array{value: string, label: string}>
     */
    public function sectionFilterOptionsFor(User $user): array
    {
        $seen = [];
        $options = [];

        foreach ($this->activeAssignmentsFor($user) as $assignment) {
            $sectionId = (string) $assignment->section_id;
            if (isset($seen[$sectionId])) {
                continue;
            }
            $seen[$sectionId] = true;

            $levelName = $assignment->educationalLevel?->name
                ?? $assignment->section?->grade?->educationalLevel?->name;
            $gradeName = $assignment->grade?->name ?? $assignment->section?->grade?->name;
            $sectionName = $assignment->section?->name ?? 'Sección';

            $options[] = [
                'value' => $sectionId,
                'label' => trim(implode(' · ', array_filter([$levelName, $gradeName, $sectionName]))),
            ];
        }

        return $options;
    }

    /**
     * Cursos asignados al docente (año activo) para filtros de notas.
     *
     * @return list<array{value: string, label: string}>
     */
    public function subjectFilterOptionsFor(User $user): array
    {
        $year = $this->activeAcademicYear();
        if ($year === null) {
            return [];
        }

        return TeacherAssignment::query()
            ->where('user_id', $user->id)
            ->where('academic_year_id', $year->id)
            ->where('is_active', true)
            ->whereNotNull('subject_id')
            ->with('subject:id,name')
            ->get()
            ->unique('subject_id')
            ->map(fn (TeacherAssignment $a): array => [
                'value' => (string) $a->subject_id,
                'label' => $a->subject?->name ?? 'Curso',
            ])
            ->values()
            ->all();
    }

    /**
     * Estudiantes agrupados por sección asignada (año activo).
     *
     * @return list<array<string, mixed>>
     */
    public function studentsGroupedBySection(User $user, ?string $search = null, ?int $sectionId = null): array
    {
        $year = $this->activeAcademicYear();
        if ($year === null) {
            return [];
        }

        $sectionIds = $this->activeSectionIdsFor($user);
        if ($sectionIds === []) {
            return [];
        }

        if ($sectionId !== null) {
            if (! in_array($sectionId, $sectionIds, true)) {
                return [];
            }
            $sectionIds = [$sectionId];
        }

        $assignments = $this->activeAssignmentsFor($user);
        $coursesBySection = [];
        foreach ($assignments as $assignment) {
            $sid = (int) $assignment->section_id;
            if ($assignment->subject_id !== null && $assignment->subject !== null) {
                $coursesBySection[$sid][$assignment->subject_id] = [
                    'id' => $assignment->subject_id,
                    'name' => $assignment->subject->name,
                ];
            }
        }

        $groups = [];

        foreach ($sectionIds as $sid) {
            $assignment = $assignments->firstWhere('section_id', $sid);
            if ($assignment === null) {
                continue;
            }

            $studentQuery = Student::query()
                ->whereHas('enrollments', function ($q) use ($year, $sid): void {
                    $q->where('academic_year_id', $year->id)
                        ->where('section_id', $sid)
                        ->where('status', EnrollmentStatus::Matriculado->value);
                })
                ->orderBy('last_name')
                ->orderBy('first_name');

            if ($search !== null && trim($search) !== '') {
                $term = trim($search);
                $studentQuery->where(function ($q) use ($term): void {
                    EncryptedPersonalDataSearch::applyDocumentOrTextSearch(
                        $q,
                        $term,
                        ['first_name', 'last_name', 'code'],
                    );
                });
            }

            $students = $studentQuery
                ->get(['id', 'code', 'first_name', 'last_name', 'document_number', 'educational_level', 'status', 'grade', 'section'])
                ->map(fn (Student $s): array => [
                    'id' => $s->id,
                    'code' => $s->code,
                    'first_name' => $s->first_name,
                    'last_name' => $s->last_name,
                    'document_number' => $s->document_number,
                    'educational_level' => $s->educational_level,
                    'status' => $s->status,
                    'grade' => $s->grade,
                    'section' => $s->section,
                ])
                ->values()
                ->all();

            $levelName = $assignment->educationalLevel?->name
                ?? $assignment->section?->grade?->educationalLevel?->name;
            $gradeName = $assignment->grade?->name ?? $assignment->section?->grade?->name;

            $groups[] = [
                'section_id' => $sid,
                'label' => trim(implode(' · ', array_filter([$levelName, $gradeName, $assignment->section?->name]))),
                'level' => $levelName,
                'grade' => $gradeName,
                'section' => $assignment->section?->name,
                'is_tutor' => $assignments->where('section_id', $sid)->contains(fn (TeacherAssignment $a): bool => $a->is_tutor),
                'courses' => array_values($coursesBySection[$sid] ?? []),
                'students' => $students,
            ];
        }

        return $groups;
    }

    public function emptyAssignmentsMessage(): string
    {
        return 'Aún no tienes secciones o cursos asignados. Contacta al administrador académico.';
    }

    /**
     * @return array<string, mixed>
     */
    private function emptyAssignmentsOverview(): array
    {
        return [
            'academic_year' => null,
            'summary' => [
                'sections_count' => 0,
                'courses_count' => 0,
                'students_count' => 0,
                'tutorias_count' => 0,
            ],
            'sections' => [],
            'sections_grouped' => [],
            'courses_grouped' => [],
            'students' => [],
            'course_assignments' => [],
        ];
    }

    private function assignmentRoleLabel(TeacherAssignment $assignment): string
    {
        if ($assignment->is_tutor) {
            return self::CLASSROOM_TUTOR_LABEL;
        }

        if ($assignment->subject_id !== null) {
            return 'Docente de curso';
        }

        return 'Docente asignado';
    }

    public function isDocentePortalScoped(User $user): bool
    {
        return $user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasAnyRole([
                IntranetRole::Administrador->value,
                IntranetRole::Secretaria->value,
            ]);
    }

    /**
     * @return list<int>
     */
    public function sectionIdsFor(User $user): array
    {
        if (! $this->isDocentePortalScoped($user)) {
            return [];
        }

        return $this->activeSectionIdsFor($user);
    }

    /**
     * Catálogo de niveles/grados/secciones limitado a asignaciones del docente (año activo).
     *
     * @return array<string, mixed>
     */
    public function attendanceCatalogFor(User $user): array
    {
        $year = $this->activeAcademicYear();
        $sectionIds = $this->activeSectionIdsFor($user);

        if ($year === null || $sectionIds === []) {
            return [
                'academic_years' => [],
                'levels' => [],
            ];
        }

        $sections = Section::query()
            ->whereIn('id', $sectionIds)
            ->with(['grade.educationalLevel'])
            ->get();

        $levelsMap = [];
        foreach ($sections as $section) {
            $level = $section->grade?->educationalLevel;
            $grade = $section->grade;
            if ($level === null || $grade === null) {
                continue;
            }
            $levelId = $level->id;
            if (! isset($levelsMap[$levelId])) {
                $levelsMap[$levelId] = [
                    'id' => $levelId,
                    'name' => $level->name,
                    'grades' => [],
                ];
            }
            $gradeId = $grade->id;
            if (! isset($levelsMap[$levelId]['grades'][$gradeId])) {
                $levelsMap[$levelId]['grades'][$gradeId] = [
                    'id' => $gradeId,
                    'name' => $grade->name,
                    'sections' => [],
                ];
            }
            $levelsMap[$levelId]['grades'][$gradeId]['sections'][] = [
                'id' => $section->id,
                'name' => $section->name,
            ];
        }

        $levels = collect($levelsMap)->map(function (array $level): array {
            $level['grades'] = array_values($level['grades']);

            return $level;
        })->values()->all();

        return [
            'academic_years' => [[
                'value' => (string) $year->id,
                'label' => $year->name.' ('.$year->year.')',
                'is_active' => true,
            ]],
            'levels' => $levels,
        ];
    }

    public function assertTeacherCanAccessSection(User $user, int $sectionId): void
    {
        if (! $this->isDocentePortalScoped($user)) {
            return;
        }

        if (! in_array($sectionId, $this->activeSectionIdsFor($user), true)) {
            abort(403, 'No tiene asignada esta sección en el año académico activo.');
        }
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

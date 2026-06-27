<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Section;
use App\Models\Student;
use App\Support\EncryptedPersonalDataSearch;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    /**
     * Estados que bloquean una segunda matrícula en el mismo año académico.
     *
     * @return list<string>
     */
    public static function blockingStatuses(): array
    {
        return [
            EnrollmentStatus::Pendiente->value,
            EnrollmentStatus::Matriculado->value,
        ];
    }

    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Enrollment::query()
            ->with([
                'student:id,code,first_name,last_name',
                'guardian:id,first_name,last_name',
                'academicYear:id,name,year',
                'educationalLevel:id,code,name',
                'grade:id,code,name',
                'section:id,code,name',
                'classroom:id,code,name',
            ])
            ->orderByDesc('enrollment_date')
            ->orderByDesc('id');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('enrollment_code', 'like', $like)
                    ->orWhereHas('student', function ($s) use ($like): void {
                        $s->where('first_name', 'like', $like)
                            ->orWhere('last_name', 'like', $like)
                            ->orWhere('code', 'like', $like);
                    });
            });
        }

        if ($request->query('academic_year_id')) {
            $query->where('academic_year_id', $request->query('academic_year_id'));
        }

        if ($request->query('educational_level_id')) {
            $query->where('educational_level_id', $request->query('educational_level_id'));
        }

        if ($request->query('grade_id')) {
            $query->where('grade_id', $request->query('grade_id'));
        }

        if ($request->query('status')) {
            $query->where('status', $request->query('status'));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Enrollment
    {
        $data = $this->normalizePayload($data);

        if (empty($data['enrollment_code'])) {
            $year = AcademicYear::query()->findOrFail($data['academic_year_id']);
            $data['enrollment_code'] = $this->generateUniqueEnrollmentCode($year);
        }

        $this->validateBusinessRules($data);

        return Enrollment::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Enrollment $enrollment, array $data): Enrollment
    {
        $data = $this->normalizePayload($data);

        $full = array_merge([
            'student_id' => $enrollment->student_id,
            'guardian_id' => $enrollment->guardian_id,
            'academic_year_id' => $enrollment->academic_year_id,
            'educational_level_id' => $enrollment->educational_level_id,
            'grade_id' => $enrollment->grade_id,
            'section_id' => $enrollment->section_id,
            'classroom_id' => $enrollment->classroom_id,
            'enrollment_date' => $enrollment->enrollment_date->format('Y-m-d'),
            'amount' => $enrollment->amount,
            'status' => $enrollment->status->value,
            'observations' => $enrollment->observations,
            'enrollment_code' => $enrollment->enrollment_code,
        ], $data);

        $this->validateBusinessRules($full, $enrollment->id);

        $enrollment->update($data);

        return $enrollment->fresh();
    }

    /**
     * @return array<string, int>
     */
    public function indexStats(): array
    {
        return [
            'enrollments_total' => Enrollment::query()->count(),
            'enrollments_pending' => Enrollment::query()->where('status', EnrollmentStatus::Pendiente->value)->count(),
            'enrollments_active_year' => AcademicYear::query()->where('is_active', true)->count(),
        ];
    }

    /**
     * Resultados de búsqueda para el formulario de matrícula (mínimo 2 caracteres en el controlador).
     *
     * @return list<array{id: int, code: string, first_name: string, last_name: string, document_number: string|null}>
     */
    public function searchStudentsForEnrollment(string $query): array
    {
        $term = trim($query);
        if (mb_strlen($term) < 2) {
            return [];
        }

        return Student::query()
            ->where(function ($q) use ($term): void {
                EncryptedPersonalDataSearch::applyDocumentOrTextSearch(
                    $q,
                    $term,
                    ['code', 'first_name', 'last_name'],
                );
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(20)
            ->get(['id', 'code', 'first_name', 'last_name', 'document_number'])
            ->map(fn (Student $s): array => [
                'id' => $s->id,
                'code' => $s->code,
                'first_name' => $s->first_name,
                'last_name' => $s->last_name,
                'document_number' => $s->document_number,
            ])
            ->values()
            ->all();
    }

    /**
     * Vista previa para tarjeta de estudiante y opciones de apoderado en matrícula.
     *
     * @return array{id: int, code: string, first_name: string, last_name: string, document_number: string|null, document_type: string|null, guardians: list<array{value: string, label: string}>}
     */
    public function studentPreviewForEnrollment(Student $student): array
    {
        $student->load(['guardians:id,first_name,last_name']);

        return [
            'id' => $student->id,
            'code' => $student->code,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'document_number' => $student->document_number,
            'document_type' => $student->document_type->value,
            'guardians' => $student->guardians->map(fn ($g): array => [
                'value' => (string) $g->id,
                'label' => $g->fullName(),
            ])->values()->all(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePayload(array $data): array
    {
        if (array_key_exists('guardian_id', $data) && ($data['guardian_id'] === '' || $data['guardian_id'] === null)) {
            $data['guardian_id'] = null;
        }

        if (array_key_exists('classroom_id', $data) && ($data['classroom_id'] === '' || $data['classroom_id'] === null)) {
            $data['classroom_id'] = null;
        }

        if (array_key_exists('observations', $data) && $data['observations'] === '') {
            $data['observations'] = null;
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function validateBusinessRules(array $data, ?int $ignoreEnrollmentId = null): void
    {
        $studentId = (int) $data['student_id'];
        $academicYearId = (int) $data['academic_year_id'];
        $status = EnrollmentStatus::from((string) $data['status']);

        if ($status->blocksConcurrentEnrollment()) {
            $exists = Enrollment::query()
                ->where('student_id', $studentId)
                ->where('academic_year_id', $academicYearId)
                ->whereIn('status', self::blockingStatuses())
                ->when($ignoreEnrollmentId, fn ($q, $id) => $q->whereKeyNot($id))
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'student_id' => ['El estudiante ya tiene una matrícula activa (pendiente o matriculada) en este año académico.'],
                ]);
            }
        }

        $guardianId = $data['guardian_id'] ?? null;
        if ($guardianId !== null) {
            $linked = Student::query()
                ->whereKey($studentId)
                ->whereHas('guardians', fn ($q) => $q->where('guardians.id', $guardianId))
                ->exists();

            if (! $linked) {
                throw ValidationException::withMessages([
                    'guardian_id' => ['El apoderado debe estar vinculado al estudiante.'],
                ]);
            }
        }

        $levelId = (int) $data['educational_level_id'];
        $gradeId = (int) $data['grade_id'];
        $sectionId = (int) $data['section_id'];

        $grade = Grade::query()->findOrFail($gradeId);
        if ((int) $grade->educational_level_id !== $levelId) {
            throw ValidationException::withMessages([
                'grade_id' => ['El grado no pertenece al nivel seleccionado.'],
            ]);
        }

        $section = Section::query()->findOrFail($sectionId);
        if ((int) $section->grade_id !== $gradeId) {
            throw ValidationException::withMessages([
                'section_id' => ['La sección no pertenece al grado seleccionado.'],
            ]);
        }

        $classroomId = $data['classroom_id'] ?? null;
        if ($classroomId !== null) {
            $room = Classroom::query()->findOrFail((int) $classroomId);
            if ($room->section_id !== null && (int) $room->section_id !== $sectionId) {
                throw ValidationException::withMessages([
                    'classroom_id' => ['El aula no corresponde a la sección seleccionada.'],
                ]);
            }
        }
    }

    private function generateUniqueEnrollmentCode(AcademicYear $year): string
    {
        do {
            $code = 'MAT-'.$year->year.'-'.strtoupper(Str::random(6));
        } while (Enrollment::query()->where('enrollment_code', $code)->exists());

        return $code;
    }
}

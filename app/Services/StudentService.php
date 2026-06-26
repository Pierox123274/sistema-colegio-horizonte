<?php

namespace App\Services;

use App\Enums\EnrollmentStatus;
use App\Models\AcademicYear;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class StudentService
{
    private const EMPTY_RESULT_SQL = '1 = 0';

    /**
     * @param  list<int>|null  $sectionIdsForActiveYear  Matrícula vigente en año activo y secciones dadas; null = sin filtro por sección.
     */
    public function paginateForIndex(Request $request, int $perPage = 15, ?array $sectionIdsForActiveYear = null): LengthAwarePaginator
    {
        $query = Student::query()
            ->orderBy('last_name')
            ->orderBy('first_name');

        $this->applyActiveYearSectionFilter($query, $sectionIdsForActiveYear);
        $this->applySearchFilter($query, $request);
        $this->applyLevelAndStatusFilters($query, $request);
        $this->applySectionIdFilter($query, $request, $sectionIdsForActiveYear);

        $query->with([
            'guardians' => function ($q): void {
                $q->wherePivot('is_primary', true);
            },
        ]);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  list<int>|null  $sectionIdsForActiveYear
     */
    private function applyActiveYearSectionFilter($query, ?array $sectionIdsForActiveYear): void
    {
        if ($sectionIdsForActiveYear === null) {
            return;
        }

        $activeYear = AcademicYear::query()->where('is_active', true)->first();
        if ($activeYear === null || $sectionIdsForActiveYear === []) {
            $query->whereRaw(self::EMPTY_RESULT_SQL);

            return;
        }

        $query->whereHas('enrollments', function ($q) use ($activeYear, $sectionIdsForActiveYear): void {
            $q->where('academic_year_id', $activeYear->id)
                ->where('status', EnrollmentStatus::Matriculado->value)
                ->whereIn('section_id', $sectionIdsForActiveYear);
        });
    }

    private function applySearchFilter($query, Request $request): void
    {
        $search = trim((string) $request->query('search', ''));
        if ($search === '') {
            return;
        }

        $query->where(function ($q) use ($search): void {
            $like = '%'.$search.'%';
            $q->where('first_name', 'like', $like)
                ->orWhere('last_name', 'like', $like)
                ->orWhere('code', 'like', $like)
                ->orWhere('document_number', 'like', $like);
        });
    }

    private function applyLevelAndStatusFilters($query, Request $request): void
    {
        if ($level = $request->query('educational_level')) {
            $query->where('educational_level', $level);
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }
    }

    /**
     * @param  list<int>|null  $sectionIdsForActiveYear
     */
    private function applySectionIdFilter($query, Request $request, ?array $sectionIdsForActiveYear): void
    {
        if (! $request->filled('section_id') || $sectionIdsForActiveYear === null) {
            return;
        }

        $sectionId = (int) $request->query('section_id');
        if ($sectionIdsForActiveYear === [] || ! in_array($sectionId, $sectionIdsForActiveYear, true)) {
            $query->whereRaw(self::EMPTY_RESULT_SQL);

            return;
        }

        $activeYear = AcademicYear::query()->where('is_active', true)->first();
        if ($activeYear === null) {
            return;
        }

        $query->whereHas('enrollments', function ($q) use ($activeYear, $sectionId): void {
            $q->where('academic_year_id', $activeYear->id)
                ->where('status', EnrollmentStatus::Matriculado->value)
                ->where('section_id', $sectionId);
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createStudent(array $data): Student
    {
        return Student::query()->create($this->normalizePayload($data));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateStudent(Student $student, array $data): Student
    {
        $student->update($this->normalizePayload($data));

        return $student->fresh();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePayload(array $data): array
    {
        foreach (['document_number', 'email', 'phone', 'address', 'section', 'medical_observations'] as $key) {
            if (array_key_exists($key, $data) && $data[$key] === '') {
                $data[$key] = null;
            }
        }

        return $data;
    }
}

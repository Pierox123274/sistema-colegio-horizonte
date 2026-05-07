<?php

namespace App\Services;

use App\Models\Guardian;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuardianService
{
    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Guardian::query()
            ->withCount('students')
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('first_name', 'like', $like)
                    ->orWhere('last_name', 'like', $like)
                    ->orWhere('document_number', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('secondary_phone', 'like', $like);
            });
        }

        if ($relationship = $request->query('relationship_type')) {
            $query->where('relationship_type', $relationship);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createGuardian(array $data): Guardian
    {
        $links = $data['students'] ?? [];
        unset($data['students']);

        $guardian = Guardian::query()->create($this->normalizeGuardianPayload($data));
        $this->syncStudentLinks($guardian, is_array($links) ? $links : []);

        return $guardian->fresh(['students']);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateGuardian(Guardian $guardian, array $data): Guardian
    {
        $links = $data['students'] ?? [];
        unset($data['students']);

        $guardian->update($this->normalizeGuardianPayload($data));
        $this->syncStudentLinks($guardian, is_array($links) ? $links : []);

        return $guardian->fresh(['students']);
    }

    /**
     * @param  array<int, array<string, mixed>>  $studentLinks
     */
    public function syncStudentLinks(Guardian $guardian, array $studentLinks): void
    {
        DB::transaction(function () use ($guardian, $studentLinks): void {
            $syncPayload = [];
            foreach ($studentLinks as $link) {
                $studentId = (int) $link['student_id'];
                $syncPayload[$studentId] = [
                    'relationship' => $link['relationship'],
                    'is_primary' => ! empty($link['is_primary']),
                    'is_financial_responsible' => ! empty($link['is_financial_responsible']),
                    'emergency_priority' => $this->nullableEmergencyPriority($link['emergency_priority'] ?? null),
                    'observations' => $this->nullableString($link['observations'] ?? null),
                ];
            }
            $guardian->students()->sync($syncPayload);
            $this->enforceExclusivePivotFlags($guardian);
        });
    }

    /**
     * Un responsable económico y un contacto principal por estudiante en todo el sistema.
     */
    private function enforceExclusivePivotFlags(Guardian $guardian): void
    {
        $rows = DB::table('guardian_student')
            ->where('guardian_id', $guardian->id)
            ->get();

        foreach ($rows as $row) {
            if ($row->is_financial_responsible) {
                DB::table('guardian_student')
                    ->where('student_id', $row->student_id)
                    ->where('guardian_id', '!=', $guardian->id)
                    ->update(['is_financial_responsible' => false]);
            }
            if ($row->is_primary) {
                DB::table('guardian_student')
                    ->where('student_id', $row->student_id)
                    ->where('guardian_id', '!=', $guardian->id)
                    ->update(['is_primary' => false]);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizeGuardianPayload(array $data): array
    {
        foreach (['document_number', 'secondary_phone', 'email', 'occupation', 'address', 'workplace'] as $key) {
            if (array_key_exists($key, $data) && $data[$key] === '') {
                $data[$key] = null;
            }
        }

        return $data;
    }

    private function nullableEmergencyPriority(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return is_string($value) ? $value : null;
    }
}

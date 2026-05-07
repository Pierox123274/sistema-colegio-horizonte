<?php

namespace App\Services;

use App\Models\AcademicYear;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class AcademicYearService
{
    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        return AcademicYear::query()
            ->orderByDesc('year')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): AcademicYear
    {
        $year = AcademicYear::query()->create($data);
        $this->syncSingleActive($year);

        return $year->fresh();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(AcademicYear $academicYear, array $data): AcademicYear
    {
        $academicYear->update($data);
        $this->syncSingleActive($academicYear->fresh());

        return $academicYear->fresh();
    }

    private function syncSingleActive(AcademicYear $year): void
    {
        if (! $year->is_active) {
            return;
        }

        AcademicYear::query()
            ->whereKeyNot($year->id)
            ->update(['is_active' => false]);
    }
}

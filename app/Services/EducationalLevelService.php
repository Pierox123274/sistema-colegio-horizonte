<?php

namespace App\Services;

use App\Models\EducationalLevel;
use App\Models\Grade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class EducationalLevelService
{
    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = EducationalLevel::query()
            ->withCount('grades')
            ->orderBy('code');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('code', 'like', $like)
                    ->orWhere('name', 'like', $like);
            });
        }

        if ($request->query('is_active') !== null && $request->query('is_active') !== '') {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): EducationalLevel
    {
        return EducationalLevel::query()->create($this->normalize($data));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(EducationalLevel $educationalLevel, array $data): EducationalLevel
    {
        $educationalLevel->update($this->normalize($data));

        return $educationalLevel->fresh();
    }

    public function delete(EducationalLevel $educationalLevel): void
    {
        $educationalLevel->delete();
    }

    /**
     * @return array<string, int>
     */
    public function indexStats(): array
    {
        return [
            'levels_total' => EducationalLevel::query()->count(),
            'levels_active' => EducationalLevel::query()->where('is_active', true)->count(),
            'grades_total' => Grade::query()->count(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data): array
    {
        if (array_key_exists('description', $data) && $data['description'] === '') {
            $data['description'] = null;
        }

        return $data;
    }
}

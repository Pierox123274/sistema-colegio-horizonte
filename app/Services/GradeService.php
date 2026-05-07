<?php

namespace App\Services;

use App\Models\EducationalLevel;
use App\Models\Grade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class GradeService
{
    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Grade::query()
            ->with('educationalLevel:id,code,name')
            ->withCount('sections')
            ->orderBy('educational_level_id')
            ->orderBy('order');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('code', 'like', $like)
                    ->orWhere('name', 'like', $like);
            });
        }

        if ($levelId = $request->query('educational_level_id')) {
            $query->where('educational_level_id', $levelId);
        }

        if ($request->query('is_active') !== null && $request->query('is_active') !== '') {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Grade
    {
        return Grade::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Grade $grade, array $data): Grade
    {
        $grade->update($data);

        return $grade->fresh();
    }

    public function delete(Grade $grade): void
    {
        $grade->delete();
    }

    /**
     * @return array<string, int>
     */
    public function indexStats(): array
    {
        return [
            'grades_total' => Grade::query()->count(),
            'grades_active' => Grade::query()->where('is_active', true)->count(),
            'levels_total' => EducationalLevel::query()->count(),
        ];
    }
}

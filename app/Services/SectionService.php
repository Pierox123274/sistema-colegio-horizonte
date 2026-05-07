<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class SectionService
{
    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Section::query()
            ->with(['grade.educationalLevel:id,code,name'])
            ->withCount('classrooms')
            ->orderBy('grade_id')
            ->orderBy('code');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('code', 'like', $like)
                    ->orWhere('name', 'like', $like);
            });
        }

        if ($gradeId = $request->query('grade_id')) {
            $query->where('grade_id', $gradeId);
        }

        if ($request->query('is_active') !== null && $request->query('is_active') !== '') {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Section
    {
        return Section::query()->create($data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Section $section, array $data): Section
    {
        $section->update($data);

        return $section->fresh();
    }

    public function delete(Section $section): void
    {
        $section->delete();
    }

    /**
     * @return array<string, int>
     */
    public function indexStats(): array
    {
        return [
            'sections_total' => Section::query()->count(),
            'sections_active' => Section::query()->where('is_active', true)->count(),
            'grades_total' => Grade::query()->count(),
        ];
    }
}

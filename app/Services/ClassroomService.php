<?php

namespace App\Services;

use App\Models\Classroom;
use App\Models\Section;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ClassroomService
{
    public function paginateForIndex(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Classroom::query()
            ->with(['section.grade.educationalLevel:id,code,name'])
            ->orderBy('code');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('code', 'like', $like)
                    ->orWhere('name', 'like', $like)
                    ->orWhere('floor', 'like', $like);
            });
        }

        if (($sid = $request->query('section_id')) !== null && $sid !== '') {
            $query->where('section_id', $sid);
        }

        if ($request->query('is_active') !== null && $request->query('is_active') !== '') {
            $query->where('is_active', filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Classroom
    {
        return Classroom::query()->create($this->normalize($data));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Classroom $classroom, array $data): Classroom
    {
        $classroom->update($this->normalize($data));

        return $classroom->fresh();
    }

    public function delete(Classroom $classroom): void
    {
        $classroom->delete();
    }

    /**
     * @return array<string, int>
     */
    public function indexStats(): array
    {
        return [
            'classrooms_total' => Classroom::query()->count(),
            'classrooms_active' => Classroom::query()->where('is_active', true)->count(),
            'sections_total' => Section::query()->count(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalize(array $data): array
    {
        foreach (['section_id', 'floor', 'description'] as $key) {
            if (array_key_exists($key, $data) && $data[$key] === '') {
                $data[$key] = null;
            }
        }

        return $data;
    }
}

<?php

namespace App\Services;

use App\Models\Subject;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class SubjectService
{
    public function paginate(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Subject::query()->orderBy('name');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('code', 'like', $like)
                    ->orWhere('name', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return $query->paginate($perPage)->withQueryString();
    }
}

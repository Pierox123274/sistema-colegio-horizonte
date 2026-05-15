<?php

namespace App\Services;

use App\Models\Evaluation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class EvaluationService
{
    public function paginate(Request $request, int $perPage = 15): LengthAwarePaginator
    {
        $query = Evaluation::query()
            ->with([
                'subject:id,name,code',
                'academicYear:id,name,year',
                'section:id,name',
                'createdBy:id,name',
            ])
            ->orderByDesc('evaluated_at')
            ->orderByDesc('id');

        if ($request->filled('subject_id')) {
            $query->where('subject_id', (int) $request->query('subject_id'));
        }
        if ($request->filled('section_id')) {
            $query->where('section_id', (int) $request->query('section_id'));
        }
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', (int) $request->query('academic_year_id'));
        }
        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('title', 'like', $like)
                    ->orWhere('period', 'like', $like)
                    ->orWhereHas('subject', fn ($s) => $s
                        ->where('name', 'like', $like)
                        ->orWhere('code', 'like', $like));
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}

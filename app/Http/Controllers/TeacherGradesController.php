<?php

namespace App\Http\Controllers;

use App\Enums\IntranetRole;
use App\Models\GradeRecord;
use App\Services\TeacherContextService;
use Inertia\Inertia;
use Inertia\Response;

class TeacherGradesController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', GradeRecord::class);

        $user = auth()->user();
        $sectionScope = null;
        if ($user !== null
            && $user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasAnyRole([
                IntranetRole::Administrador->value,
                IntranetRole::Secretaria->value,
            ])
        ) {
            $sectionScope = app(TeacherContextService::class)->activeSectionIdsFor($user);
        }

        $recordsQuery = GradeRecord::query()
            ->with([
                'student:id,code,first_name,last_name',
                'evaluation:id,title,period,subject_id,section_id',
                'evaluation.subject:id,name',
                'evaluation.section:id,name',
            ])
            ->orderByDesc('id');

        if ($sectionScope !== null && $sectionScope !== []) {
            $recordsQuery->whereHas('evaluation', fn ($q) => $q->whereIn('section_id', $sectionScope));
        } elseif ($sectionScope !== null && $sectionScope === []) {
            $recordsQuery->whereRaw('1 = 0');
        }

        $firstSectionId = ($sectionScope !== null && $sectionScope !== []) ? $sectionScope[0] : null;

        $links = [
            'records' => route('intranet.academic.grades.records.index', absolute: false),
            'history' => route('intranet.academic.grades.history.index', absolute: false),
            'reports' => route('intranet.academic.grades.reports.index', absolute: false),
        ];

        if ($firstSectionId !== null) {
            $links['records'] = route('intranet.academic.grades.records.index', [
                'section_id' => $firstSectionId,
            ], false);
            $links['reports'] = route('intranet.academic.grades.reports.index', [
                'section_id' => $firstSectionId,
            ], false);
        }

        return Inertia::render('Teacher/Grades/Index', [
            'recent_records' => $recordsQuery->limit(25)->get(),
            'links' => $links,
            'has_teaching_assignments' => $sectionScope === null || $sectionScope !== [],
            'teacher_portal_scoped' => $sectionScope !== null,
        ]);
    }
}

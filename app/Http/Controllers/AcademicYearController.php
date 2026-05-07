<?php

namespace App\Http\Controllers;

use App\Http\Requests\Intranet\StoreAcademicYearRequest;
use App\Http\Requests\Intranet\UpdateAcademicYearRequest;
use App\Models\AcademicYear;
use App\Services\AcademicYearService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AcademicYearController extends Controller
{
    public function __construct(
        private readonly AcademicYearService $academicYearService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', AcademicYear::class);

        return Inertia::render('Intranet/AcademicYears/Index', [
            'years' => $this->academicYearService->paginateForIndex($request),
            'permissions' => [
                'manage' => $request->user()?->can('create', AcademicYear::class) ?? false,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', AcademicYear::class);

        return Inertia::render('Intranet/AcademicYears/Create');
    }

    public function store(StoreAcademicYearRequest $request): RedirectResponse
    {
        $this->academicYearService->create($request->validated());

        return redirect()
            ->route('intranet.academic-years.index')
            ->with('success', 'Año académico registrado.');
    }

    public function edit(Request $request, AcademicYear $academicYear): Response
    {
        $this->authorize('update', $academicYear);

        return Inertia::render('Intranet/AcademicYears/Edit', [
            'year' => $academicYear,
        ]);
    }

    public function update(UpdateAcademicYearRequest $request, AcademicYear $academicYear): RedirectResponse
    {
        $this->academicYearService->update($academicYear, $request->validated());

        return redirect()
            ->route('intranet.academic-years.index')
            ->with('success', 'Año académico actualizado.');
    }
}

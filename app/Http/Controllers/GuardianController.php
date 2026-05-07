<?php

namespace App\Http\Controllers;

use App\Enums\DocumentType;
use App\Enums\GuardianRelationshipType;
use App\Http\Requests\Intranet\StoreGuardianRequest;
use App\Http\Requests\Intranet\UpdateGuardianRequest;
use App\Models\Guardian;
use App\Models\Student;
use App\Services\GuardianService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GuardianController extends Controller
{
    public function __construct(
        private readonly GuardianService $guardianService
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Guardian::class);

        return Inertia::render('Intranet/Guardians/Index', [
            'guardians' => $this->guardianService->paginateForIndex($request),
            'filters' => [
                'search' => $request->query('search', ''),
                'relationship_type' => $request->query('relationship_type', ''),
            ],
            'catalog' => [
                'relationship_types' => GuardianRelationshipType::options(),
            ],
            'permissions' => [
                'manage' => $request->user()?->can('create', Guardian::class) ?? false,
            ],
        ]);
    }

    public function create(Request $request): Response
    {
        $this->authorize('create', Guardian::class);

        return Inertia::render('Intranet/Guardians/Create', [
            'catalog' => $this->formCatalog(),
            'student_options' => $this->studentSelectOptions(),
        ]);
    }

    public function store(StoreGuardianRequest $request)
    {
        $guardian = $this->guardianService->createGuardian($request->validated());

        return redirect()
            ->route('intranet.guardians.show', $guardian)
            ->with('success', 'Apoderado registrado correctamente.');
    }

    public function show(Request $request, Guardian $guardian): Response
    {
        $this->authorize('view', $guardian);

        $guardian->load(['students' => fn ($q) => $q->orderBy('students.last_name')->orderBy('students.first_name')]);

        return Inertia::render('Intranet/Guardians/Show', [
            'guardian' => $guardian,
            'student_links' => $this->formatStudentLinks($guardian),
            'permissions' => [
                'manage' => $request->user()?->can('update', $guardian) ?? false,
            ],
        ]);
    }

    public function edit(Request $request, Guardian $guardian): Response
    {
        $this->authorize('update', $guardian);

        $guardian->load(['students' => fn ($q) => $q->orderBy('students.last_name')->orderBy('students.first_name')]);

        return Inertia::render('Intranet/Guardians/Edit', [
            'guardian' => $guardian,
            'student_links' => $this->formatStudentLinks($guardian),
            'catalog' => $this->formCatalog(),
            'student_options' => $this->studentSelectOptions(),
        ]);
    }

    public function update(UpdateGuardianRequest $request, Guardian $guardian)
    {
        $this->guardianService->updateGuardian($guardian, $request->validated());

        return redirect()
            ->route('intranet.guardians.show', $guardian)
            ->with('success', 'Apoderado actualizado correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formCatalog(): array
    {
        return [
            'document_types' => DocumentType::options(),
            'relationship_types' => GuardianRelationshipType::options(),
        ];
    }

    /**
     * @return list<array{value: int, label: string}>
     */
    private function studentSelectOptions(): array
    {
        return Student::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->limit(500)
            ->get(['id', 'code', 'first_name', 'last_name'])
            ->map(fn (Student $s) => [
                'value' => $s->id,
                'label' => "{$s->code} — {$s->first_name} {$s->last_name}",
            ])
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function formatStudentLinks(Guardian $guardian): array
    {
        return $guardian->students->map(function (Student $student): array {
            $pivot = $student->pivot;

            return [
                'student_id' => $student->id,
                'student_code' => $student->code,
                'student_name' => "{$student->first_name} {$student->last_name}",
                'relationship' => $pivot->relationship,
                'is_primary' => (bool) $pivot->is_primary,
                'is_financial_responsible' => (bool) $pivot->is_financial_responsible,
                'emergency_priority' => $pivot->emergency_priority !== null ? (int) $pivot->emergency_priority : null,
                'observations' => $pivot->observations,
            ];
        })->values()->all();
    }
}

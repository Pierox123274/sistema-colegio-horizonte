<?php

namespace App\Http\Controllers;

use App\Enums\AuditAction;
use App\Enums\AuditModule;
use App\Enums\AuditSeverity;
use App\Enums\IntranetRole;
use App\Http\Requests\Intranet\StoreIntranetUserRequest;
use App\Http\Requests\Intranet\UpdateIntranetUserRequest;
use App\Jobs\WelcomeInstitutionUserJob;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    public function __construct(
        private readonly AuditService $audit,
    ) {}

    public function index(Request $request): Response
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()->with('roles')->orderBy('name');

        if ($search = trim((string) $request->query('search', ''))) {
            $like = '%'.$search.'%';
            $query->where(function ($q) use ($like): void {
                $q->where('name', 'like', $like)->orWhere('email', 'like', $like);
            });
        }

        if ($role = $request->query('role')) {
            $query->whereHas('roles', function ($q) use ($role): void {
                $q->where('name', $role);
            });
        }

        return Inertia::render('Intranet/Admin/Users/Index', [
            'users' => $query->paginate(15)->withQueryString(),
            'filters' => [
                'search' => $request->query('search', ''),
                'role' => $request->query('role', ''),
            ],
            'catalog' => [
                'roles' => collect(IntranetRole::cases())->map(fn (IntranetRole $r): array => [
                    'value' => $r->value,
                    'label' => $r->value,
                ])->values()->all(),
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', User::class);

        return Inertia::render('Intranet/Admin/Users/Create', [
            'catalog' => [
                'roles' => collect(IntranetRole::cases())->map(fn (IntranetRole $r): array => [
                    'value' => $r->value,
                    'label' => $r->value,
                ])->values()->all(),
            ],
        ]);
    }

    public function store(StoreIntranetUserRequest $request)
    {
        $data = $request->validated();
        $role = $data['role'];
        unset($data['role'], $data['password_confirmation']);

        $user = User::query()->create($data);
        $user->syncRoles([$role]);

        $this->audit->log(
            AuditAction::Create,
            AuditModule::Users,
            $request->user(),
            User::class,
            $user->id,
            description: 'Usuario creado',
            newValues: ['email' => $user->email, 'role' => $role],
            severity: AuditSeverity::Info,
            request: $request,
        );

        if (config('devops.send_welcome_email', false)) {
            dispatch(new WelcomeInstitutionUserJob($user->id))->afterCommit();
        }

        return redirect()
            ->route('intranet.admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $user): Response
    {
        $this->authorize('update', $user);

        $user->load('roles');

        return Inertia::render('Intranet/Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => (bool) $user->is_active,
                'role' => $user->roles->first()?->name ?? IntranetRole::Estudiante->value,
            ],
            'catalog' => [
                'roles' => collect(IntranetRole::cases())->map(fn (IntranetRole $r): array => [
                    'value' => $r->value,
                    'label' => $r->value,
                ])->values()->all(),
            ],
        ]);
    }

    public function update(UpdateIntranetUserRequest $request, User $user)
    {
        $data = $request->validated();
        $role = $data['role'];
        unset($data['role'], $data['password_confirmation']);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->load('roles');
        $previousRole = $user->roles->first()?->name;

        $user->update($data);
        $user->syncRoles([$role]);

        $this->audit->log(
            AuditAction::Update,
            AuditModule::Users,
            $request->user(),
            User::class,
            $user->id,
            description: 'Usuario actualizado',
            oldValues: ['role' => $previousRole],
            newValues: ['role' => $role],
            severity: AuditSeverity::Info,
            request: $request,
        );

        if ($previousRole !== $role) {
            $this->audit->log(
                AuditAction::RoleChange,
                AuditModule::Users,
                $request->user(),
                User::class,
                $user->id,
                description: 'Cambio de rol de usuario',
                oldValues: ['role' => $previousRole],
                newValues: ['role' => $role],
                severity: AuditSeverity::Warning,
                request: $request,
            );
        }

        return redirect()
            ->route('intranet.admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }
}

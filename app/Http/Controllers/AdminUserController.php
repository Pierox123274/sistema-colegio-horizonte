<?php

namespace App\Http\Controllers;

use App\Enums\IntranetRole;
use App\Http\Requests\Intranet\StoreIntranetUserRequest;
use App\Http\Requests\Intranet\UpdateIntranetUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
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

        $user->update($data);
        $user->syncRoles([$role]);

        return redirect()
            ->route('intranet.admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }
}

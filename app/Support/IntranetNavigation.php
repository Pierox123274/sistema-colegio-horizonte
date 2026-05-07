<?php

namespace App\Support;

use App\Enums\IntranetRole;
use App\Models\User;

final class IntranetNavigation
{
    /**
     * Navegación lateral de la intranet.
     *
     * @return list<array{label: string, href: string|null, icon: string, disabled: bool, children?: list<array{label: string, href: string|null, icon: string, disabled: bool}>}>
     */
    public static function items(?User $user): array
    {
        if ($user === null) {
            return [];
        }

        $canManageStudents = $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);

        $canManageGuardians = $canManageStudents;

        $canViewAcademic = $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);

        $nav = [
            [
                'label' => 'Dashboard',
                'href' => route('dashboard', absolute: false),
                'icon' => 'layout-dashboard',
                'disabled' => false,
            ],
        ];

        if ($canViewAcademic) {
            $nav[] = [
                'label' => 'Gestión académica',
                'href' => null,
                'icon' => 'school',
                'disabled' => false,
                'children' => [
                    [
                        'label' => 'Niveles',
                        'href' => route('intranet.academic.levels.index', absolute: false),
                        'icon' => 'layers',
                        'disabled' => false,
                    ],
                    [
                        'label' => 'Grados',
                        'href' => route('intranet.academic.grades.index', absolute: false),
                        'icon' => 'book-marked',
                        'disabled' => false,
                    ],
                    [
                        'label' => 'Secciones',
                        'href' => route('intranet.academic.sections.index', absolute: false),
                        'icon' => 'layout-grid',
                        'disabled' => false,
                    ],
                    [
                        'label' => 'Aulas',
                        'href' => route('intranet.academic.classrooms.index', absolute: false),
                        'icon' => 'door-open',
                        'disabled' => false,
                    ],
                ],
            ];
        }

        $nav[] = [
            'label' => 'Estudiantes',
            'href' => $canManageStudents
                ? route('intranet.students.index', absolute: false)
                : null,
            'icon' => 'users',
            'disabled' => ! $canManageStudents,
        ];

        $nav[] = [
            'label' => 'Apoderados',
            'href' => $canManageGuardians
                ? route('intranet.guardians.index', absolute: false)
                : null,
            'icon' => 'user-circle',
            'disabled' => ! $canManageGuardians,
        ];

        return array_merge($nav, [
            [
                'label' => 'Matrículas',
                'href' => null,
                'icon' => 'clipboard-list',
                'disabled' => true,
            ],
            [
                'label' => 'Pensiones',
                'href' => null,
                'icon' => 'wallet',
                'disabled' => true,
            ],
            [
                'label' => 'Inventario',
                'href' => null,
                'icon' => 'package',
                'disabled' => true,
            ],
            [
                'label' => 'Ventas',
                'href' => null,
                'icon' => 'shopping-cart',
                'disabled' => true,
            ],
            [
                'label' => 'Reportes',
                'href' => null,
                'icon' => 'bar-chart-3',
                'disabled' => true,
            ],
            [
                'label' => 'Configuración',
                'href' => null,
                'icon' => 'settings',
                'disabled' => true,
            ],
            [
                'label' => 'Mi perfil',
                'href' => route('profile.edit', absolute: false),
                'icon' => 'user',
                'disabled' => false,
            ],
        ]);
    }
}

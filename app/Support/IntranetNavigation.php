<?php

namespace App\Support;

use App\Enums\IntranetRole;
use App\Models\User;

final class IntranetNavigation
{
    /**
     * Navegación lateral de la intranet.
     *
     * @return list<array{label: string, href: string|null, icon: string, disabled: bool, active_routes?: list<string>, activeRoutes?: list<string>, children?: list<array{label: string, href: string|null, icon: string, disabled: bool, active_routes?: list<string>, activeRoutes?: list<string>}>}>
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

        $canViewEnrollments = $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
            IntranetRole::Docente->value,
        ]);

        $canFinance = $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
        $canInventory = $user->hasAnyRole([
            IntranetRole::Administrador->value,
            IntranetRole::Secretaria->value,
        ]);
        $canSales = $canInventory;
        $canAttendance = $user->hasAnyRole([
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
                        'activeRoutes' => [
                            'intranet.academic.levels.index',
                            'intranet.academic.levels.show',
                            'intranet.academic.levels.create',
                            'intranet.academic.levels.edit',
                        ],
                    ],
                    [
                        'label' => 'Grados',
                        'href' => route('intranet.academic.grades.index', absolute: false),
                        'icon' => 'book-marked',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.academic.grades.index',
                            'intranet.academic.grades.show',
                            'intranet.academic.grades.create',
                            'intranet.academic.grades.edit',
                        ],
                    ],
                    [
                        'label' => 'Secciones',
                        'href' => route('intranet.academic.sections.index', absolute: false),
                        'icon' => 'layout-grid',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.academic.sections.index',
                            'intranet.academic.sections.show',
                            'intranet.academic.sections.create',
                            'intranet.academic.sections.edit',
                        ],
                    ],
                    [
                        'label' => 'Aulas',
                        'href' => route('intranet.academic.classrooms.index', absolute: false),
                        'icon' => 'door-open',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.academic.classrooms.index',
                            'intranet.academic.classrooms.show',
                            'intranet.academic.classrooms.create',
                            'intranet.academic.classrooms.edit',
                        ],
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

        $financeNav = [];
        if ($canFinance) {
            $financeNav[] = [
                'label' => 'Finanzas',
                'href' => null,
                'icon' => 'banknote',
                'disabled' => false,
                'children' => [
                    [
                        'label' => 'Conceptos de pago',
                        'href' => route('intranet.payment-concepts.index', absolute: false),
                        'icon' => 'package',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.payment-concepts.index',
                            'intranet.payment-concepts.show',
                            'intranet.payment-concepts.create',
                            'intranet.payment-concepts.edit',
                        ],
                    ],
                    [
                        'label' => 'Pensiones',
                        'href' => route('intranet.pensions.index', absolute: false),
                        'icon' => 'calendar-days',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.pensions.index',
                            'intranet.pensions.show',
                            'intranet.pensions.create',
                            'intranet.pensions.edit',
                        ],
                    ],
                    [
                        'label' => 'Pagos',
                        'href' => route('intranet.payments.index', absolute: false),
                        'icon' => 'receipt',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.payments.index',
                            'intranet.payments.show',
                            'intranet.payments.create',
                            'intranet.payments.receipt',
                            'intranet.payments.receipt.pdf',
                            'intranet.payments.receipt.ticket',
                        ],
                    ],
                ],
            ];
        }

        return array_merge($nav, [
            [
                'label' => 'Matrículas',
                'href' => $canViewEnrollments
                    ? route('intranet.enrollments.index', absolute: false)
                    : null,
                'icon' => 'clipboard-list',
                'disabled' => ! $canViewEnrollments,
            ],
            ...$financeNav,
            ...($canInventory ? [[
                'label' => 'Inventario',
                'href' => null,
                'icon' => 'package',
                'disabled' => false,
                'children' => [
                    [
                        'label' => 'Categorías',
                        'href' => route('intranet.inventory.categories.index', absolute: false),
                        'icon' => 'folder-tree',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.inventory.categories.index',
                            'intranet.inventory.categories.show',
                            'intranet.inventory.categories.create',
                            'intranet.inventory.categories.edit',
                        ],
                    ],
                    [
                        'label' => 'Productos',
                        'href' => route('intranet.inventory.products.index', absolute: false),
                        'icon' => 'package-search',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.inventory.products.index',
                            'intranet.inventory.products.show',
                            'intranet.inventory.products.create',
                            'intranet.inventory.products.edit',
                        ],
                    ],
                    [
                        'label' => 'Movimientos',
                        'href' => route('intranet.inventory.movements.index', absolute: false),
                        'icon' => 'arrow-left-right',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.inventory.movements.index',
                            'intranet.inventory.movements.show',
                            'intranet.inventory.movements.create',
                        ],
                    ],
                ],
            ]] : [[
                'label' => 'Inventario',
                'href' => null,
                'icon' => 'package',
                'disabled' => true,
            ]]),
            ...($canSales ? [[
                'label' => 'Caja y ventas',
                'href' => null,
                'icon' => 'shopping-cart',
                'disabled' => false,
                'children' => [
                    [
                        'label' => 'Caja diaria',
                        'href' => route('intranet.sales.cash-registers.index', absolute: false),
                        'icon' => 'wallet',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.sales.cash-registers.index'],
                    ],
                    [
                        'label' => 'Ventas',
                        'href' => route('intranet.sales.sales.index', absolute: false),
                        'icon' => 'shopping-cart',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.sales.sales.index',
                            'intranet.sales.sales.show',
                            'intranet.sales.sales.receipt',
                            'intranet.sales.sales.receipt.pdf',
                            'intranet.sales.sales.receipt.ticket',
                        ],
                    ],
                    [
                        'label' => 'Nueva venta',
                        'href' => route('intranet.sales.sales.create', absolute: false),
                        'icon' => 'receipt',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.sales.sales.create'],
                    ],
                    [
                        'label' => 'Movimientos',
                        'href' => route('intranet.sales.cash-movements.index', absolute: false),
                        'icon' => 'arrow-left-right',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.sales.cash-movements.index'],
                    ],
                ],
            ]] : [[
                'label' => 'Caja y ventas',
                'href' => null,
                'icon' => 'shopping-cart',
                'disabled' => true,
            ]]),
            ...($canAttendance ? [[
                'label' => 'Asistencia',
                'href' => null,
                'icon' => 'calendar-check',
                'disabled' => false,
                'active_routes' => ['intranet.attendance.*'],
                'children' => [
                    [
                        'label' => 'Registrar asistencia',
                        'href' => route('intranet.attendance.create', absolute: false),
                        'icon' => 'clipboard-check',
                        'disabled' => false,
                        'active_routes' => ['intranet.attendance.create'],
                    ],
                    [
                        'label' => 'Historial por estudiante',
                        'href' => route('intranet.attendance.index', absolute: false),
                        'icon' => 'user-check',
                        'disabled' => false,
                        'active_routes' => [
                            'intranet.attendance.index',
                            'intranet.attendance.students.show',
                        ],
                    ],
                    [
                        'label' => 'Reportes de asistencia',
                        'href' => route('intranet.attendance.reports.index', absolute: false),
                        'icon' => 'file-bar-chart',
                        'disabled' => false,
                        'active_routes' => [
                            'intranet.attendance.reports.index',
                        ],
                    ],
                ],
            ]] : [[
                'label' => 'Asistencia',
                'href' => null,
                'icon' => 'calendar-check',
                'disabled' => true,
            ]]),
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

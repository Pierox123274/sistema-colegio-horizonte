<?php

namespace App\Support;

use App\Enums\IntranetRole;
use App\Models\User;

final class IntranetNavigation
{
    /**
     * Destino del logotipo / “inicio” en el sidebar intranet: docente (sin admin) → portal docente.
     */
    public static function sidebarHomeHref(?User $user): string
    {
        if ($user !== null
            && $user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)
        ) {
            return route('teacher.dashboard', absolute: false);
        }

        return route('dashboard', absolute: false);
    }

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

        $docenteSinAdministrador = $user->hasRole(IntranetRole::Docente->value)
            && ! $user->hasRole(IntranetRole::Administrador->value);

        $dashboardItem = [
            'label' => $docenteSinAdministrador ? 'Inicio docente' : 'Dashboard',
            'href' => self::sidebarHomeHref($user),
            'icon' => 'layout-dashboard',
            'disabled' => false,
        ];

        if ($docenteSinAdministrador) {
            $dashboardItem['activeRoutes'] = [
                'teacher.dashboard',
                'teacher.attendance.index',
                'teacher.grades.index',
                'teacher.students.index',
                'teacher.reports.index',
            ];
        }

        $nav = [$dashboardItem];

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
                    [
                        'label' => 'Cursos',
                        'href' => route('intranet.academic.subjects.index', absolute: false),
                        'icon' => 'book-marked',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.academic.subjects.index',
                            'intranet.academic.subjects.show',
                            'intranet.academic.subjects.create',
                            'intranet.academic.subjects.edit',
                        ],
                    ],
                    [
                        'label' => 'Evaluaciones',
                        'href' => route('intranet.academic.evaluations.index', absolute: false),
                        'icon' => 'clipboard-check',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.academic.evaluations.index',
                            'intranet.academic.evaluations.show',
                            'intranet.academic.evaluations.create',
                            'intranet.academic.evaluations.edit',
                        ],
                    ],
                    [
                        'label' => 'Registro de notas',
                        'href' => route('intranet.academic.grades.records.index', absolute: false),
                        'icon' => 'receipt',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.academic.grades.records.index'],
                    ],
                    [
                        'label' => 'Historial académico',
                        'href' => route('intranet.academic.grades.history.index', absolute: false),
                        'icon' => 'user-check',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.academic.grades.history.index',
                            'intranet.academic.grades.students.show',
                        ],
                    ],
                    [
                        'label' => 'Reportes académicos',
                        'href' => route('intranet.academic.grades.reports.index', absolute: false),
                        'icon' => 'file-bar-chart',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.academic.grades.reports.index',
                            'intranet.academic.grades.reports.export.pdf',
                            'intranet.academic.grades.reports.export.excel',
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

        $canAdministration = $user->hasRole(IntranetRole::Administrador->value);

        if ($user->hasRole(IntranetRole::Administrador->value)) {
            $nav[] = [
                'label' => 'Comunicados',
                'href' => route('intranet.announcements.index', absolute: false),
                'icon' => 'megaphone',
                'disabled' => false,
                'activeRoutes' => [
                    'intranet.announcements.index',
                    'intranet.announcements.create',
                    'intranet.announcements.show',
                    'intranet.announcements.edit',
                ],
            ];
        }

        if ($user->hasRole(IntranetRole::Secretaria->value)
            && ! $user->hasRole(IntranetRole::Administrador->value)
        ) {
            $nav[] = [
                'label' => 'Comunicados',
                'href' => route('intranet.announcements.inbox.index', absolute: false),
                'icon' => 'megaphone',
                'disabled' => false,
                'activeRoutes' => [
                    'intranet.announcements.inbox.index',
                    'intranet.announcements.inbox.show',
                ],
            ];
        }

        if ($canAdministration) {
            $nav[] = [
                'label' => 'Administración',
                'href' => null,
                'icon' => 'shield',
                'disabled' => false,
                'children' => [
                    [
                        'label' => 'Usuarios',
                        'href' => route('intranet.admin.users.index', absolute: false),
                        'icon' => 'users',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.admin.users.index',
                            'intranet.admin.users.create',
                            'intranet.admin.users.edit',
                        ],
                    ],
                    [
                        'label' => 'Asignaciones docentes',
                        'href' => route('intranet.admin.teacher-assignments.index', absolute: false),
                        'icon' => 'user-cog',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.admin.teacher-assignments.index',
                            'intranet.admin.teacher-assignments.create',
                            'intranet.admin.teacher-assignments.edit',
                        ],
                    ],
                    [
                        'label' => 'Salud del sistema',
                        'href' => route('intranet.system.health.index', absolute: false),
                        'icon' => 'activity',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.system.health.index'],
                    ],
                    [
                        'label' => 'Colas y jobs',
                        'href' => route('intranet.system.jobs.index', absolute: false),
                        'icon' => 'list-todo',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.system.jobs.index'],
                    ],
                    [
                        'label' => 'Respaldos',
                        'href' => route('intranet.system.backups.index', absolute: false),
                        'icon' => 'archive',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.system.backups.index'],
                    ],
                    [
                        'label' => 'IA institucional',
                        'href' => route('intranet.ai-analytics.index', absolute: false),
                        'icon' => 'sparkles',
                        'disabled' => false,
                        'activeRoutes' => [
                            'intranet.ai-analytics.index',
                            'intranet.ai-analytics.refresh',
                        ],
                    ],
                    [
                        'label' => 'Aprendizaje adaptativo',
                        'href' => null,
                        'icon' => 'arrow-left-right',
                        'disabled' => false,
                        'children' => [
                            [
                                'label' => 'Analítica institucional',
                                'href' => route('intranet.adaptive-analytics.index', absolute: false),
                                'icon' => 'bar-chart-3',
                                'disabled' => false,
                                'activeRoutes' => ['intranet.adaptive-analytics.index'],
                            ],
                            [
                                'label' => 'Exámenes diagnóstico',
                                'href' => route('intranet.adaptive.diagnostic-exams.index', absolute: false),
                                'icon' => 'clipboard-list',
                                'disabled' => false,
                                'activeRoutes' => [
                                    'intranet.adaptive.diagnostic-exams.index',
                                    'intranet.adaptive.diagnostic-exams.create',
                                    'intranet.adaptive.diagnostic-exams.store',
                                    'intranet.adaptive.diagnostic-exams.show',
                                    'intranet.adaptive.diagnostic-exams.edit',
                                    'intranet.adaptive.diagnostic-exams.update',
                                ],
                            ],
                            [
                                'label' => 'Banco de preguntas',
                                'href' => route('intranet.adaptive.questions.index', absolute: false),
                                'icon' => 'clipboard-check',
                                'disabled' => false,
                                'activeRoutes' => ['intranet.adaptive.questions.index'],
                            ],
                            [
                                'label' => 'Resultados diagnóstico',
                                'href' => route('intranet.adaptive.results.index', absolute: false),
                                'icon' => 'file-bar-chart',
                                'disabled' => false,
                                'activeRoutes' => ['intranet.adaptive.results.index'],
                            ],
                        ],
                    ],
                ],
            ];
        }

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
            ...($user->hasAnyRole([
                IntranetRole::Administrador->value,
                IntranetRole::Secretaria->value,
                IntranetRole::Docente->value,
            ]) ? [[
                'label' => 'Seguridad y auditoría',
                'href' => null,
                'icon' => 'shield-check',
                'disabled' => false,
                'children' => array_values(array_filter([
                    [
                        'label' => 'Auditoría',
                        'href' => route('intranet.security.audit-logs.index', absolute: false),
                        'icon' => 'scroll-text',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.security.audit-logs.index'],
                    ],
                    $user->hasAnyRole([
                        IntranetRole::Administrador->value,
                        IntranetRole::Secretaria->value,
                    ]) ? [
                        'label' => 'Accesos recientes',
                        'href' => route('intranet.security.access-monitor.index', absolute: false),
                        'icon' => 'activity',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.security.access-monitor.index'],
                    ] : null,
                    $user->hasRole(IntranetRole::Administrador->value) ? [
                        'label' => 'Sesiones activas',
                        'href' => route('intranet.security.sessions.index', absolute: false),
                        'icon' => 'monitor-smartphone',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.security.sessions.index'],
                    ] : null,
                    $user->hasRole(IntranetRole::Administrador->value) ? [
                        'label' => 'Intentos fallidos',
                        'href' => route('intranet.security.login-attempts.index', absolute: false),
                        'icon' => 'shield-alert',
                        'disabled' => false,
                        'activeRoutes' => ['intranet.security.login-attempts.index'],
                    ] : null,
                ])),
            ]] : []),
            ...($user->hasAnyRole([
                IntranetRole::Administrador->value,
                IntranetRole::Secretaria->value,
            ]) ? [[
                'label' => 'Analítica',
                'href' => route('intranet.analytics.index', absolute: false),
                'icon' => 'bar-chart-3',
                'disabled' => false,
                'active_routes' => [
                    'intranet.analytics.index',
                    'intranet.reports.analytics.index',
                    'intranet.reports.analytics.show',
                ],
                'children' => [
                    [
                        'label' => 'Dashboard ejecutivo',
                        'href' => route('intranet.analytics.index', absolute: false),
                        'icon' => 'layout-dashboard',
                        'disabled' => false,
                        'active_routes' => ['intranet.analytics.index'],
                    ],
                    [
                        'label' => 'Reportes analíticos',
                        'href' => route('intranet.reports.analytics.index', absolute: false),
                        'icon' => 'file-bar-chart',
                        'disabled' => false,
                        'active_routes' => [
                            'intranet.reports.analytics.index',
                            'intranet.reports.analytics.show',
                        ],
                    ],
                ],
            ]] : [[
                'label' => 'Reportes',
                'href' => null,
                'icon' => 'bar-chart-3',
                'disabled' => true,
            ]]),
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

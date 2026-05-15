<?php

namespace App\Support;

use App\Enums\IntranetRole;
use App\Models\User;

final class TeacherNavigation
{
    /**
     * Menú lateral del portal docente (Docente y Administrador para supervisión; sin enlace al ERP).
     *
     * @return list<array{label: string, href: string|null, icon: string, disabled: bool, activeRoutes?: list<string>}>
     */
    public static function items(?User $user): array
    {
        if ($user === null || ! $user->hasAnyRole([
            IntranetRole::Docente->value,
            IntranetRole::Administrador->value,
        ])) {
            return [];
        }

        return [
            [
                'label' => 'Inicio',
                'href' => route('teacher.dashboard', absolute: false),
                'icon' => 'layout-dashboard',
                'disabled' => false,
                'activeRoutes' => ['teacher.dashboard'],
            ],
            [
                'label' => 'Asistencia',
                'href' => route('teacher.attendance.index', absolute: false),
                'icon' => 'calendar-check',
                'disabled' => false,
                'activeRoutes' => ['teacher.attendance.index'],
            ],
            [
                'label' => 'Notas',
                'href' => route('teacher.grades.index', absolute: false),
                'icon' => 'clipboard-check',
                'disabled' => false,
                'activeRoutes' => ['teacher.grades.index'],
            ],
            [
                'label' => 'Estudiantes',
                'href' => route('teacher.students.index', absolute: false),
                'icon' => 'users',
                'disabled' => false,
                'activeRoutes' => ['teacher.students.index'],
            ],
            [
                'label' => 'Reportes',
                'href' => route('teacher.reports.index', absolute: false),
                'icon' => 'file-bar-chart',
                'disabled' => false,
                'activeRoutes' => ['teacher.reports.index'],
            ],
        ];
    }
}

<?php

namespace App\Support;

use App\Enums\IntranetRole;
use App\Models\User;

final class StudentNavigation
{
    /**
     * Menú lateral del portal estudiante (Estudiante y Administrador para supervisión).
     *
     * @return list<array{label: string, href: string|null, icon: string, disabled: bool, activeRoutes?: list<string>}>
     */
    public static function items(?User $user): array
    {
        if ($user === null || ! $user->hasAnyRole([
            IntranetRole::Estudiante->value,
            IntranetRole::Administrador->value,
        ])) {
            return [];
        }

        return [
            [
                'label' => 'Inicio',
                'href' => route('student.dashboard', absolute: false),
                'icon' => 'layout-dashboard',
                'disabled' => false,
                'activeRoutes' => ['student.dashboard'],
            ],
            [
                'label' => 'Diagnóstico adaptativo',
                'href' => route('student.diagnostic.index', absolute: false),
                'icon' => 'clipboard-list',
                'disabled' => false,
                'activeRoutes' => [
                    'student.diagnostic.index',
                    'student.diagnostic.show',
                    'student.diagnostic.attempt',
                ],
            ],
            [
                'label' => 'Ruta de aprendizaje',
                'href' => route('student.learning-path.index', absolute: false),
                'icon' => 'arrow-left-right',
                'disabled' => false,
                'activeRoutes' => ['student.learning-path.index'],
            ],
            [
                'label' => 'Recomendaciones IA',
                'href' => route('student.recommendations.index', absolute: false),
                'icon' => 'sparkles',
                'disabled' => false,
                'activeRoutes' => ['student.recommendations.index'],
            ],
            [
                'label' => 'Tutor IA',
                'href' => route('student.ai-tutor.index', absolute: false),
                'icon' => 'bot',
                'disabled' => false,
                'activeRoutes' => [
                    'student.ai-tutor.index',
                    'student.ai-tutor.message',
                ],
            ],
            [
                'label' => 'Mis notas',
                'href' => route('student.grades.index', absolute: false),
                'icon' => 'clipboard-check',
                'disabled' => false,
                'activeRoutes' => ['student.grades.index'],
            ],
            [
                'label' => 'Mi asistencia',
                'href' => route('student.attendance.index', absolute: false),
                'icon' => 'calendar-check',
                'disabled' => false,
                'activeRoutes' => ['student.attendance.index'],
            ],
            [
                'label' => 'Mis pagos',
                'href' => route('student.payments.index', absolute: false),
                'icon' => 'wallet',
                'disabled' => false,
                'activeRoutes' => ['student.payments.index'],
            ],
            [
                'label' => 'Comunicados',
                'href' => route('student.announcements.index', absolute: false),
                'icon' => 'megaphone',
                'disabled' => false,
                'activeRoutes' => [
                    'student.announcements.index',
                    'student.announcements.show',
                ],
            ],
            [
                'label' => 'Mi perfil',
                'href' => route('student.profile.show', absolute: false),
                'icon' => 'user',
                'disabled' => false,
                'activeRoutes' => ['student.profile.show'],
            ],
        ];
    }
}

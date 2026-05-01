<?php

namespace App\Support;

use App\Models\User;

final class IntranetNavigation
{
    /**
     * Navegación lateral de la intranet (Fase 3: visual; módulos deshabilitados hasta implementación).
     *
     * @return list<array{label: string, href: string|null, icon: string, disabled: bool}>
     */
    public static function items(?User $user): array
    {
        if ($user === null) {
            return [];
        }

        return [
            [
                'label' => 'Dashboard',
                'href' => route('dashboard', absolute: false),
                'icon' => 'layout-dashboard',
                'disabled' => false,
            ],
            [
                'label' => 'Gestión académica',
                'href' => null,
                'icon' => 'school',
                'disabled' => true,
            ],
            [
                'label' => 'Estudiantes',
                'href' => null,
                'icon' => 'users',
                'disabled' => true,
            ],
            [
                'label' => 'Apoderados',
                'href' => null,
                'icon' => 'user-circle',
                'disabled' => true,
            ],
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
        ];
    }
}

<?php

namespace App\Support;

use App\Models\User;

final class IntranetNavigation
{
    /**
     * Items visible in the intranet sidebar. Filtered server-side (mínimo privilegio en UI).
     *
     * @return list<array{label: string, href: string, roles: null|list<string>}>
     */
    public static function items(?User $user): array
    {
        if ($user === null) {
            return [];
        }

        $items = [
            [
                'label' => 'Inicio',
                'href' => route('dashboard', absolute: false),
                'roles' => null,
            ],
            [
                'label' => 'Mi perfil',
                'href' => route('profile.edit', absolute: false),
                'roles' => null,
            ],
        ];

        return array_values(array_filter($items, function (array $item) use ($user): bool {
            if ($item['roles'] === null) {
                return true;
            }

            return $user->hasAnyRole($item['roles']);
        }));
    }
}

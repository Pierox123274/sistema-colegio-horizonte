<?php

namespace App\Enums;

enum StudentStatus: string
{
    case Activo = 'activo';
    case Inactivo = 'inactivo';
    case Retirado = 'retirado';
    case Egresado = 'egresado';

    public function label(): string
    {
        return match ($this) {
            self::Activo => 'Activo',
            self::Inactivo => 'Inactivo',
            self::Retirado => 'Retirado',
            self::Egresado => 'Egresado',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases(),
        );
    }
}

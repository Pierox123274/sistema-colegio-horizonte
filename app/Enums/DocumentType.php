<?php

namespace App\Enums;

enum DocumentType: string
{
    case Dni = 'dni';
    case Ce = 'ce';
    case Pasaporte = 'pasaporte';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Dni => 'DNI',
            self::Ce => 'C.E.',
            self::Pasaporte => 'Pasaporte',
            self::Otro => 'Otro',
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

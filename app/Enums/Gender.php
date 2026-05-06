<?php

namespace App\Enums;

enum Gender: string
{
    case Masculino = 'masculino';
    case Femenino = 'femenino';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Masculino => 'Masculino',
            self::Femenino => 'Femenino',
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

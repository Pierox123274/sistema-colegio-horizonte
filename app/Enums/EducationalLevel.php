<?php

namespace App\Enums;

enum EducationalLevel: string
{
    case Inicial = 'inicial';
    case Primaria = 'primaria';
    case Secundaria = 'secundaria';

    public function label(): string
    {
        return match ($this) {
            self::Inicial => 'Inicial',
            self::Primaria => 'Primaria',
            self::Secundaria => 'Secundaria',
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

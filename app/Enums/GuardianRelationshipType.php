<?php

namespace App\Enums;

enum GuardianRelationshipType: string
{
    case Padre = 'padre';
    case Madre = 'madre';
    case Abuelo = 'abuelo';
    case Abuela = 'abuela';
    case TutorLegal = 'tutor_legal';
    case Hermano = 'hermano';
    case Hermana = 'hermana';
    case Tio = 'tio';
    case Tia = 'tia';
    case Otro = 'otro';

    public function label(): string
    {
        return match ($this) {
            self::Padre => 'Padre',
            self::Madre => 'Madre',
            self::Abuelo => 'Abuelo',
            self::Abuela => 'Abuela',
            self::TutorLegal => 'Tutor legal',
            self::Hermano => 'Hermano',
            self::Hermana => 'Hermana',
            self::Tio => 'Tío',
            self::Tia => 'Tía',
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

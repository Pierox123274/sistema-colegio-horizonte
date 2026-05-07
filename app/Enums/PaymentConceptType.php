<?php

namespace App\Enums;

enum PaymentConceptType: string
{
    case Matricula = 'matricula';
    case Pension = 'pension';
    case Uniforme = 'uniforme';
    case Libro = 'libro';
    case Otro = 'otro';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Matricula->value, 'label' => 'Matrícula'],
            ['value' => self::Pension->value, 'label' => 'Pensión'],
            ['value' => self::Uniforme->value, 'label' => 'Uniforme'],
            ['value' => self::Libro->value, 'label' => 'Libro'],
            ['value' => self::Otro->value, 'label' => 'Otro'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

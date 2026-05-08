<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Presente = 'presente';
    case Tarde = 'tarde';
    case Falta = 'falta';
    case Justificado = 'justificado';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Presente->value, 'label' => 'Presente'],
            ['value' => self::Tarde->value, 'label' => 'Tarde'],
            ['value' => self::Falta->value, 'label' => 'Falta'],
            ['value' => self::Justificado->value, 'label' => 'Justificado'],
        ];
    }
}

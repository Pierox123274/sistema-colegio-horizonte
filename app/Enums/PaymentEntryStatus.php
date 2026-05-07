<?php

namespace App\Enums;

enum PaymentEntryStatus: string
{
    case Registrado = 'registrado';
    case Anulado = 'anulado';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Registrado->value, 'label' => 'Registrado'],
            ['value' => self::Anulado->value, 'label' => 'Anulado'],
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

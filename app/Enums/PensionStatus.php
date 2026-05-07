<?php

namespace App\Enums;

enum PensionStatus: string
{
    case Pendiente = 'pendiente';
    case Parcial = 'parcial';
    case Pagado = 'pagado';
    case Vencido = 'vencido';
    case Anulado = 'anulado';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Pendiente->value, 'label' => 'Pendiente'],
            ['value' => self::Parcial->value, 'label' => 'Parcial'],
            ['value' => self::Pagado->value, 'label' => 'Pagado'],
            ['value' => self::Vencido->value, 'label' => 'Vencido'],
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

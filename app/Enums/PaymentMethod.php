<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Efectivo = 'efectivo';
    case Yape = 'yape';
    case Plin = 'plin';
    case Transferencia = 'transferencia';
    case Tarjeta = 'tarjeta';
    case Otro = 'otro';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Efectivo->value, 'label' => 'Efectivo'],
            ['value' => self::Yape->value, 'label' => 'Yape'],
            ['value' => self::Plin->value, 'label' => 'Plin'],
            ['value' => self::Transferencia->value, 'label' => 'Transferencia'],
            ['value' => self::Tarjeta->value, 'label' => 'Tarjeta'],
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

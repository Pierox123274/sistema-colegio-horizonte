<?php

namespace App\Enums;

enum InventoryMovementType: string
{
    case Entrada = 'entrada';
    case Salida = 'salida';
    case Ajuste = 'ajuste';

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
            ['value' => self::Entrada->value, 'label' => 'Entrada'],
            ['value' => self::Salida->value, 'label' => 'Salida'],
            ['value' => self::Ajuste->value, 'label' => 'Ajuste'],
        ];
    }
}

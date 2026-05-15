<?php

namespace App\Enums;

enum AnnouncementPriority: string
{
    case Baja = 'baja';
    case Media = 'media';
    case Alta = 'alta';
    case Urgente = 'urgente';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Baja->value, 'label' => 'Baja'],
            ['value' => self::Media->value, 'label' => 'Media'],
            ['value' => self::Alta->value, 'label' => 'Alta'],
            ['value' => self::Urgente->value, 'label' => 'Urgente'],
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

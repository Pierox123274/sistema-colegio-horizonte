<?php

namespace App\Enums;

enum NotificationPriority: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
    case Critical = 'critical';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Low->value, 'label' => 'Baja'],
            ['value' => self::Medium->value, 'label' => 'Media'],
            ['value' => self::High->value, 'label' => 'Alta'],
            ['value' => self::Critical->value, 'label' => 'Crítica'],
        ];
    }
}

<?php

namespace App\Enums;

enum MeetingStatus: string
{
    case Scheduled = 'scheduled';
    case Live = 'live';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Scheduled->value, 'label' => 'Programada'],
            ['value' => self::Live->value, 'label' => 'En curso'],
            ['value' => self::Completed->value, 'label' => 'Finalizada'],
            ['value' => self::Cancelled->value, 'label' => 'Cancelada'],
        ];
    }
}

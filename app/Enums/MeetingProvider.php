<?php

namespace App\Enums;

enum MeetingProvider: string
{
    case GoogleMeet = 'google_meet';
    case Zoom = 'zoom';
    case Teams = 'teams';
    case Manual = 'manual';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::GoogleMeet->value, 'label' => 'Google Meet'],
            ['value' => self::Zoom->value, 'label' => 'Zoom'],
            ['value' => self::Teams->value, 'label' => 'Microsoft Teams'],
            ['value' => self::Manual->value, 'label' => 'Enlace manual'],
        ];
    }
}

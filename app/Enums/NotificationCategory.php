<?php

namespace App\Enums;

enum NotificationCategory: string
{
    case Academic = 'academic';
    case Financial = 'financial';
    case Security = 'security';
    case Lms = 'lms';
    case Ai = 'ai';
    case Gamification = 'gamification';
    case System = 'system';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Academic->value, 'label' => 'Académico'],
            ['value' => self::Financial->value, 'label' => 'Financiero'],
            ['value' => self::Security->value, 'label' => 'Seguridad'],
            ['value' => self::Lms->value, 'label' => 'Aula virtual / LMS'],
            ['value' => self::Ai->value, 'label' => 'IA'],
            ['value' => self::Gamification->value, 'label' => 'Gamificación'],
            ['value' => self::System->value, 'label' => 'Sistema'],
        ];
    }
}

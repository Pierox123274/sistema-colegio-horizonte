<?php

namespace App\Enums;

enum AuditSeverity: string
{
    case Info = 'info';
    case Warning = 'warning';
    case Critical = 'critical';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Info->value, 'label' => 'Informativo'],
            ['value' => self::Warning->value, 'label' => 'Advertencia'],
            ['value' => self::Critical->value, 'label' => 'Crítico'],
        ];
    }
}

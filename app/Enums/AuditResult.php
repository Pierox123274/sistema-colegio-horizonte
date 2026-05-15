<?php

namespace App\Enums;

enum AuditResult: string
{
    case Success = 'success';
    case Error = 'error';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Success->value, 'label' => 'Éxito'],
            ['value' => self::Error->value, 'label' => 'Error'],
        ];
    }
}

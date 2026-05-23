<?php

namespace App\Enums;

enum CmsMenuLocation: string
{
    case Header = 'header';
    case Footer = 'footer';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Header->value, 'label' => 'Cabecera'],
            ['value' => self::Footer->value, 'label' => 'Pie de página'],
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

<?php

namespace App\Enums;

enum CmsPublicationStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Draft->value, 'label' => 'Borrador'],
            ['value' => self::Published->value, 'label' => 'Publicado'],
            ['value' => self::Archived->value, 'label' => 'Archivado'],
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

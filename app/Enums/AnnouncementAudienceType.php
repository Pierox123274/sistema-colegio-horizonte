<?php

namespace App\Enums;

enum AnnouncementAudienceType: string
{
    case All = 'all';
    case Administrators = 'administrators';
    case Secretaries = 'secretaries';
    case Teachers = 'teachers';
    case Students = 'students';
    case Guardians = 'guardians';
    case CustomUsers = 'custom_users';

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::All->value, 'label' => 'Todos los usuarios'],
            ['value' => self::Administrators->value, 'label' => 'Administradores'],
            ['value' => self::Secretaries->value, 'label' => 'Secretaría'],
            ['value' => self::Teachers->value, 'label' => 'Docentes'],
            ['value' => self::Students->value, 'label' => 'Estudiantes'],
            ['value' => self::Guardians->value, 'label' => 'Apoderados'],
            ['value' => self::CustomUsers->value, 'label' => 'Usuarios específicos'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function roleName(): ?string
    {
        return match ($this) {
            self::Administrators => IntranetRole::Administrador->value,
            self::Secretaries => IntranetRole::Secretaria->value,
            self::Teachers => IntranetRole::Docente->value,
            self::Students => IntranetRole::Estudiante->value,
            self::Guardians => IntranetRole::Apoderado->value,
            default => null,
        };
    }
}

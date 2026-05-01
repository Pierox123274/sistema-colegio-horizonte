<?php

namespace App\Enums;

enum IntranetRole: string
{
    case Administrador = 'Administrador';
    case Secretaria = 'Secretaria';
    case Docente = 'Docente';
    case Estudiante = 'Estudiante';
    case Apoderado = 'Apoderado';

    /**
     * Pipe-separated list for Spatie `role:` middleware.
     */
    public static function middlewarePipe(): string
    {
        return implode('|', array_column(self::cases(), 'value'));
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

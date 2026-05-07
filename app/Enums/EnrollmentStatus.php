<?php

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Pendiente = 'pendiente';
    case Matriculado = 'matriculado';
    case Anulado = 'anulado';
    case Retirado = 'retirado';

    /**
     * Estados que impiden otra matrícula “activa” en el mismo año.
     */
    public function blocksConcurrentEnrollment(): bool
    {
        return $this === self::Pendiente || $this === self::Matriculado;
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return [
            ['value' => self::Pendiente->value, 'label' => 'Pendiente'],
            ['value' => self::Matriculado->value, 'label' => 'Matriculado'],
            ['value' => self::Anulado->value, 'label' => 'Anulado'],
            ['value' => self::Retirado->value, 'label' => 'Retirado'],
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

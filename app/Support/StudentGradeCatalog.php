<?php

namespace App\Support;

use App\Enums\EducationalLevel;

final class StudentGradeCatalog
{
    /**
     * @return list<string>
     */
    public static function gradesFor(EducationalLevel $level): array
    {
        return match ($level) {
            EducationalLevel::Inicial => ['3 años', '4 años', '5 años'],
            EducationalLevel::Primaria => ['1.º', '2.º', '3.º', '4.º', '5.º', '6.º'],
            EducationalLevel::Secundaria => ['1.º', '2.º', '3.º', '4.º', '5.º'],
        };
    }

    /**
     * Mapa nivel → lista de grados (para front y validación).
     *
     * @return array<string, list<string>>
     */
    public static function gradesByLevelMap(): array
    {
        $map = [];
        foreach (EducationalLevel::cases() as $level) {
            $map[$level->value] = self::gradesFor($level);
        }

        return $map;
    }
}

<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    public function run(): void
    {
        $year = AcademicYear::query()->firstOrCreate(
            ['year' => 2026],
            [
                'name' => 'Año académico 2026',
                'starts_at' => '2026-03-01',
                'ends_at' => '2026-12-20',
                'is_active' => true,
            ],
        );

        if ($year->is_active) {
            AcademicYear::query()->whereKeyNot($year->id)->update(['is_active' => false]);
        }
    }
}

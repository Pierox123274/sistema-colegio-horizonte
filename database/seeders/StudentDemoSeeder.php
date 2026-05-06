<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

/**
 * Datos de demostración opcionales.
 * Ejecutar: php artisan db:seed --class=StudentDemoSeeder
 */
class StudentDemoSeeder extends Seeder
{
    public function run(): void
    {
        Student::factory()->count(12)->create();
    }
}

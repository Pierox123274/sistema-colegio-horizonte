<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\EducationalLevel;
use App\Models\Grade;
use App\Models\Section;
use Illuminate\Database\Seeder;

class AcademicStructureSeeder extends Seeder
{
    public function run(): void
    {
        $inicial = EducationalLevel::query()->firstOrCreate(
            ['code' => 'INI'],
            [
                'name' => 'Inicial',
                'description' => 'Educación inicial (3, 4 y 5 años).',
                'is_active' => true,
            ],
        );

        $primaria = EducationalLevel::query()->firstOrCreate(
            ['code' => 'PRI'],
            [
                'name' => 'Primaria',
                'description' => 'Educación primaria (1.º a 6.º grado).',
                'is_active' => true,
            ],
        );

        $secundaria = EducationalLevel::query()->firstOrCreate(
            ['code' => 'SEC'],
            [
                'name' => 'Secundaria',
                'description' => 'Educación secundaria (1.º a 5.º grado).',
                'is_active' => true,
            ],
        );

        $this->seedGradesInicial($inicial);
        $this->seedGradesPrimaria($primaria);
        $this->seedGradesSecundaria($secundaria);

        $this->seedDemoSectionsAndClassrooms();
    }

    private function seedGradesInicial(EducationalLevel $level): void
    {
        $rows = [
            ['code' => 'INI-3', 'name' => '3 años', 'order' => 1],
            ['code' => 'INI-4', 'name' => '4 años', 'order' => 2],
            ['code' => 'INI-5', 'name' => '5 años', 'order' => 3],
        ];
        foreach ($rows as $row) {
            Grade::query()->firstOrCreate(
                [
                    'educational_level_id' => $level->id,
                    'code' => $row['code'],
                ],
                [
                    'name' => $row['name'],
                    'order' => $row['order'],
                    'is_active' => true,
                ],
            );
        }
    }

    private function seedGradesPrimaria(EducationalLevel $level): void
    {
        $labels = ['1.º', '2.º', '3.º', '4.º', '5.º', '6.º'];
        foreach ($labels as $i => $label) {
            $order = $i + 1;
            Grade::query()->firstOrCreate(
                [
                    'educational_level_id' => $level->id,
                    'code' => 'PRI-'.$order,
                ],
                [
                    'name' => $label.' grado',
                    'order' => $order,
                    'is_active' => true,
                ],
            );
        }
    }

    private function seedGradesSecundaria(EducationalLevel $level): void
    {
        $labels = ['1.º', '2.º', '3.º', '4.º', '5.º'];
        foreach ($labels as $i => $label) {
            $order = $i + 1;
            Grade::query()->firstOrCreate(
                [
                    'educational_level_id' => $level->id,
                    'code' => 'SEC-'.$order,
                ],
                [
                    'name' => $label.' grado',
                    'order' => $order,
                    'is_active' => true,
                ],
            );
        }
    }

    private function seedDemoSectionsAndClassrooms(): void
    {
        /** @var Grade|null $sampleGrade */
        $sampleGrade = Grade::query()->where('code', 'PRI-3')->first()
            ?? Grade::query()->first();

        if ($sampleGrade === null) {
            return;
        }

        $section = Section::query()->firstOrCreate(
            [
                'grade_id' => $sampleGrade->id,
                'code' => 'A',
            ],
            [
                'name' => 'Sección A',
                'capacity' => 28,
                'is_active' => true,
            ],
        );

        Classroom::query()->firstOrCreate(
            ['code' => 'AUL-PRI3-A-01'],
            [
                'section_id' => $section->id,
                'name' => 'Aula multimedia',
                'floor' => '2',
                'capacity' => 30,
                'description' => 'Aula demo vinculada a la sección A de 3.º primaria.',
                'is_active' => true,
            ],
        );

        Classroom::query()->firstOrCreate(
            ['code' => 'AUL-GEN-01'],
            [
                'section_id' => null,
                'name' => 'Aula multifuncional',
                'floor' => '1',
                'capacity' => 40,
                'description' => 'Espacio general sin sección asignada (demo).',
                'is_active' => true,
            ],
        );
    }
}

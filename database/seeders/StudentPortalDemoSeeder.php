<?php

namespace Database\Seeders;

use App\Enums\DocumentType;
use App\Enums\EducationalLevel;
use App\Enums\Gender;
use App\Enums\IntranetRole;
use App\Enums\StudentStatus;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentPortalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'estudiante@demo.com'],
            [
                'name' => 'Estudiante Demo',
                'password' => Hash::make('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([IntranetRole::Estudiante->value]);

        $student = Student::query()->where('user_id', $user->id)->first()
            ?? Student::query()->whereNull('user_id')->first()
            ?? Student::query()->firstOrCreate(
                ['code' => 'EST-000001'],
                [
                    'first_name' => 'Estudiante',
                    'last_name' => 'Demo',
                    'document_type' => DocumentType::Dni->value,
                    'document_number' => '70000001',
                    'birth_date' => '2012-05-15',
                    'gender' => Gender::Masculino->value,
                    'educational_level' => EducationalLevel::Primaria->value,
                    'grade' => '5.º',
                    'section' => 'A',
                    'status' => StudentStatus::Activo->value,
                    'email' => 'estudiante@demo.com',
                ],
            );

        if ($student->user_id !== $user->id) {
            $student->update(['user_id' => $user->id, 'email' => 'estudiante@demo.com']);
        }
    }
}

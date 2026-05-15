<?php

namespace Database\Seeders;

use App\Enums\IntranetRole;
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

        $student = Student::query()->whereNull('user_id')->first()
            ?? Student::factory()->create([
                'email' => 'estudiante@demo.com',
            ]);

        if ($student->user_id !== $user->id) {
            $student->update(['user_id' => $user->id]);
        }
    }
}

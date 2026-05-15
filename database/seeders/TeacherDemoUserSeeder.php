<?php

namespace Database\Seeders;

use App\Enums\IntranetRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherDemoUserSeeder extends Seeder
{
    /**
     * Usuario docente de demostración para entornos locales (idempotente por email).
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $user = User::query()->updateOrCreate(
            ['email' => 'docente@demo.com'],
            [
                'name' => 'Docente Demo',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        $user->syncRoles([IntranetRole::Docente->value]);
    }
}

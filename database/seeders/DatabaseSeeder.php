<?php

namespace Database\Seeders;

use App\Enums\IntranetRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(AcademicStructureSeeder::class);
        $this->call(AcademicYearSeeder::class);
        $this->call(PaymentConceptSeeder::class);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $user->syncRoles([IntranetRole::Administrador->value]);
    }
}

<?php

namespace Database\Seeders;

use App\Enums\IntranetRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);
        $this->call(CmsContentSeeder::class);
        $this->call(AcademicStructureSeeder::class);
        $this->call(AcademicYearSeeder::class);
        $this->call(PaymentConceptSeeder::class);
        $this->call(InventoryDemoSeeder::class);

        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles([IntranetRole::Administrador->value]);
    }
}

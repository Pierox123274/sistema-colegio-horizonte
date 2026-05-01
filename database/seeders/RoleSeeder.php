<?php

namespace Database\Seeders;

use App\Enums\IntranetRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (IntranetRole::cases() as $role) {
            Role::query()->firstOrCreate(
                ['name' => $role->value, 'guard_name' => 'web'],
            );
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role; // <-- Import Role model

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['super_admin','admin','user'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }
    }
}

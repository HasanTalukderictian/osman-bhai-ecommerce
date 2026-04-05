<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        $role = Role::where('name', 'super_admin')->first();

        // 🔥 IMPORTANT FIX
        if (!$role) {
            $role = Role::create([
                'name' => 'super_admin'
            ]);
        }

        // check if super admin already exists
        $exists = User::where('email', 'superadmin@gmail.com')->first();

        if (!$exists) {
            User::create([
                'name' => 'Super Admin',
                'email' => 'superadmin@gmail.com',
                'password' => Hash::make('password'),
                'role_id' => $role->id
            ]);
        }
    }
}

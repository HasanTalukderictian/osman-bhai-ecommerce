<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserInfo;

class UserInfoSeeder extends Seeder
{
    public function run()
    {
        UserInfo::create([
            'company_name' => 'Gazi Builders',
            'your_name' => 'Admin',
            'image' => null,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
use App\UserRole;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'superadmin@mail.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'role' => UserRole::SuperAdmin,
            ],
        );

        User::firstOrCreate(
            ['email' => 'redaktur@mail.com'],
            [
                'name' => 'Redaktur',
                'password' => bcrypt('password'),
                'role' => UserRole::Redaktur,
            ],
        );
    }
}

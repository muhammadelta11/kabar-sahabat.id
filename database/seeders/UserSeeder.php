<?php

namespace Database\Seeders;

use App\Models\User;
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
                'role' => 'super_admin',
            ],
        );

        User::firstOrCreate(
            ['email' => 'adminkonten@mail.com'],
            [
                'name' => 'Admin Konten',
                'password' => bcrypt('password'),
                'role' => 'admin_konten',
            ],
        );
    }
}

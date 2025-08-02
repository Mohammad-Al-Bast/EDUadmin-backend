<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('admin_users')->insert([
            [
                'email' => 'admin@example.com',
                'name' => 'Admin User',
                'password' => Hash::make('admin123'),
                'profile' => null,
                'campus' => 'Main',
                'school' => 'Engineering',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

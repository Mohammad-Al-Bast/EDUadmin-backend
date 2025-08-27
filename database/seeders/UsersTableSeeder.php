<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('password'),
                'is_verified' => false,
                'is_admin' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mohammad Al Bast',
                'email' => 'bast@gmail.com',
                'password' => Hash::make('12345678'),
                'is_verified' => true,
                'is_admin' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        \App\Models\User::factory()->count(10)->create();
    }
}

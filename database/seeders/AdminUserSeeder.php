<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default admin user
        User::updateOrCreate([
            'email' => 'admin@eduadmin.com'
        ], [
            'name' => 'System Administrator',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'is_verified' => true,
            'is_admin' => true,
            'campus' => 'Main Campus',
            'school' => 'Administration',
            'profile' => 'System Administrator Account',
        ]);

        // Create additional admin users using factory
        User::factory()->admin()->count(2)->create();
    }
}

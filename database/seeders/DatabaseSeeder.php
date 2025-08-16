<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create specific user
        \App\Models\User::create([
            'email' => 'bast@gmail.com',
            'password' => bcrypt('12345678'),
            'name' => 'Bast User', // Adding a default name
        ]);

        \App\Models\User::factory(10)->create();
        \App\Models\Course::factory(10)->create();
        \App\Models\CoursesChangeGradeForm::factory(10)->create();
        \App\Models\Student::factory(10)->create();
        \App\Models\ChangeGradeForm::factory(10)->create();
    }
}

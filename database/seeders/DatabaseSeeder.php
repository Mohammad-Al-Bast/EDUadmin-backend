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

        $this->call([
            UsersTableSeeder::class,
            AdminUsersTableSeeder::class,
            CoursesTableSeeder::class,
            CoursesChangeGradeFormTableSeeder::class,
            StudentsTableSeeder::class,
            ChangeGradeFormTableSeeder::class,
        ]);
    }
}

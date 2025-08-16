<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('students')->insert([
            [
                'student_name' => 'John Doe',
                'university_id' => '20250001', // 8-digit university ID
                'campus' => 'Main',
                'school' => 'Engineering',
                'major' => 'Computer Science',
                'semester' => 'Fall',
                'year' => 2025,
                'registered_courses_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

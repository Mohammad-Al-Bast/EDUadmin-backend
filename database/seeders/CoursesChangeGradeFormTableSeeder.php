<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesChangeGradeFormTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('courses_change_grade_form')->insert([
            [
                'courses_id_change_grade_form' => 1,
                'grade_type' => 'Midterm',
                'grade_percentage' => 30.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

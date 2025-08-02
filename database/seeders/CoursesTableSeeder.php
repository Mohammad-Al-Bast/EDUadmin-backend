<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('courses')->insert([
            [
                'course_code' => 'CS101',
                'course_name' => 'Introduction to Computer Science',
                'instructor' => 'Dr. Smith',
                'section' => 'A',
                'credits' => 3,
                'room' => '101',
                'schedule' => 'MWF',
                'days' => 'Mon,Wed,Fri',
                'time' => '09:00-10:00',
                'school' => 'Engineering',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

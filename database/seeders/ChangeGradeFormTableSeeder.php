<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChangeGradeFormTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('change_grade_form')->insert([
            [
                'student_id' => 1,
                'student_full_name' => 'John Doe',
                'semester_year' => 'Fall 2025',
                'major' => 'Computer Science',
                'campus' => 'Main',
                'instructor_name' => 'Dr. Smith',
                'course_code' => 'CS101',
                'course_name' => 'Introduction to Computer Science',
                'section' => 'A',
                'reason_for_change' => 'Grade miscalculation',
                'copy_of_original_grading_report' => null,
                'copy_of_graded_final_exam' => null,
                'tuition_report' => null,
                'copy_of_first_ten_pages_final_report' => null,
                'course_grade_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RegisterDropCourse;
use App\Models\Student;
use App\Models\Course;

class RegisterDropCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have students and courses to work with
        if (Student::count() === 0) {
            Student::factory(10)->create();
        }

        if (Course::count() === 0) {
            Course::factory(20)->create();
        }

        // Create various types of register/drop forms

        // Pending forms
        RegisterDropCourse::factory(5)->pending()->create();

        // Approved forms
        RegisterDropCourse::factory(3)->approved()->create();

        // Rejected forms
        RegisterDropCourse::factory(2)->rejected()->create();

        // Forms with only register courses
        RegisterDropCourse::factory(3)->registerOnly()->pending()->create();

        // Forms with only drop courses
        RegisterDropCourse::factory(2)->dropOnly()->pending()->create();

        $this->command->info('Created ' . RegisterDropCourse::count() . ' register/drop course forms.');
    }
}

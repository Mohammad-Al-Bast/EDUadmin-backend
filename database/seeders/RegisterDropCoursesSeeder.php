<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RegisterDropCoursesForm;
use App\Models\RegisterCourse;
use App\Models\DropCourse;
use App\Models\Student;

class RegisterDropCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have some students first
        if (Student::count() === 0) {
            Student::factory(10)->create();
        }

        // Create 5 register/drop courses forms
        RegisterDropCoursesForm::factory(5)->create()->each(function ($form) {
            // Add 1-3 register courses per form
            $registerCoursesCount = rand(1, 3);
            for ($i = 0; $i < $registerCoursesCount; $i++) {
                RegisterCourse::factory()->create([
                    'form_id' => $form->form_id
                ]);
            }

            // Add 0-2 drop courses per form (some forms may not have drop courses)
            $dropCoursesCount = rand(0, 2);
            for ($i = 0; $i < $dropCoursesCount; $i++) {
                DropCourse::factory()->create([
                    'form_id' => $form->form_id
                ]);
            }
        });
    }
}

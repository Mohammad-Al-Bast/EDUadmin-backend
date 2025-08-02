<?php

namespace Database\Factories;

use App\Models\ChangeGradeForm;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChangeGradeFormFactory extends Factory
{
    protected $model = ChangeGradeForm::class;

    public function definition(): array
    {
        return [
            'student_id' => 1, // You may want to set this dynamically
            'student_full_name' => $this->faker->name(),
            'semester_year' => $this->faker->word() . ' ' . $this->faker->year(),
            'major' => $this->faker->word(),
            'campus' => $this->faker->word(),
            'instructor_name' => $this->faker->name(),
            'course_code' => $this->faker->bothify('CS###'),
            'course_name' => $this->faker->words(3, true),
            'section' => $this->faker->randomElement(['A', 'B', 'C']),
            'reason_for_change' => $this->faker->sentence(),
            'copy_of_original_grading_report' => null,
            'copy_of_graded_final_exam' => null,
            'tuition_report' => null,
            'copy_of_first_ten_pages_final_report' => null,
            'course_grade_id' => 1, // You may want to set this dynamically
        ];
    }
}

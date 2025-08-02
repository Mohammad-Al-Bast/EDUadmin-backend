<?php

namespace Database\Factories;

use App\Models\CoursesChangeGradeForm;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoursesChangeGradeFormFactory extends Factory
{
    protected $model = CoursesChangeGradeForm::class;

    public function definition(): array
    {
        return [
            'courses_id_change_grade_form' => 1, // You may want to set this dynamically
            'grade_type' => $this->faker->randomElement(['Midterm', 'Final', 'Quiz']),
            'grade_percentage' => $this->faker->randomFloat(2, 0, 100),
        ];
    }
}

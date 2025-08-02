<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'student_name' => $this->faker->name(),
            'campus' => $this->faker->word(),
            'school' => $this->faker->word(),
            'major' => $this->faker->word(),
            'semester' => $this->faker->randomElement(['Fall', 'Spring', 'Summer']),
            'year' => $this->faker->year(),
            'registered_courses_id' => 1, // You may want to set this dynamically
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        return [
            'course_code' => $this->faker->unique()->bothify('CS###'),
            'course_name' => $this->faker->words(3, true),
            'instructor' => $this->faker->name(),
            'section' => $this->faker->randomElement(['A', 'B', 'C']),
            'credits' => $this->faker->numberBetween(1, 5),
            'room' => $this->faker->numberBetween(100, 500),
            'schedule' => $this->faker->randomElement(['MWF', 'TTh']),
            'days' => $this->faker->randomElement(['Mon,Wed,Fri', 'Tue,Thu']),
            'time' => $this->faker->time('H:i') . '-' . $this->faker->time('H:i'),
            'school' => $this->faker->word(),
        ];
    }
}

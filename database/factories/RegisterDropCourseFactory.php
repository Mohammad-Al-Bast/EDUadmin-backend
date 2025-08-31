<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\RegisterDropCourse;
use App\Models\Student;
use App\Models\Course;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RegisterDropCourse>
 */
class RegisterDropCourseFactory extends Factory
{
    protected $model = RegisterDropCourse::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random courses for the form
        $courses = Course::inRandomOrder()->take(fake()->numberBetween(1, 4))->get();

        $coursesData = $courses->map(function ($course) {
            return [
                'id' => $course->course_id,
                'course_code' => $course->course_code,
                'course_name' => $course->course_name,
                'action' => fake()->randomElement(['register', 'drop']),
                'credits' => $course->credits,
                'section' => $course->section,
                'instructor' => $course->instructor,
            ];
        })->toArray();

        return [
            'student_id' => Student::factory(),
            'semester' => fake()->randomElement(['Fall', 'Spring', 'Summer']),
            'academic_year' => fake()->randomElement(['2024-2025', '2025-2026', '2026-2027']),
            'courses' => $coursesData,
            'reason' => fake()->paragraph(3),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'submitted_at' => fake()->dateTimeBetween('-30 days', 'now'),
            'processed_at' => function (array $attributes) {
                return $attributes['status'] !== 'pending' ? fake()->dateTimeBetween($attributes['submitted_at'], 'now') : null;
            },
            'processed_by' => function (array $attributes) {
                return $attributes['status'] !== 'pending' ? fake()->numberBetween(1, 10) : null;
            },
        ];
    }

    /**
     * Indicate that the form is pending.
     */
    public function pending(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'pending',
            'processed_at' => null,
            'processed_by' => null,
        ]);
    }

    /**
     * Indicate that the form is approved.
     */
    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'approved',
            'processed_at' => fake()->dateTimeBetween($attributes['submitted_at'] ?? '-7 days', 'now'),
            'processed_by' => fake()->numberBetween(1, 10),
        ]);
    }

    /**
     * Indicate that the form is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'rejected',
            'processed_at' => fake()->dateTimeBetween($attributes['submitted_at'] ?? '-7 days', 'now'),
            'processed_by' => fake()->numberBetween(1, 10),
        ]);
    }

    /**
     * Create a form with only register courses.
     */
    public function registerOnly(): static
    {
        return $this->state(function (array $attributes) {
            $courses = Course::inRandomOrder()->take(fake()->numberBetween(1, 3))->get();

            $coursesData = $courses->map(function ($course) {
                return [
                    'id' => $course->course_id,
                    'course_code' => $course->course_code,
                    'course_name' => $course->course_name,
                    'action' => 'register',
                    'credits' => $course->credits,
                    'section' => $course->section,
                    'instructor' => $course->instructor,
                ];
            })->toArray();

            return [
                'courses' => $coursesData,
            ];
        });
    }

    /**
     * Create a form with only drop courses.
     */
    public function dropOnly(): static
    {
        return $this->state(function (array $attributes) {
            $courses = Course::inRandomOrder()->take(fake()->numberBetween(1, 3))->get();

            $coursesData = $courses->map(function ($course) {
                return [
                    'id' => $course->course_id,
                    'course_code' => $course->course_code,
                    'course_name' => $course->course_name,
                    'action' => 'drop',
                    'credits' => $course->credits,
                    'section' => $course->section,
                    'instructor' => $course->instructor,
                ];
            })->toArray();

            return [
                'courses' => $coursesData,
            ];
        });
    }
}

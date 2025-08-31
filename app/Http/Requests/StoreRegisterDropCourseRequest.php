<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Course;
use App\Models\Student;

class StoreRegisterDropCourseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'student_id' => 'required|exists:students,student_id',
            'semester' => 'required|string|max:50',
            'academic_year' => 'required|string|max:20',
            'courses' => 'required|array|min:1',
            'courses.*.id' => 'required|exists:courses,course_id',
            'courses.*.action' => ['required', Rule::in(['register', 'drop'])],
            'reason' => 'required|string|min:10|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'student_id.required' => 'Student ID is required.',
            'student_id.exists' => 'The selected student does not exist.',
            'semester.required' => 'Semester is required.',
            'academic_year.required' => 'Academic year is required.',
            'courses.required' => 'At least one course is required.',
            'courses.min' => 'At least one course must be selected.',
            'courses.*.id.required' => 'Course ID is required for each course.',
            'courses.*.id.exists' => 'One or more selected courses do not exist.',
            'courses.*.action.required' => 'Action (register/drop) is required for each course.',
            'courses.*.action.in' => 'Action must be either "register" or "drop".',
            'reason.required' => 'Reason is required.',
            'reason.min' => 'Reason must be at least 10 characters long.',
            'reason.max' => 'Reason cannot exceed 1000 characters.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateRegistrationRules($validator);
            $this->validateDropRules($validator);
            $this->validateDeadlines($validator);
            $this->validateDuplicateCourses($validator);
        });
    }

    private function validateRegistrationRules($validator)
    {
        if (!$this->has('courses')) {
            return;
        }

        $registerCourses = collect($this->courses)->where('action', 'register');

        foreach ($registerCourses as $index => $course) {
            // Check if course exists and get course details
            $courseModel = Course::find($course['id']);
            if (!$courseModel) {
                continue;
            }

            // TODO: Add business logic validation:
            // - Check prerequisites
            // - Check credit limits (e.g., max 18 credits per semester)
            // - Check time conflicts
            // - Check course capacity
            // - Check if student is already registered for this course

            // Example: Check if student already registered
            // $student = Student::find($this->student_id);
            // if ($student && $this->isStudentAlreadyRegistered($student, $courseModel)) {
            //     $validator->errors()->add("courses.{$index}.id", "Student is already registered for {$courseModel->course_name}");
            // }
        }
    }

    private function validateDropRules($validator)
    {
        if (!$this->has('courses')) {
            return;
        }

        $dropCourses = collect($this->courses)->where('action', 'drop');

        foreach ($dropCourses as $index => $course) {
            // Check if course exists
            $courseModel = Course::find($course['id']);
            if (!$courseModel) {
                continue;
            }

            // TODO: Add business logic validation:
            // - Check if student is currently enrolled in this course
            // - Check drop deadline
            // - Check minimum credit requirements after drop
            // - Calculate financial implications

            // Example: Check if student is enrolled
            // $student = Student::find($this->student_id);
            // if ($student && !$this->isStudentEnrolledIn($student, $courseModel)) {
            //     $validator->errors()->add("courses.{$index}.id", "Student is not enrolled in {$courseModel->course_name}");
            // }
        }
    }

    private function validateDeadlines($validator)
    {
        // TODO: Implement deadline validation
        // - Registration deadline
        // - Drop deadline
        // - Different deadlines for different semesters

        // Example implementation:
        // $currentDate = now();
        // $registrationDeadline = $this->getRegistrationDeadline($this->semester, $this->academic_year);
        // $dropDeadline = $this->getDropDeadline($this->semester, $this->academic_year);

        // if ($currentDate->gt($registrationDeadline)) {
        //     $registerCourses = collect($this->courses)->where('action', 'register');
        //     if ($registerCourses->isNotEmpty()) {
        //         $validator->errors()->add('courses', 'Registration deadline has passed for this semester.');
        //     }
        // }

        // if ($currentDate->gt($dropDeadline)) {
        //     $dropCourses = collect($this->courses)->where('action', 'drop');
        //     if ($dropCourses->isNotEmpty()) {
        //         $validator->errors()->add('courses', 'Drop deadline has passed for this semester.');
        //     }
        // }
    }

    private function validateDuplicateCourses($validator)
    {
        if (!$this->has('courses')) {
            return;
        }

        $courseIds = collect($this->courses)->pluck('id');
        $duplicates = $courseIds->duplicates();

        if ($duplicates->isNotEmpty()) {
            $validator->errors()->add('courses', 'Duplicate courses are not allowed in the same form.');
        }
    }

    // Helper methods for business logic (to be implemented)

    // private function isStudentAlreadyRegistered($student, $course)
    // {
    //     // Check if student is already registered for this course
    //     // This would typically check an enrollments table
    //     return false;
    // }

    // private function isStudentEnrolledIn($student, $course)
    // {
    //     // Check if student is currently enrolled in this course
    //     // This would typically check an enrollments table
    //     return true;
    // }

    // private function getRegistrationDeadline($semester, $academicYear)
    // {
    //     // Get registration deadline for the given semester and academic year
    //     // This would typically come from a settings table or configuration
    //     return now()->addDays(30);
    // }

    // private function getDropDeadline($semester, $academicYear)
    // {
    //     // Get drop deadline for the given semester and academic year
    //     // This would typically come from a settings table or configuration
    //     return now()->addDays(45);
    // }
}

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

    protected function prepareForValidation()
    {
        // Transform frontend data format to backend format
        $data = $this->all();

        // Transform studentId to university_id
        if (isset($data['studentId'])) {
            $data['university_id'] = $data['studentId'];
            unset($data['studentId']);
        }

        // Transform courses array to registered_courses and dropped_courses
        if (isset($data['courses']) && is_array($data['courses'])) {
            $registeredCourses = [];
            $droppedCourses = [];

            foreach ($data['courses'] as $course) {
                $courseData = [
                    'course_id' => $course['courseId'] ?? $course['course_id'] ?? null,
                    'course_code' => $course['course_code'] ?? 'TBD',
                    'course_name' => $course['course_name'] ?? 'TBD',
                    'section' => $course['section'] ?? 'TBD',
                    'instructor' => $course['instructor'] ?? 'TBD',
                    'credits' => $course['credits'] ?? 3,
                ];

                if (($course['action'] ?? '') === 'register') {
                    $registeredCourses[] = $courseData;
                } elseif (($course['action'] ?? '') === 'drop') {
                    $droppedCourses[] = $courseData;
                }
            }

            if (!empty($registeredCourses)) {
                $data['registered_courses'] = $registeredCourses;
            }
            if (!empty($droppedCourses)) {
                $data['dropped_courses'] = $droppedCourses;
            }

            unset($data['courses']);
        }

        // Set default values for missing required fields
        if (!isset($data['semester'])) {
            $data['semester'] = 'Fall 2025'; // Default semester
        }
        if (!isset($data['academic_year'])) {
            $data['academic_year'] = '2025-2026'; // Default academic year
        }

        $this->replace($data);
    }

    public function rules()
    {
        return [
            'university_id' => 'required|numeric|digits:8|exists:students,university_id',
            'semester' => 'sometimes|string|max:50',
            'academic_year' => 'sometimes|string|max:20',
            'reason' => 'required|string|min:10|max:1000',

            // Registered courses
            'registered_courses' => 'sometimes|array',
            'registered_courses.*.course_id' => 'required_with:registered_courses|exists:courses,course_id',
            'registered_courses.*.course_code' => 'sometimes|string',
            'registered_courses.*.course_name' => 'sometimes|string',
            'registered_courses.*.section' => 'sometimes|string',
            'registered_courses.*.instructor' => 'sometimes|string',
            'registered_courses.*.credits' => 'sometimes|integer|min:1',

            // Dropped courses
            'dropped_courses' => 'sometimes|array',
            'dropped_courses.*.course_id' => 'required_with:dropped_courses|exists:courses,course_id',
            'dropped_courses.*.course_code' => 'sometimes|string',
            'dropped_courses.*.course_name' => 'sometimes|string',
            'dropped_courses.*.section' => 'sometimes|string',
            'dropped_courses.*.instructor' => 'sometimes|string',
            'dropped_courses.*.credits' => 'sometimes|integer|min:1',
        ];
    }

    public function messages()
    {
        return [
            'university_id.required' => 'University ID is required.',
            'university_id.numeric' => 'University ID must be numeric.',
            'university_id.digits' => 'University ID must be exactly 8 digits.',
            'university_id.exists' => 'The provided university ID does not exist.',
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
            // Ensure at least one course is being registered or dropped
            if (empty($this->registered_courses) && empty($this->dropped_courses)) {
                $validator->errors()->add('courses', 'At least one course must be registered or dropped.');
            }

            $this->validateRegistrationRules($validator);
            $this->validateDropRules($validator);
            $this->validateDeadlines($validator);
            $this->validateDuplicateCourses($validator);
        });
    }

    private function validateRegistrationRules($validator)
    {
        if (!$this->has('registered_courses')) {
            return;
        }

        foreach ($this->registered_courses as $index => $course) {
            // Check if course exists and get course details
            $courseModel = Course::find($course['course_id']);
            if (!$courseModel) {
                $validator->errors()->add("registered_courses.{$index}.course_id", "Course not found.");
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
            //     $validator->errors()->add("registered_courses.{$index}.course_id", "Student is already registered for {$courseModel->course_name}");
            // }
        }
    }

    private function validateDropRules($validator)
    {
        if (!$this->has('dropped_courses')) {
            return;
        }

        foreach ($this->dropped_courses as $index => $course) {
            // Check if course exists
            $courseModel = Course::find($course['course_id']);
            if (!$courseModel) {
                $validator->errors()->add("dropped_courses.{$index}.course_id", "Course not found.");
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
            //     $validator->errors()->add("dropped_courses.{$index}.course_id", "Student is not enrolled in {$courseModel->course_name}");
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
        //     if ($this->has('registered_courses') && !empty($this->registered_courses)) {
        //         $validator->errors()->add('registered_courses', 'Registration deadline has passed for this semester.');
        //     }
        // }

        // if ($currentDate->gt($dropDeadline)) {
        //     if ($this->has('dropped_courses') && !empty($this->dropped_courses)) {
        //         $validator->errors()->add('dropped_courses', 'Drop deadline has passed for this semester.');
        //     }
        // }
    }

    private function validateDuplicateCourses($validator)
    {
        $allCourseIds = [];

        // Check for duplicates in registered courses
        if ($this->has('registered_courses')) {
            $registeredIds = collect($this->registered_courses)->pluck('course_id')->toArray();
            $allCourseIds = array_merge($allCourseIds, $registeredIds);
        }

        // Check for duplicates in dropped courses
        if ($this->has('dropped_courses')) {
            $droppedIds = collect($this->dropped_courses)->pluck('course_id')->toArray();
            $allCourseIds = array_merge($allCourseIds, $droppedIds);
        }

        // Check for duplicates across both arrays
        if (count($allCourseIds) !== count(array_unique($allCourseIds))) {
            $validator->errors()->add('courses', 'Duplicate courses are not allowed in the same form.');
        }
    }    // Helper methods for business logic (to be implemented)

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

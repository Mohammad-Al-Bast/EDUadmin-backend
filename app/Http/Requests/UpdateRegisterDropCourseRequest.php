<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegisterDropCourseRequest extends FormRequest
{
    public function authorize()
    {
        // Only allow updates if the form is in pending status
        $form = $this->route('form');
        return $form && $form->canBeEdited();
    }

    public function rules()
    {
        return [
            'semester' => 'sometimes|string|max:50',
            'academic_year' => 'sometimes|string|max:20',
            'courses' => 'sometimes|array|min:1',
            'courses.*.id' => 'required_with:courses|exists:courses,course_id',
            'courses.*.action' => ['required_with:courses', Rule::in(['register', 'drop'])],
            'reason' => 'sometimes|string|min:10|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'semester.string' => 'Semester must be a string.',
            'academic_year.string' => 'Academic year must be a string.',
            'courses.array' => 'Courses must be an array.',
            'courses.min' => 'At least one course must be selected.',
            'courses.*.id.required_with' => 'Course ID is required for each course.',
            'courses.*.id.exists' => 'One or more selected courses do not exist.',
            'courses.*.action.required_with' => 'Action (register/drop) is required for each course.',
            'courses.*.action.in' => 'Action must be either "register" or "drop".',
            'reason.string' => 'Reason must be a string.',
            'reason.min' => 'Reason must be at least 10 characters long.',
            'reason.max' => 'Reason cannot exceed 1000 characters.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Apply the same business logic as store request if courses are being updated
            if ($this->has('courses')) {
                $this->validateRegistrationRules($validator);
                $this->validateDropRules($validator);
                $this->validateDeadlines($validator);
                $this->validateDuplicateCourses($validator);
            }
        });
    }

    private function validateRegistrationRules($validator)
    {
        // Same logic as StoreRegisterDropCourseRequest
        // ... (implementation details)
    }

    private function validateDropRules($validator)
    {
        // Same logic as StoreRegisterDropCourseRequest
        // ... (implementation details)
    }

    private function validateDeadlines($validator)
    {
        // Same logic as StoreRegisterDropCourseRequest
        // ... (implementation details)
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
}

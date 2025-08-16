<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportCoursesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // We'll handle authorization in middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:csv,xlsx,xls',
                'max:10240', // 10MB max
            ],
            'semester' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9\s\-_]+$/', // Allow alphanumeric, spaces, hyphens, underscores
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'Please select a file to upload.',
            'file.file' => 'The uploaded file is not valid.',
            'file.mimes' => 'The file must be a CSV, XLS, or XLSX file.',
            'file.max' => 'The file size must not exceed 10MB.',
            'semester.required' => 'Semester is required.',
            'semester.string' => 'Semester must be a valid string.',
            'semester.max' => 'Semester must not exceed 50 characters.',
            'semester.regex' => 'Semester contains invalid characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'file' => 'import file',
            'semester' => 'semester',
        ];
    }
}

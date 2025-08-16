<?php

namespace App\Http\Controllers;

use App\Models\ChangeGradeForm;
use App\Models\Student;
use Illuminate\Http\Request;

class ChangeGradeFormController extends Controller
{
    public function index()
    {
        $forms = ChangeGradeForm::with('student')->get();
        return response()->json($forms);
    }

    public function show($id)
    {
        $form = ChangeGradeForm::with('student')->findOrFail($id);
        return response()->json($form);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'university_id' => 'required|numeric|digits:8|exists:students,university_id',
            'student_full_name' => 'required|string|max:255',
            'semester_year' => 'required|string|max:255',
            'major' => 'required|string|max:255',
            'campus' => 'required|string|max:255',
            'instructor_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:255',
            'course_name' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'reason_for_change' => 'required|string|max:500',
            'copy_of_original_grading_report' => 'nullable|string',
            'copy_of_graded_final_exam' => 'nullable|string',
            'tuition_report' => 'nullable|string',
            'copy_of_first_ten_pages_final_report' => 'nullable|string',
            'course_grade_id' => 'nullable|integer',
        ]);

        // Find the student by university_id and get the internal student_id
        $student = Student::where('university_id', $validated['university_id'])->firstOrFail();
        
        // Replace university_id with student_id for database storage
        $validated['student_id'] = $student->student_id;
        unset($validated['university_id']);

        $item = ChangeGradeForm::create($validated);
        
        // Load the student relationship and return
        $item->load('student');
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = ChangeGradeForm::findOrFail($id);
        
        $validated = $request->validate([
            'university_id' => 'sometimes|numeric|digits:8|exists:students,university_id',
            'student_full_name' => 'sometimes|string|max:255',
            'semester_year' => 'sometimes|string|max:255',
            'major' => 'sometimes|string|max:255',
            'campus' => 'sometimes|string|max:255',
            'instructor_name' => 'sometimes|string|max:255',
            'course_code' => 'sometimes|string|max:255',
            'course_name' => 'sometimes|string|max:255',
            'section' => 'sometimes|string|max:255',
            'reason_for_change' => 'sometimes|string|max:500',
            'copy_of_original_grading_report' => 'sometimes|nullable|string',
            'copy_of_graded_final_exam' => 'sometimes|nullable|string',
            'tuition_report' => 'sometimes|nullable|string',
            'copy_of_first_ten_pages_final_report' => 'sometimes|nullable|string',
            'course_grade_id' => 'sometimes|nullable|integer',
        ]);

        // If university_id is provided, convert it to student_id
        if (isset($validated['university_id'])) {
            $student = Student::where('university_id', $validated['university_id'])->firstOrFail();
            $validated['student_id'] = $student->student_id;
            unset($validated['university_id']);
        }

        $item->update($validated);
        $item->load('student');
        return response()->json($item);
    }

    public function destroy($id)
    {
        ChangeGradeForm::destroy($id);
        return response()->json(null, 204);
    }
}

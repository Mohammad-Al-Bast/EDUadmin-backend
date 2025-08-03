<?php

namespace App\Http\Controllers;

use App\Models\ChangeGradeForm;
use Illuminate\Http\Request;

class ChangeGradeFormController extends Controller
{
    public function index()
    {
        return response()->json(ChangeGradeForm::all());
    }

    public function show($id)
    {
        return response()->json(ChangeGradeForm::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id'  => 'required|exists:courses,id',
            'new_grade'  => 'required|string|max:5', // adjust type/range as needed
            'reason'     => 'required|string|max:500',
            // Prevent duplicate requests for same student/course/grade
            // You may need a unique index in DB for this
            // Example:
            // 'student_id' => 'unique:change_grade_forms,student_id,NULL,id,course_id,' . $request->course_id . ',new_grade,' . $request->new_grade,
        ]);

        $item = ChangeGradeForm::create($validated);
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = ChangeGradeForm::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        ChangeGradeForm::destroy($id);
        return response()->json(null, 204);
    }
}

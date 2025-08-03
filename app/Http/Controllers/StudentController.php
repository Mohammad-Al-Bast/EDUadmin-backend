<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return response()->json(Student::all());
    }

    public function show($id)
    {
        return response()->json(Student::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:students,email',
            'student_number' => 'required|string|unique:students,student_number',
            // Add other fields and rules as needed
        ]);

        $student = Student::create($validated);
        return response()->json($student, 201);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'sometimes|required|string|max:255',
            'email'          => 'sometimes|required|email|unique:students,email,' . $student->id,
            'student_number' => 'sometimes|required|string|unique:students,student_number,' . $student->id,
            // Add other fields and rules as needed
        ]);

        $student->update($validated);
        return response()->json($student);
    }

    public function destroy($id)
    {
        Student::destroy($id);
        return response()->json(null, 204);
    }
}

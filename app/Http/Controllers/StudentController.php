<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class StudentController extends Controller
{
    public function index()
    {
        return response()->json(Student::all());
    }

    public function show($id)
    {
        try {
            return response()->json(Student::findOrFail($id));
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Student not found'], 404);
        }
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
        try {
            $student = Student::findOrFail($id);

            $validated = $request->validate([
                'name'           => 'sometimes|required|string|max:255',
                'email'          => 'sometimes|required|email|unique:students,email,' . $student->id,
                'student_number' => 'sometimes|required|string|unique:students,student_number,' . $student->id,
                // Add other fields and rules as needed
            ]);

            $student->update($validated);
            return response()->json($student);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Student not found'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->delete();
            return response()->json(null, 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Student not found'], 404);
        }
    }
}

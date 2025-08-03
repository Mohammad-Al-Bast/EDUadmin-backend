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
        $request->validate([
            'student_name' => 'required|string|max:255',
            // Add other fields and rules as needed
        ]);

        if (Student::where('student_name', $request->name)->exists()) {
            return response()->json(['message' => 'Student with this name already exists'], 409);
        }

        $student = Student::create($request->all());
        return response()->json($student, 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);

            $validated = $request->validate([
                'student_name'           => 'sometimes|required|string|max:255',
                'campus'                 => 'sometimes|nullable|string|max:255',
                'school'                 => 'sometimes|nullable|string|max:255',
                'major'                  => 'sometimes|nullable|string|max:255',
                'semester'               => 'sometimes|nullable|string|max:255',
                'year'                   => 'sometimes|nullable|integer',
                'registered_courses_id'  => 'sometimes|nullable|integer',
                // Add other fields and rules as needed
            ]);

            if (empty($validated)) {
                return response()->json(['message' => 'No valid fields to update'], 400);
            }

            $student->fill($validated);

            if ($student->isDirty()) {
                $student->save();
                return response()->json([
                    'message' => 'Student updated successfully',
                    'student' => $student->fresh()
                ]);
            } else {
                return response()->json(['message' => 'No changes detected'], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Student not found'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
            $student->delete();
            return response()->json([
                'message' => 'Student deleted successfully',
                'student' => $student
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Student not found'], 404);
        }
    }
}

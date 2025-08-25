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

    public function show($university_id)
    {
        try {
            $student = Student::where('university_id', $university_id)->firstOrFail();
            return response()->json($student);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Student not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_name' => 'required|string|max:255',
            'university_id' => 'required|integer|digits:8|unique:students,university_id',
            'campus' => 'nullable|string|max:255',
            'school' => 'nullable|string|max:255',
            'major' => 'nullable|string|max:255',
            'semester' => 'nullable|string|max:255',
            'year' => 'nullable|integer',
            'registered_courses_id' => 'nullable|integer',
        ]);

        $student = Student::create($request->all());
        return response()->json($student, 201);
    }

    public function update(Request $request, $university_id)
    {
        try {
            $student = Student::where('university_id', $university_id)->firstOrFail();

            $validated = $request->validate([
                'student_name'           => 'sometimes|required|string|max:255',
                'university_id'          => 'sometimes|required|integer|digits:8|unique:students,university_id,' . $student->student_id . ',student_id',
                'campus'                 => 'sometimes|nullable|string|max:255',
                'school'                 => 'sometimes|nullable|string|max:255',
                'major'                  => 'sometimes|nullable|string|max:255',
                'semester'               => 'sometimes|nullable|string|max:255',
                'year'                   => 'sometimes|nullable|integer',
                'registered_courses_id'  => 'sometimes|nullable|integer',
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

    public function destroy($university_id)
    {
        try {
            $student = Student::where('university_id', $university_id)->firstOrFail();
            $student->delete();
            return response()->json([
                'message' => 'Student deleted successfully',
                'student' => $student
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Student not found'], 404);
        }
    }

    public function destroyAll()
    {
        try {
            $deletedCount = Student::count();

            if ($deletedCount === 0) {
                return response()->json(['message' => 'No students found to delete'], 404);
            }

            Student::truncate(); // More efficient than delete() for all records

            return response()->json([
                'message' => 'All students deleted successfully',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete students'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        return response()->json(Course::all());
    }

    public function show($id)
    {
        try {
            return response()->json(Course::findOrFail($id));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Course not found'], 404);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_name' => 'required|string|max:255|unique:courses,course_name',
            'course_code' => 'required|string|max:50|unique:courses,course_code',
            // Add other fields and rules as needed
        ]);

        $course = Course::create($validated);
        return response()->json($course, 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $course = Course::findOrFail($id);


            $validated = $request->validate([
                'course_name' => 'sometimes|required|string|max:255|unique:courses,course_name,' . $course->id,
                'course_code' => 'sometimes|required|string|max:50|unique:courses,course_code,' . $course->id,
                // Add other fields and rules as needed
            ]);

            if (empty($validated)) {
                return response()->json(['message' => 'No valid fields to update'], 400);
            }

            $course->fill($validated);

            if ($course->isDirty()) {
                $course->save();
                return response()->json([
                    'message' => 'Course updated successfully',
                    'course' => $course->fresh()
                ]);
            } else {
                return response()->json(['message' => 'No changes detected'], 200);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Course not found'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $course = Course::findOrFail($id);
            $course->delete();
            return response()->json([
                'message' => 'Course deleted successfully',
                'course' => $course
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Course not found'], 404);
        }
    }
}

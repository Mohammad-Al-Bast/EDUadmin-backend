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
        try {
            $validated = $request->validate([
                'course_name'        => 'required|string|max:255|unique:courses,course_name,NULL,course_id',
                'course_code'        => 'required|string|max:50|unique:courses,course_code,NULL,course_id',
                'instructor'         => 'required|string|max:255',
                'section'            => 'required|string|max:50',
                'credits'            => 'required|integer',
                'room'               => 'required|string|max:50',
                'schedule'           => 'required|string|max:255',
                'days'               => 'required|string|max:255',
                'time'               => 'required|string|max:50',
                'school'             => 'required|string|max:255',
            ]);

            $course = Course::create($validated);
            return response()->json($course, 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->validator->errors();
            if ($errors->has('course_name') || $errors->has('course_code')) {
                return response()->json(['message' => 'This course already exists.'], 409);
            }
            throw $e;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $course = Course::findOrFail($id);



            $validated = $request->validate([
                'course_name'        => 'sometimes|required|string|max:255|unique:courses,course_name,' . $course->course_id . ',course_id',
                'course_code'        => 'sometimes|required|string|max:50|unique:courses,course_code,' . $course->course_id . ',course_id',
                'course_description' => 'sometimes|nullable|string',
                'course_credits'     => 'sometimes|nullable|integer',
                'department'         => 'sometimes|nullable|string|max:255',
                'instructor'         => 'sometimes|nullable|string|max:255',
                'section'            => 'sometimes|nullable|string|max:50',
                'credits'            => 'sometimes|nullable|integer',
                'room'               => 'sometimes|nullable|string|max:50',
                'schedule'           => 'sometimes|nullable|string|max:255',
                'days'               => 'sometimes|nullable|string|max:255',
                'time'               => 'sometimes|nullable|string|max:50',
                'school'             => 'sometimes|nullable|string|max:255',
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

    public function destroyAll()
    {
        try {
            $deletedCount = Course::count();

            if ($deletedCount === 0) {
                return response()->json(['message' => 'No courses found to delete'], 404);
            }

            Course::truncate(); // More efficient than delete() for all records

            return response()->json([
                'message' => 'All courses deleted successfully',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete courses'], 500);
        }
    }
}

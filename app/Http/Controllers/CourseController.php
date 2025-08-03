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
        return response()->json(Course::findOrFail($id));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:courses,name',
            'code' => 'required|string|max:50|unique:courses,code',
            // Add other fields and rules as needed
        ]);

        $course = Course::create($validated);
        return response()->json($course, 201);
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:courses,name,' . $course->id,
            'code' => 'sometimes|required|string|max:50|unique:courses,code,' . $course->id,
            // Add other fields and rules as needed
        ]);

        $course->update($validated);
        return response()->json($course);
    }

    public function destroy($id)
    {
        Course::destroy($id);
        return response()->json(null, 204);
    }
}

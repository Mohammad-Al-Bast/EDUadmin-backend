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
        $student = Student::create($request->all());
        return response()->json($student, 201);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);
        $student->update($request->all());
        return response()->json($student);
    }

    public function destroy($id)
    {
        Student::destroy($id);
        return response()->json(null, 204);
    }
}

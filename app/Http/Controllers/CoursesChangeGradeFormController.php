<?php

namespace App\Http\Controllers;

use App\Models\CoursesChangeGradeForm;
use Illuminate\Http\Request;

class CoursesChangeGradeFormController extends Controller
{
    public function index()
    {
        return response()->json(CoursesChangeGradeForm::all());
    }

    public function show($id)
    {
        return response()->json(CoursesChangeGradeForm::findOrFail($id));
    }

    public function store(Request $request)
    {
        $item = CoursesChangeGradeForm::create($request->all());
        return response()->json($item, 201);
    }

    public function update(Request $request, $id)
    {
        $item = CoursesChangeGradeForm::findOrFail($id);
        $item->update($request->all());
        return response()->json($item);
    }

    public function destroy($id)
    {
        CoursesChangeGradeForm::destroy($id);
        return response()->json(null, 204);
    }
}

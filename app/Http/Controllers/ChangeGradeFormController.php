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
        $item = ChangeGradeForm::create($request->all());
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

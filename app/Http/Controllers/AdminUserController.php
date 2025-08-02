<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        return response()->json(AdminUser::all());
    }

    public function show($id)
    {
        return response()->json(AdminUser::findOrFail($id));
    }

    public function store(Request $request)
    {
        $adminUser = AdminUser::create($request->all());
        return response()->json($adminUser, 201);
    }

    public function update(Request $request, $id)
    {
        $adminUser = AdminUser::findOrFail($id);
        $adminUser->update($request->all());
        return response()->json($adminUser);
    }

    public function destroy($id)
    {
        AdminUser::destroy($id);
        return response()->json(null, 204);
    }
}

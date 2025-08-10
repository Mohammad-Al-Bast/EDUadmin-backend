<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        // Only allow admins to list all users (you may want to add admin check here)
        return response()->json(User::all());
    }

    public function show($id)
    {
        $user = User::findOrFail($id);
        
        // Users can only view their own profile unless they're admin
        if (Auth::id() !== (int)$id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($user);
    }

    public function store(Request $request)
    {
        // Only allow admin users to create new users
        // You may want to add proper admin authorization here
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:8',
            'is_verified' => 'sometimes|boolean',
            'is_admin'    => 'sometimes|boolean',
            'campus'      => 'nullable|string|max:255',
            'school'      => 'nullable|string|max:255',
            'profile'     => 'nullable|string',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        
        return response()->json($user, 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Users can only update their own profile unless they're admin
        if (Auth::id() !== (int)$id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name'        => 'sometimes|required|string|max:255',
            'email'       => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password'    => 'sometimes|required|string|min:8',
            'is_verified' => 'sometimes|boolean',
            'is_admin'    => 'sometimes|boolean',
            'campus'      => 'sometimes|nullable|string|max:255',
            'school'      => 'sometimes|nullable|string|max:255',
            'profile'     => 'sometimes|nullable|string',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json($user);
    }

    public function destroy($id)
    {
        // Users can only delete their own account unless they're admin
        if (Auth::id() !== (int)$id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        User::destroy($id);
        return response()->json(['message' => 'User deleted successfully']);
    }
}

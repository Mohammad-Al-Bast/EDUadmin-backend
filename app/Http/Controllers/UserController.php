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
        $currentUser = Auth::user();

        // Users can view their own profile OR admins can view any profile
        if ($currentUser->id !== (int)$id && !$currentUser->is_admin) {
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
        $currentUser = Auth::user();

        // Users can update their own profile OR admins can update any profile
        if ($currentUser->id !== (int)$id && !$currentUser->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Define validation rules
        $rules = [
            'name'        => 'sometimes|required|string|max:255',
            'email'       => 'sometimes|required|email|unique:users,email,' . $user->id,
            'campus'      => 'sometimes|nullable|string|max:255',
            'school'      => 'sometimes|nullable|string|max:255',
            'profile'     => 'sometimes|nullable|string',
        ];

        // Only admins can update sensitive fields
        if ($currentUser->is_admin) {
            $rules['password'] = 'sometimes|required|string|min:8';
            $rules['is_verified'] = 'sometimes|boolean';
            $rules['is_admin'] = 'sometimes|boolean';
        }

        $validated = $request->validate($rules);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh()
        ]);
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

    public function verifyUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $originalStatus = $user->is_verified;
        $user->is_verified = true;
        $saved = $user->save();

        return response()->json([
            'message' => 'User verified successfully',
            'original_status' => $originalStatus,
            'new_status' => $user->is_verified,
            'save_result' => $saved,
            'user_id' => $user->id
        ], 200);
    }

    public function blockUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $originalStatus = $user->is_verified;
        $user->is_verified = false;
        $saved = $user->save();

        return response()->json([
            'message' => 'User blocked successfully',
            'original_status' => $originalStatus,
            'new_status' => $user->is_verified,
            'save_result' => $saved,
            'user_id' => $user->id
        ], 200);
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Validate the incoming password
        $validated = $request->validate([
            'password' => 'required|string|min:8|max:255'
        ]);

        // Use the provided password or generate a random one if not provided
        $newPassword = $validated['password'];
        $user->password = bcrypt($newPassword);
        $user->save();

        return response()->json([
            'message' => 'Password reset successfully',
            'new_password' => $newPassword
        ], 200);
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $deleted = $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
            'delete_result' => $deleted,
            'user_id' => $id
        ], 200);
    }
}

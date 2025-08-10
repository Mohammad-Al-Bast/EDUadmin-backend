<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisteredAdminUserController extends Controller
{
    // Registration for admin users
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|string|min:8',
        ]);

        $admin = \App\Models\AdminUser::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json(['message' => 'Admin registration successful', 'admin' => $admin], 201);
    }

    // Example: Admin dashboard
    public function dashboard()
    {
        return response()->json([
            'message' => 'Welcome to the admin dashboard!',
            'admin' => Auth::user(),
        ]);
    }

    // Example: Manage users page
    public function manageUsers()
    {
        // You can fetch all users or add more logic here
        return response()->json([
            'message' => 'Manage users page.',
            // 'users' => User::all(), // Uncomment if needed
        ]);
    }

    // Example: Site settings page
    public function siteSettings()
    {
        return response()->json([
            'message' => 'Site settings page.',
        ]);
    }

    // Example: Reports page
    public function reports()
    {
        return response()->json([
            'message' => 'Reports page.',
        ]);
    }
}

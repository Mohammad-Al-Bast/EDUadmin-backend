<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisteredUserController extends Controller
{
    // Registration for regular users
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json(['message' => 'Registration successful', 'user' => $user], 201);
    }

    // Example: Dashboard page for registered users
    public function dashboard()
    {
        return response()->json([
            'message' => 'Welcome to your dashboard!',
            'user' => Auth::user(),
        ]);
    }

    // Example: Profile page for registered users
    public function profile()
    {
        return response()->json([
            'message' => 'This is your profile page.',
            'user' => Auth::user(),
        ]);
    }

    // Example: Settings page for registered users
    public function settings()
    {
        return response()->json([
            'message' => 'This is your settings page.',
            'user' => Auth::user(),
        ]);
    }
}

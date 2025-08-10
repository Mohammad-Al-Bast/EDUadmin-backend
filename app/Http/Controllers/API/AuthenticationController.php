<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthenticationController extends Controller
{
    // Register a new user
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Set remember token using Laravel's built-in method
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Refresh the model to get the database defaults
        $user->refresh();

        return response()->json([
            'message' => 'Registration successful!',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_verified' => $user->is_verified,
                'is_admin' => $user->is_admin,
                'email_verified_at' => $user->email_verified_at
            ]
        ], 201);
    }

    // Login user
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Check if user is verified
        if (!$user->is_verified) {
            return response()->json(['error' => 'Your account is not verified. Please contact the system administrator to verify your account before you can log in.'], 403);
        }

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_verified' => $user->is_verified,
                'is_admin' => $user->is_admin,
                'email_verified_at' => $user->email_verified_at
            ]
        ]);
    }

    // Get authenticated user info
    public function userInfo(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_verified' => $user->is_verified,
                'is_admin' => $user->is_admin,
                'email_verified_at' => $user->email_verified_at
            ]
        ]);
    }

    // Update user profile
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $emailChanged = $user->email !== $request->email;
        
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at
        ]);

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();
            $message = 'Profile updated successfully. Please verify your new email address.';
        } else {
            $message = 'Profile updated successfully.';
        }

        return response()->json([
            'message' => $message,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_verified' => $user->is_verified,
                'is_admin' => $user->is_admin,
                'email_verified_at' => $user->email_verified_at
            ]
        ]);
    }

    // Logout user
    public function logOut(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out successfully']);
    }
}

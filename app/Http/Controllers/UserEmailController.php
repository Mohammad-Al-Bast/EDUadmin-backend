<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserEmail;
use Illuminate\Http\Request;

class UserEmailController extends Controller
{
    // GET: /api/users/{user}/emails
    public function index(User $user)
    {
        return response()->json($user->userEmails);
    }

    // POST: /api/users/{user}/emails
    public function store(Request $request, User $user)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check for max 5 emails per user
        if ($user->userEmails()->count() >= 5) {
            return response()->json(['error' => 'Maximum 5 emails per user'], 422);
        }

        // Check for duplicate
        if ($user->userEmails()->where('email', $request->email)->exists()) {
            return response()->json(['error' => 'Email already exists for this user'], 422);
        }

        $userEmail = $user->userEmails()->create([
            'email' => $request->email,
            'is_locked' => false,
        ]);

        return response()->json($userEmail, 201);
    }
}

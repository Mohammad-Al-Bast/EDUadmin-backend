<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminAndVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Check if user is authenticated
        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'error' => 'Authentication required',
                'details' => 'You must be logged in to perform this action.'
            ], 401);
        }

        // Check if user is admin
        if (!$this->isAdmin($user)) {
            return response()->json([
                'message' => 'Forbidden.',
                'error' => 'Administrator access required',
                'details' => 'Only administrators can perform this action.',
                'user_role' => 'regular_user'
            ], 403);
        }

        // Check if user is verified by admin
        if (!$this->isVerifiedByAdmin($user)) {
            return response()->json([
                'message' => 'Your account is not verified by an administrator.',
                'error' => 'Account verification required',
                'details' => 'Please contact your system administrator to verify your account.',
                'verification_status' => [
                    'is_verified' => false,
                    'is_admin' => true,
                    'verification_type' => 'admin_approval'
                ],
                'contact' => 'Please contact your system administrator for account verification.'
            ], 403);
        }

        return $next($request);
    }

    /**
     * Check if user is an administrator
     */
    private function isAdmin($user): bool
    {
        return (bool) $user->is_admin;
    }

    /**
     * Check if user is verified by admin
     */
    private function isVerifiedByAdmin($user): bool
    {
        return (bool) $user->is_verified;
    }
}

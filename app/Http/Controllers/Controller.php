<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    /**
     * Check if the authenticated user can perform admin actions
     */
    protected function canPerformAdminActions(Request $request): bool
    {
        $user = $request->user();
        return $user && $user->canPerformAdminActions();
    }

    /**
     * Get user verification status for API responses
     */
    protected function getUserVerificationStatus(Request $request): array
    {
        $user = $request->user();
        return $user ? $user->getVerificationStatus() : [];
    }

    /**
     * Return a standardized admin verification error response
     */
    protected function adminVerificationErrorResponse(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'error' => 'Authentication required'
            ], 401);
        }

        if (!$user->isAdmin()) {
            return response()->json([
                'message' => 'Forbidden.',
                'error' => 'Administrator access required',
                'user_status' => $this->getUserVerificationStatus($request)
            ], 403);
        }

        if (!$user->isVerified()) {
            return response()->json([
                'message' => 'Your account is not verified by an administrator.',
                'error' => 'Account verification required',
                'details' => 'Please contact your system administrator to verify your account.',
                'user_status' => $this->getUserVerificationStatus($request),
                'contact' => 'Please contact your system administrator for account verification.'
            ], 403);
        }

        return response()->json([
            'message' => 'Unknown verification error',
            'error' => 'Verification check failed'
        ], 500);
    }
}

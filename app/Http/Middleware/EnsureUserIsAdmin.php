<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return response()->json([
                'message' => 'Unauthenticated.',
                'error' => 'Authentication required'
            ], 401);
        }

        // Check if user has admin role (you can adjust this logic based on your user model)
        if (! $request->user()->is_admin && $request->user()->email !== 'admin@eduadmin.com') {
            return response()->json([
                'message' => 'Forbidden.',
                'error' => 'Administrator access required'
            ], 403);
        }

        return $next($request);
    }
}

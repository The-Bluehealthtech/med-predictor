<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Must be authenticated
        if (!$user) {
            return response()->json([
                'message' => 'Forbidden. Insufficient permissions.',
                'error' => 'INSUFFICIENT_PERMISSIONS'
            ], 403);
        }

        // Super admins and system admins inherit all roles (including association_admin)
        if (in_array($user->role, ['super_admin', 'system_admin'])) {
            return $next($request);
        }

        // Exact role match fallback
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Forbidden. Insufficient permissions.',
                'error' => 'INSUFFICIENT_PERMISSIONS'
            ], 403);
        }

        return $next($request);
    }
} 
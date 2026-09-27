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

        // Normalize display labels such as "System_admin" or "Admin".
        $userRole = strtolower(str_replace(['-', ' '], '_', (string) $user->role));
        $allowedRoles = array_map(
            static fn ($role) => strtolower(str_replace(['-', ' '], '_', (string) $role)),
            $roles
        );

        // Super admins and system admins inherit all roles.
        if (in_array($userRole, ['super_admin', 'system_admin'], true)) {
            return $next($request);
        }

        // Exact normalized role match fallback.
        if (!in_array($userRole, $allowedRoles, true)) {
            return response()->json([
                'message' => 'Forbidden. Insufficient permissions.',
                'error' => 'INSUFFICIENT_PERMISSIONS'
            ], 403);
        }

        return $next($request);
    }
} 
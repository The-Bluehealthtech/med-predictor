<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantEnforcer
{
    /**
     * Ensure authenticated users have a tenant context unless system admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !in_array($user->role, ['system_admin', 'super_admin'])) {
            if (empty($user->tenant_id)) {
                // Deny access for sensitive routes when tenant is missing
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Tenant context required',
                        'error' => 'TENANT_REQUIRED'
                    ], 403);
                }
                return redirect()->route('dashboard')->withErrors(['tenant' => 'Tenant context required']);
            }
        }

        return $next($request);
    }
}





<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\RBACService;
use Symfony\Component\HttpFoundation\Response;

class UnifiedPermissionMiddleware
{
    protected RBACService $rbacService;

    public function __construct(RBACService $rbacService)
    {
        $this->rbacService = $rbacService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->get('auth_user');
        
        if (!$user) {
            Log::error('Permission check failed: No authenticated user found');
            return $this->handleInsufficientPermissions($request, $permission);
        }

        // Check if user has the required permission
        if (!$this->rbacService->userHasPermission($user, $permission)) {
            Log::warning('Permission denied', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'required_permission' => $permission,
                'user_permissions' => $this->rbacService->getUserPermissions($user),
                'url' => $request->url(),
                'method' => $request->method()
            ]);

            return $this->handleInsufficientPermissions($request, $permission);
        }

        // Log successful permission check
        Log::info('Permission granted', [
            'user_id' => $user->id,
            'permission' => $permission,
            'url' => $request->url()
        ]);

        return $next($request);
    }

    /**
     * Handle insufficient permissions
     */
    protected function handleInsufficientPermissions(Request $request, string $permission): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Forbidden. Insufficient permissions.',
                'error' => 'INSUFFICIENT_PERMISSIONS',
                'required_permission' => $permission,
                'permission_description' => $this->rbacService->getPermissionDescription($permission)
            ], 403);
        }

        return redirect()->route('dashboard')->with('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette fonctionnalité.');
    }
}




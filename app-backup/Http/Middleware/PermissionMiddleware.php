<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. Please log in.',
                    'error' => 'UNAUTHORIZED'
                ], 401);
            }
            
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Vérifier d'abord les permissions individuelles de l'utilisateur
        $userPermissions = $user->permissions ?? [];
        if (is_string($userPermissions)) {
            $userPermissions = json_decode($userPermissions, true) ?? [];
        }
        
        // Si l'utilisateur a des permissions individuelles, les vérifier
        if (!empty($userPermissions)) {
            if (!in_array($permission, $userPermissions)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Forbidden. Insufficient permissions.',
                        'error' => 'INSUFFICIENT_PERMISSIONS',
                        'required_permission' => $permission,
                        'user_permissions' => $userPermissions
                    ], 403);
                }
                
                abort(403, 'Forbidden. Insufficient permissions.');
            }
        } else {
            // Fallback sur les Gates Laravel (basés sur les rôles)
            if (!Gate::allows($permission)) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Forbidden. Insufficient permissions.',
                        'error' => 'INSUFFICIENT_PERMISSIONS',
                        'required_permission' => $permission,
                        'user_role' => $user->role
                    ], 403);
                }
                
                abort(403, 'Forbidden. Insufficient permissions.');
            }
        }

        return $next($request);
    }
}

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
        
        // Vérifier si l'utilisateur a la permission en utilisant les Gates Laravel
        if (!Gate::allows($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden. Insufficient permissions.',
                    'error' => 'INSUFFICIENT_PERMISSIONS'
                ], 403);
            }
            
            abort(403, 'Forbidden. Insufficient permissions.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RBACMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission = null): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return redirect()->route('login')->withErrors(['email' => 'Authentification requise.']);
        }
        
        // Si une permission spécifique est demandée
        if ($permission) {
            if (!$user->hasPermission($permission)) {
                abort(403, 'Accès refusé. Permission requise: ' . $permission);
            }
        }
        
        return $next($request);
    }
}

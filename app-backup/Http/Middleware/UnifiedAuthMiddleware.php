<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Services\RBACService;
use Symfony\Component\HttpFoundation\Response;

class UnifiedAuthMiddleware
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
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return $this->handleUnauthenticated($request);
        }

        $user = Auth::user();
        
        // Log authentication event
        Log::info('User authenticated', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'url' => $request->url(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Add user context to request
        $request->merge([
            'auth_user' => $user,
            'auth_permissions' => $this->rbacService->getUserPermissions($user)
        ]);

        return $next($request);
    }

    /**
     * Handle unauthenticated requests
     */
    protected function handleUnauthenticated(Request $request): Response
    {
        Log::warning('Unauthenticated access attempt', [
            'url' => $request->url(),
            'method' => $request->method(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Unauthenticated. Please log in.',
                'error' => 'UNAUTHENTICATED',
                'redirect_url' => route('login')
            ], 401);
        }

        return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder à cette page.');
    }
}







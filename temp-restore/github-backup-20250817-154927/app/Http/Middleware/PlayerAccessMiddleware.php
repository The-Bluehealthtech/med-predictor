<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PlayerAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Check if user is authenticated and has player role
        if (!$user || $user->role !== 'player') {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden. Insufficient permissions.',
                    'error' => 'INSUFFICIENT_PERMISSIONS'
                ], 403);
            }
            return redirect()->route('login', ['access_type' => 'player'])
                ->with('error', 'Access denied. Only players can access the Player Dashboard.');
        }

        // Check if the user logged in through the correct access type
        $session = $request->session();
        $loginAccessType = $session->get('login_access_type');
        
        // For debugging - temporarily allow access if session is missing
        if ($loginAccessType !== 'player') {
            // Log for debugging
            \Log::info('PlayerAccessMiddleware: Access type mismatch', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'session_access_type' => $loginAccessType,
                'expected' => 'player',
                'url' => $request->url()
            ]);
            
            // For now, allow access if user is a player (temporary fix)
            if ($user->role === 'player') {
                return $next($request);
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden. Insufficient permissions.',
                    'error' => 'INSUFFICIENT_PERMISSIONS'
                ], 403);
            }
            
            // Log the user out and redirect to the correct login URL
            Auth::logout();
            $session->invalidate();
            $session->regenerateToken();
            
            return redirect()->route('login', ['access_type' => 'player'])
                ->with('error', 'Please login through the Player Portal to access the Player Dashboard.');
        }

        return $next($request);
    }
} 
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RefereeAccessMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Check if user is authenticated and has referee role or is system admin
        if (!$user || ($user->role !== 'referee' && $user->role !== 'system_admin')) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden. Insufficient permissions.',
                    'error' => 'INSUFFICIENT_PERMISSIONS'
                ], 403);
            }
            return redirect()->route('login', ['access_type' => 'referee'])
                ->with('error', 'Access denied. Only referees and system admins can access the Referee Dashboard.');
        }

        // Check if the user logged in through the correct access type
        $session = $request->session();
        $loginAccessType = $session->get('login_access_type');
        
        // If no access type is set in session, set it based on user role
        if (!$loginAccessType && ($user->role === 'referee' || $user->role === 'system_admin')) {
            $session->put('login_access_type', $user->role === 'referee' ? 'referee' : 'admin');
            return $next($request);
        }
        
        // If access type doesn't match, redirect to proper login
        if ($loginAccessType !== 'referee' && $loginAccessType !== 'admin') {
            // Log for debugging
            \Log::info('RefereeAccessMiddleware: Access type mismatch', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'session_access_type' => $loginAccessType,
                'expected' => 'referee or admin',
                'url' => $request->url()
            ]);
            
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
            
            return redirect()->route('login', ['access_type' => 'referee'])
                ->with('error', 'Please login through the Referee Portal to access the Referee Dashboard.');
        }

        return $next($request);
    }
} 
<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                // If user is authenticated, redirect to appropriate dashboard based on role
                $user = Auth::guard($guard)->user();
                
                // Check if there's a specific access type requested
                $accessType = $request->query('access_type');
                
                // If access type is specified, validate it matches the user's role
                if ($accessType) {
                    $allowedRoles = $this->getAllowedRolesForAccessType($accessType);
                    if (!in_array($user->role, $allowedRoles)) {
                        // User doesn't have the right role for this access type
                        Auth::logout();
                        return redirect()->route('login', ['access_type' => $accessType])
                            ->with('error', 'You do not have permission to access this portal.');
                    }
                }
                
                // Redirect based on role and access type
                if ($accessType === 'referee' && $user->role === 'referee') {
                    return redirect('/referee/dashboard');
                } elseif ($accessType === 'player' && $user->role === 'player') {
                    return redirect('/player-dashboard');
                } elseif ($accessType === 'club' && $user->isClubUser()) {
                    return redirect('/club-management/dashboard');
                } elseif ($accessType === 'association' && $user->isAssociationUser()) {
                    return redirect('/dashboard');
                }
                
                // Default redirects based on role
                if ($user->isSystemAdmin() || $user->role === 'admin') {
                    return redirect('/dashboard');
                } elseif ($user->isAssociationUser()) {
                    return redirect('/dashboard');
                } elseif ($user->isClubUser()) {
                    return redirect('/club-management/dashboard');
                } elseif ($user->isPlayer()) {
                    return redirect('/player-dashboard');
                } elseif ($user->isReferee()) {
                    return redirect('/referee/dashboard');
                }
                
                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
    
    /**
     * Get allowed roles for each access type.
     */
    private function getAllowedRolesForAccessType(string $accessType): array
    {
        $roleMap = [
            'club' => ['club_admin', 'club_manager', 'club_staff', 'club_medical'],
            'association' => ['association_admin', 'association_staff', 'association_registrar', 'association_medical', 'admin'],
            'player' => ['player'],
            'referee' => ['referee'],
            'medical_staff' => ['medical_staff', 'medical_admin', 'club_medical', 'association_medical'],
        ];

        return $roleMap[$accessType] ?? [];
    }
} 
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthSessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur a une session valide OU s'il est connecté via Auth
        if (!session()->has('user_role') && !auth()->check()) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        // Si pas de session user_role mais connecté via Auth, créer la session
        if (!session()->has('user_role') && auth()->check()) {
            $user = auth()->user();
            if ($user->role === 'admin') {
                session(['user_role' => 'admin']);
            } elseif ($user->role === 'player') {
                session(['user_role' => 'player']);
            }
        }

        return $next($request);
    }
}

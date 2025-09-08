<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ValidateSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (Auth::check()) {
            $user = Auth::user();
            
            // Vérifier si l'utilisateur existe toujours en base
            if (!$user || !$user->exists) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                
                return redirect()->route('login')->with('error', 'Votre compte n\'existe plus. Veuillez vous reconnecter.');
            }
            
            // Vérifier si la session Laravel est valide
            // Laravel utilise automatiquement la session pour maintenir l'authentification
            // Si Auth::check() retourne true, la session est valide
            // Pas besoin de vérifier une clé spécifique
            
            // Optionnel : vérifier si la session n'a pas expiré
            $sessionLifetime = config('session.lifetime') * 60; // en secondes
            $lastActivity = session('last_activity', time());
            
            if (time() - $lastActivity > $sessionLifetime) {
                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();
                
                return redirect()->route('login')->with('error', 'Votre session a expiré. Veuillez vous reconnecter.');
            }
            
            // Mettre à jour l'activité de la session
            session(['last_activity' => time()]);
        }
        
        return $next($request);
    }
}

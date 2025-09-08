<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Gestion spéciale pour les joueurs - redirection directe vers leur FIT Portal
            $user = Auth::user();
            if ($user->role === 'player' && $user->player_id) {
                // Redirection directe vers le portail joueur simple
                return redirect('/test-portail-joueur-simple?player_id=' . $user->player_id);
            }
            
            // Redirection par rôle
            $route = match ($user->role) {
                'referee' => 'referee.dashboard',
                'player' => 'player-dashboard', // Fallback pour les joueurs sans player_id
                'club_admin', 'club_manager', 'club_medical' => 'dashboard',
                'association_admin', 'association_registrar', 'association_medical' => 'dashboard',
                'system_admin', 'super_admin', 'admin' => 'dashboard',
                default => 'dashboard',
            };
            return redirect()->intended(route($route));
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ]);
    }

    public function logout(Request $request)
    {
        // Si on est dans un contexte joueur, juste sortir du contexte
        if ($request->session()->has('player_context')) {
            $request->session()->forget('player_context');
            return redirect('/login')->with('message', 'Retour à la connexion admin');
        }
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Vous avez été déconnecté avec succès.');
    }
}

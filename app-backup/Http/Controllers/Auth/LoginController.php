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
            
            // Gestion spéciale pour les joueurs - créer un contexte utilisateur séparé
            $user = Auth::user();
            if ($user->role === 'player' && $user->player_id) {
                // Stocker l'utilisateur joueur dans un contexte séparé
                $request->session()->put('player_context', [
                    'user_id' => $user->id,
                    'player_id' => $user->player_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role
                ]);
                
                // Déconnecter le joueur de la session principale pour préserver l'admin
                Auth::logout();
                
                return redirect('/test-portail-joueur-simple?player_id=' . $user->player_id);
            }
            
            return redirect()->intended('dashboard');
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
        return redirect('/');
    }
}

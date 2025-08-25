<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Player;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'user_type' => 'required|in:player,admin'
        ]);

        if ($request->user_type === 'player') {
            // Connexion joueur
            $player = Player::where('email', $request->email)->first();
            
            if (!$player) {
                return back()->withErrors(['email' => 'Joueur non trouvé.']);
            }

            // Vérifier le mot de passe (simulation - en production, utiliser Hash)
            if ($request->password === ($player->password ?? 'password123')) {
                session([
                    'user_type' => 'player',
                    'player_id' => $player->id,
                    'player_name' => $player->first_name . ' ' . $player->last_name
                ]);
                
                return redirect()->route('joueur.portal', $player->id);
            }
            
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
            
        } else {
            // Connexion admin
            $user = User::where('email', $request->email)->first();
            
            if (!$user || !in_array($user->role, ['system_admin', 'association_admin'])) {
                return back()->withErrors(['email' => 'Accès administrateur non autorisé.']);
            }

            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                session(['user_type' => 'admin']);
                return redirect()->route('modules.index');
            }
            
            return back()->withErrors(['password' => 'Mot de passe incorrect.']);
        }
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        session()->forget(['user_type', 'player_id', 'player_name']);
        Auth::logout();
        
        return redirect()->route('login');
    }

    /**
     * Vérifier si l'utilisateur est connecté
     */
    public static function isAuthenticated()
    {
        return session()->has('user_type');
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    public static function isAdmin()
    {
        return session('user_type') === 'admin';
    }

    /**
     * Vérifier si l'utilisateur est un joueur
     */
    public static function isPlayer()
    {
        return session('user_type') === 'player';
    }

    /**
     * Obtenir l'ID du joueur connecté
     */
    public static function getPlayerId()
    {
        return session('player_id');
    }
}

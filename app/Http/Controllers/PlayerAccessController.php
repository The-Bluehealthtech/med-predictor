<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class PlayerAccessController extends Controller
{
    /**
     * Afficher le formulaire d'accès pour un joueur spécifique
     */
    public function showAccessForm(string $playerId): View
    {
        try {
            $player = Player::findOrFail($playerId);
            
            // Vérifier si le joueur a déjà un compte utilisateur
            $hasUserAccount = User::where('player_id', $playerId)->exists();
            
            return view('player-access.form', compact('player', 'hasUserAccount'));
        } catch (\Exception $e) {
            abort(404, 'Joueur non trouvé');
        }
    }
    
    /**
     * Authentifier un joueur et créer une session
     */
    public function authenticate(Request $request, string $playerId): RedirectResponse
    {
        try {
            \Log::info('PlayerAccessController authenticate called', [
                'player_id' => $playerId,
            ]);
            
            $player = Player::findOrFail($playerId);
            \Log::info('Player found', ['player' => $player->toArray()]);
            
            // Vérifier si le joueur a un compte utilisateur
            $user = User::where('player_id', $playerId)->first();
            \Log::info('User found', ['user' => $user ? $user->toArray() : null]);
            
            if (!$user) {
                // Créer un compte utilisateur automatiquement pour le joueur
                $user = $this->createPlayerUserAccount($player);
                \Log::info('User account created', ['user' => $user->toArray()]);
            }
            
            // Vérifier le mot de passe ou créer une session directe
            if ($request->has('password') && $request->password) {
                \Log::info('Password authentication attempted');
                // Authentification par mot de passe
                if (!Hash::check($request->password, $user->password)) {
                    \Log::warning('Password authentication failed');
                    return back()->withErrors(['password' => 'Mot de passe incorrect']);
                }
                \Log::info('Password authentication successful');
            } else {
                \Log::info('Access code authentication attempted');
                // Authentification par code d'accès unique
                $accessCode = $request->input('access_code');

                
                if (!$this->validateAccessCode($player, $accessCode)) {
                    \Log::warning('Player access code validation failed', [
                        'player_id' => $player->id,
                    ]);
                    return back()->withErrors(['access_code' => 'Code d\'accès incorrect']);
                }
                \Log::info('Access code validation successful');
            }
            
            // Créer une session pour le joueur
            Auth::login($user);
            \Log::info('User logged in successfully', ['userId' => $user->id]);
            
            // Mettre à jour les informations de connexion
            $user->update([
                'last_login_at' => now(),
                'login_count' => $user->login_count + 1
            ]);
            
            // Rediriger vers le portail du joueur
            \Log::info('Redirecting to player portal');
            return redirect()->route('player.portal', ['playerId' => $playerId])
                           ->with('success', 'Bienvenue ' . $player->first_name . ' !');
                           
        } catch (\Exception $e) {
            \Log::error('PlayerAccessController authenticate error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Erreur lors de l\'authentification']);
        }
    }
    
    /**
     * Créer un compte utilisateur pour un joueur
     */
    private function createPlayerUserAccount(Player $player): User
    {
        $email = $player->email ?? 'joueur.' . $player->id . '@fifa-connect.local';
        
        $user = User::create([
            'name' => $player->first_name . ' ' . $player->last_name,
            'email' => $email,
            'password' => Hash::make(Str::random(12)), // Mot de passe temporaire
            'role' => 'player',
            'player_id' => $player->id,
            'club_id' => $player->club_id,
            'association_id' => $player->association_id,
            'status' => 'active',
            'permissions' => ['player_portal_access'],
            'preferences' => [
                'language' => 'fr',
                'timezone' => 'Europe/Paris',
                'notifications_email' => true,
                'notifications_sms' => false
            ]
        ]);
        
        return $user;
    }
    
    /**
     * Valider le code d'accès unique
     */
    private function validateAccessCode(Player $player, string $accessCode): bool
    {
        // Code d'accès basé sur l'ID du joueur et sa date de naissance
        $expectedCode = $this->generateAccessCode($player);
        
        return $accessCode === $expectedCode;
    }
    
    /**
     * Générer un code d'accès unique pour un joueur
     */
    private function generateAccessCode(Player $player): string
    {
        // Code basé sur l'ID du joueur et sa date de naissance
        $base = $player->id . $player->date_of_birth->format('dmY');
        return strtoupper(substr(md5($base), 0, 8));
    }
    
}

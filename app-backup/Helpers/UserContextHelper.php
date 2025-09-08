<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserContextHelper
{
    /**
     * Obtenir l'utilisateur actuel (admin ou joueur selon le contexte)
     */
    public static function getCurrentUser()
    {
        // Si on est dans un contexte joueur, retourner les infos du joueur
        if (Session::has('player_context')) {
            return Session::get('player_context');
        }
        
        // Sinon retourner l'utilisateur authentifié normal
        return Auth::user();
    }
    
    /**
     * Vérifier si on est dans un contexte joueur
     */
    public static function isPlayerContext(): bool
    {
        return Session::has('player_context');
    }
    
    /**
     * Obtenir l'ID du joueur actuel
     */
    public static function getCurrentPlayerId(): ?int
    {
        if (self::isPlayerContext()) {
            return Session::get('player_context.player_id');
        }
        
        return Auth::user()->player_id ?? null;
    }
    
    /**
     * Obtenir le rôle de l'utilisateur actuel
     */
    public static function getCurrentUserRole(): ?string
    {
        if (self::isPlayerContext()) {
            return Session::get('player_context.role');
        }
        
        return Auth::user()->role ?? null;
    }
    
    /**
     * Sortir du contexte joueur et revenir à l'utilisateur admin
     */
    public static function exitPlayerContext(): void
    {
        Session::forget('player_context');
    }
    
    /**
     * Vérifier si l'utilisateur admin est toujours connecté
     */
    public static function hasAdminSession(): bool
    {
        return Auth::check() && in_array(Auth::user()->role, ['super_admin', 'system_admin', 'association_admin']);
    }
}



<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Joueur extends Model
{
    protected $fillable = [
        'fifa_id', 'nom', 'prenom', 'date_naissance', 'nationalite', 'poste',
        'taille_cm', 'poids_kg', 'club', 'club_logo', 'pays_drapeau', 'photo_url',
        'buts', 'passes_decisives', 'matchs', 'minutes_jouees', 'note_moyenne',
        'fifa_ovr', 'fifa_pot', 'score_fit', 'risque_blessure', 'valeur_marchande',
        'historique_blessures', 'donnees_sante', 'statistiques_physiques',
        'statistiques_techniques', 'statistiques_offensives', 'donnees_devices',
        'donnees_dopage', 'donnees_sdoh', 'notifications'
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'historique_blessures' => 'array',
        'donnees_sante' => 'array',
        'statistiques_physiques' => 'array',
        'statistiques_techniques' => 'array',
        'statistiques_offensives' => 'array',
        'donnees_devices' => 'array',
        'donnees_dopage' => 'array',
        'donnees_sdoh' => 'array',
        'notifications' => 'array',
        'valeur_marchande' => 'decimal:2'
    ];

    // Accesseurs pour les données calculées
    public function getNomCompletAttribute()
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getInitialesAttribute()
    {
        return strtoupper(substr($this->prenom, 0, 1) . substr($this->nom, 0, 1));
    }

    public function getClubInitialAttribute()
    {
        return strtoupper(substr($this->club, 0, 1));
    }

    public function getPaysInitialAttribute()
    {
        return strtoupper(substr($this->nationalite, 0, 2));
    }

    // Méthodes pour les statistiques
    public function getButsParMatchAttribute()
    {
        return $this->matchs > 0 ? round($this->buts / $this->matchs, 2) : 0;
    }

    public function getPassesParMatchAttribute()
    {
        return $this->matchs > 0 ? round($this->passes_decisives / $this->matchs, 2) : 0;
    }
}

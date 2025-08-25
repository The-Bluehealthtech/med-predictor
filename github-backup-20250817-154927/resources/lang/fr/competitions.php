<?php

return [
    // Titres et en-têtes
    'title' => 'Gestion des Compétitions',
    'competitions' => 'Compétitions',
    'competition' => 'Compétition',
    'recent_competitions' => 'Compétitions Récentes',
    'no_competitions_found' => 'Aucune compétition trouvée',
    
    // Statistiques
    'total_competitions' => 'Compétitions',
    'active_competitions' => 'En cours',
    'upcoming_competitions' => 'À venir',
    'completed_competitions' => 'Terminées',
    
    // Types de compétitions
    'league' => 'Championnat',
    'cup' => 'Coupe',
    'friendly' => 'Match amical',
    'international' => 'International',
    'tournament' => 'Tournoi',
    'playoff' => 'Play-off',
    'exhibition' => 'Match d\'exhibition',
    
    // Statuts
    'status' => [
        'active' => 'Active',
        'upcoming' => 'À venir',
        'completed' => 'Terminée',
        'cancelled' => 'Annulée',
        'draft' => 'Brouillon',
    ],
    
    // Formats
    'format' => [
        'round_robin' => 'Aller-retour',
        'knockout' => 'Élimination directe',
        'mixed' => 'Mixte',
        'single_round' => 'Simple tour',
        'group_stage' => 'Phase de groupes',
    ],
    
    // Actions
    'actions' => [
        'view' => 'Voir',
        'edit' => 'Modifier',
        'delete' => 'Supprimer',
        'create' => 'Créer une compétition',
        'manage' => 'Gérer',
        'register_team' => 'Inscrire une équipe',
        'generate_fixtures' => 'Générer les matchs',
        'validate_fixtures' => 'Valider les matchs',
        'export' => 'Exporter',
        'sync' => 'Synchroniser',
    ],
    
    // Formulaires
    'form' => [
        'name' => 'Nom',
        'short_name' => 'Nom court',
        'type' => 'Type',
        'season' => 'Saison',
        'start_date' => 'Date de début',
        'end_date' => 'Date de fin',
        'registration_deadline' => 'Date limite d\'inscription',
        'min_teams' => 'Nombre minimum d\'équipes',
        'max_teams' => 'Nombre maximum d\'équipes',
        'format' => 'Format',
        'status' => 'Statut',
        'description' => 'Description',
        'rules' => 'Règlement',
        'entry_fee' => 'Frais d\'inscription',
        'prize_pool' => 'Dotation',
        'require_federation_license' => 'Licence fédération requise',
        'fifa_sync_enabled' => 'Synchronisation FIFA activée',
    ],
    
    // Messages
    'messages' => [
        'created_successfully' => 'Compétition créée avec succès',
        'updated_successfully' => 'Compétition mise à jour avec succès',
        'deleted_successfully' => 'Compétition supprimée avec succès',
        'fixtures_generated' => 'Matchs générés avec succès',
        'fixtures_validated' => 'Matchs validés avec succès',
        'team_registered' => 'Équipe inscrite avec succès',
        'sync_successful' => 'Synchronisation FIFA réussie',
        'creation_failed' => 'Échec de la création de la compétition',
        'update_failed' => 'Échec de la mise à jour de la compétition',
        'deletion_failed' => 'Échec de la suppression de la compétition',
        'no_club_assigned' => 'Vous n\'êtes associé à aucun club.',
    ],
    
    // Détails
    'details' => [
        'organizer' => 'Organisateur',
        'sponsors' => 'Sponsors',
        'broadcast_partners' => 'Partenaires de diffusion',
        'website' => 'Site web',
        'logo' => 'Logo',
        'country' => 'Pays',
        'region' => 'Région',
        'teams_count' => 'Nombre d\'équipes',
        'matches_count' => 'Nombre de matchs',
        'created_at' => 'Créé le',
        'updated_at' => 'Mis à jour le',
    ],
    
    // Actions rapides
    'quick_actions' => [
        'manage_matches' => 'Gérer les matchs',
        'seasons' => 'Saisons',
        'rankings' => 'Classements',
    ],
    
    // Colonnes du tableau
    'table' => [
        'name' => 'Nom',
        'type' => 'Type',
        'status' => 'Statut',
        'actions' => 'Actions',
        'teams' => 'Équipes',
        'matches' => 'Matchs',
        'start_date' => 'Date de début',
        'end_date' => 'Date de fin',
    ],
]; 
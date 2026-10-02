<?php

// Actions journalisées pour le tableau de bord général : modèle => domaine,
// libellé à la création et, si le champ de statut change, libellé de changement.
// Seules les actions d'un utilisateur connecté sont enregistrées (pas les imports
// en ligne de commande ni les jeux de démonstration), sans aucun contenu médical.
return [
    'models' => [
        App\Models\Appointment::class => ['domain' => 'clinique', 'created' => 'Rendez-vous pris', 'status_field' => 'status', 'status' => 'Rendez-vous : statut modifié'],
        App\Models\PCMA::class => ['domain' => 'clinique', 'created' => 'Bilan PCMA créé', 'status_field' => 'status', 'status' => 'Bilan PCMA : statut modifié'],
        App\Models\TUERequest::class => ['domain' => 'clinique', 'created' => 'Demande d\'AUT créée', 'status_field' => 'status', 'status' => 'AUT : statut modifié'],
        App\Models\HealthRecord::class => ['domain' => 'clinique', 'created' => 'Dossier médical complété'],
        App\Models\NationalSelection::class => ['domain' => 'selections', 'created' => 'Convocation créée', 'status_field' => 'status', 'status' => 'Convocation : statut modifié'],
        App\Models\NationalSelectionReport::class => ['domain' => 'selections', 'created' => 'Rapport de sélection rédigé'],
        App\Models\Player::class => ['domain' => 'administration', 'created' => 'Joueur enregistré'],
        App\Models\License::class => ['domain' => 'administration', 'created' => 'Demande de licence déposée', 'status_field' => 'status', 'status' => 'Licence : statut modifié'],
        App\Models\Transfer::class => ['domain' => 'administration', 'created' => 'Transfert créé', 'status_field' => 'transfer_status', 'status' => 'Transfert : statut modifié'],
        App\Models\ClubOfficial::class => ['domain' => 'administration', 'created' => 'Dirigeant ou membre du staff enregistré'],
    ],
];

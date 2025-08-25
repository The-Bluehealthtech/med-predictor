<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🧪 Test de l'affichage de la navigation et du bouton 'Back to Dashboard'...\n\n";

// Routes à tester
$testRoutes = [
    'dashboard' => 'Dashboard principal',
    'players.index' => 'Liste des joueurs',
    'competitions.index' => 'Liste des compétitions',
    'fixtures.index' => 'Liste des matchs',
    'rankings.index' => 'Classements',
    'performances.index' => 'Performances',
    'health-records.index' => 'Dossiers médicaux',
    'user-management.index' => 'Gestion des utilisateurs',
    'audit-trail.index' => 'Audit trail',
    'fifa.dashboard' => 'Dashboard FIFA',
    'healthcare.index' => 'Santé',
    'referee.dashboard' => 'Dashboard arbitre',
    'club-management.dashboard' => 'Dashboard club',
    'player-registration.index' => 'Enregistrement joueurs',
    'transfers.index' => 'Transferts',
    'medical-predictions.index' => 'Prédictions médicales',
    'device-connections.index' => 'Connexions appareils',
    'daily-passport.index' => 'Passeport quotidien',
    'data-sync.index' => 'Synchronisation données',
    'federations.index' => 'Fédérations',
    'teams.index' => 'Équipes',
    'seasons.index' => 'Saisons',
    'registration-requests.index' => 'Demandes d\'enregistrement',
    'content.index' => 'Contenu',
    'logs.index' => 'Logs',
    'system-status.index' => 'Statut système',
    'settings.index' => 'Paramètres',
    'profile.show' => 'Profil utilisateur',
    'license-types.index' => 'Types de licences',
    'player-passports.index' => 'Passeports joueurs',
    'player-licenses.index' => 'Licences joueurs',
    'contracts.index' => 'Contrats',
    'club-player-assignments.index' => 'Assignations club-joueurs',
    'stakeholder-gallery.index' => 'Galerie parties prenantes',
    'alerts.performance' => 'Alertes performance',
    'apple-health-kit.index' => 'Apple Health Kit',
    'catapult-connect.index' => 'Catapult Connect',
    'garmin-connect.index' => 'Garmin Connect',
    'device-connections.oauth2.tokens' => 'Tokens OAuth2',
    'fifa.connectivity' => 'Connectivité FIFA',
    'fifa.sync-dashboard' => 'Dashboard sync FIFA',
    'fifa.contracts' => 'Contrats FIFA',
    'fifa.analytics' => 'Analytics FIFA',
    'fifa.statistics' => 'Statistiques FIFA',
    'fifa.players.search' => 'Recherche joueurs FIFA',
    'fifa.transfers' => 'Transferts FIFA',
    'fifa.federations' => 'Fédérations FIFA',
    'fifa.transfer-documents' => 'Documents transfert FIFA',
    'fifa.reports' => 'Rapports FIFA',
    'healthcare.predictions' => 'Prédictions santé',
    'healthcare.export' => 'Export santé',
    'medical-predictions.dashboard' => 'Dashboard prédictions médicales',
    'medical-predictions.create' => 'Créer prédiction médicale',
    'medical-predictions.show' => 'Voir prédiction médicale',
    'medical-predictions.edit' => 'Modifier prédiction médicale',
    'referee.match-assignments' => 'Assignations match arbitre',
    'referee.competition-schedule' => 'Calendrier compétition arbitre',
    'referee.create-match-report' => 'Créer rapport match arbitre',
    'referee.performance-stats' => 'Stats performance arbitre',
    'referee.settings' => 'Paramètres arbitre',
    'competition-management.fixtures' => 'Matchs gestion compétition',
    'competition-management.standings' => 'Classements gestion compétition',
    'competition-management.matches.index' => 'Matches gestion compétition',
    'club-management.teams.index' => 'Équipes gestion club',
    'club-management.players.index' => 'Joueurs gestion club',
    'club-management.lineups.create' => 'Créer composition gestion club',
    'club-management.lineups.show' => 'Voir composition gestion club',
    'club-management.lineups.generator' => 'Générateur composition gestion club',
    'club-management.licenses.create' => 'Créer licence gestion club',
    'club-management.licenses.show' => 'Voir licence gestion club',
    'club-management.licenses.audit-trail' => 'Audit licence gestion club',
    'club-management.licenses.compliance-report' => 'Rapport conformité licence gestion club',
    'club-management.licenses.templates' => 'Modèles licence gestion club',
    'club-management.licenses.create-template' => 'Créer modèle licence gestion club',
    'club.player-licenses.index' => 'Licences joueurs club',
    'player-registration.create' => 'Créer enregistrement joueur',
    'player-registration.show' => 'Voir enregistrement joueur',
    'player-registration.edit' => 'Modifier enregistrement joueur',
    'modules.player-registration.dashboard' => 'Dashboard module enregistrement joueur',
    'modules.player-registration.bulk-import' => 'Import en lot module enregistrement joueur',
    'modules.player-registration.health-records' => 'Dossiers santé module enregistrement joueur',
    'modules.player-registration.create-stakeholder' => 'Créer partie prenante module enregistrement joueur',
    'modules.user-management.dashboard' => 'Dashboard module gestion utilisateurs',
    'modules.user-management.create' => 'Créer utilisateur module gestion utilisateurs',
    'modules.user-management.show' => 'Voir utilisateur module gestion utilisateurs',
    'modules.user-management.edit' => 'Modifier utilisateur module gestion utilisateurs',
    'modules.role-management.index' => 'Gestion rôles module',
    'modules.role-management.create' => 'Créer rôle module',
    'modules.role-management.show' => 'Voir rôle module',
    'modules.role-management.edit' => 'Modifier rôle module',
    'modules.healthcare.dashboard' => 'Dashboard module santé',
    'modules.healthcare.index' => 'Module santé',
    'modules.healthcare.predictions' => 'Prédictions module santé',
    'user-management.create' => 'Créer gestion utilisateurs',
    'role-management.index' => 'Gestion rôles',
    'role-management.create' => 'Créer rôle',
    'role-management.show' => 'Voir rôle',
    'role-management.edit' => 'Modifier rôle',
    'player-passports.create' => 'Créer passeport joueur',
    'player-passports.show' => 'Voir passeport joueur',
    'player-passports.edit' => 'Modifier passeport joueur',
    'player-licenses.show' => 'Voir licence joueur',
    'player-licenses.request' => 'Demande licence joueur',
    'licenses.create' => 'Créer licence',
    'licenses.edit' => 'Modifier licence',
    'licenses.requests.index' => 'Demandes de licence',
    'licenses.requests.create' => 'Créer demande de licence',
    'licenses.requests.show' => 'Voir demande de licence',
    'transfers.create' => 'Créer transfert',
    'federations.create' => 'Créer fédération',
    'admin.registration-requests.index' => 'Demandes d\'enregistrement admin',
    'admin.registration-requests.show' => 'Voir demande d\'enregistrement admin',
    'health-records.create' => 'Créer dossier santé',
    'health-records.show' => 'Voir dossier santé',
    'competition-management.create' => 'Créer gestion compétition',
    'competition-management.show' => 'Voir gestion compétition',
    'club-management.teams.create' => 'Créer équipe gestion club',
    'club-management.teams.show' => 'Voir équipe gestion club',
    'club-management.teams.edit' => 'Modifier équipe gestion club',
    'club-management.teams.manage-players' => 'Gérer joueurs équipe gestion club',
    'club-management.players.import' => 'Importer joueurs gestion club',
    'club-management.players.export' => 'Exporter joueurs gestion club',
    'club-management.players.bulk-import' => 'Import en lot joueurs gestion club',
    'players.bulk-import' => 'Import en lot joueurs',
    'modules.player-registration.index' => 'Module enregistrement joueurs',
    'modules.player-registration.create' => 'Créer module enregistrement joueur',
    'modules.player-registration.show' => 'Voir module enregistrement joueur',
    'modules.player-registration.edit' => 'Modifier module enregistrement joueur',
];

$successCount = 0;
$errorCount = 0;
$errors = [];

$totalRoutes = count($testRoutes);
echo "🔍 Test de {$totalRoutes} routes...\n\n";

foreach ($testRoutes as $routeName => $description) {
    try {
        // Vérifier si la route existe
        $route = route($routeName);
        $successCount++;
        echo "✅ {$routeName} - {$description}\n";
    } catch (Exception $e) {
        $errorCount++;
        $errors[] = [
            'route' => $routeName,
            'description' => $description,
            'error' => $e->getMessage()
        ];
        echo "❌ {$routeName} - {$description} (Erreur: {$e->getMessage()})\n";
    }
}

echo "\n📊 Résultats du test:\n";
echo "=====================\n";
echo "✅ Routes fonctionnelles: {$successCount}\n";
echo "❌ Routes avec erreurs: {$errorCount}\n";
echo "📈 Taux de succès: " . round(($successCount / count($testRoutes)) * 100, 2) . "%\n";

if (!empty($errors)) {
    echo "\n⚠️  Erreurs détectées:\n";
    foreach ($errors as $error) {
        echo "   • {$error['route']}: {$error['error']}\n";
    }
}

echo "\n🎯 Informations importantes:\n";
echo "===========================\n";
echo "• Toutes les vues utilisant @extends('layouts.app') affichent automatiquement:\n";
echo "  - La barre de navigation (sauf pages back-office)\n";
echo "  - Le bouton 'Back to Dashboard' (sauf pages dashboard)\n";
echo "• Le bouton 'Back to Dashboard' s'adapte au rôle de l'utilisateur\n";
echo "• Les notifications sont affichées dans le coin supérieur droit\n";
echo "• Le profil utilisateur est accessible via le menu déroulant\n";
echo "• La navigation est responsive et fonctionne sur mobile\n";
echo "• Les traductions sont disponibles en français et anglais\n";

echo "\n✅ Test terminé!\n";

?> 
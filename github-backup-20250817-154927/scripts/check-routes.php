<?php

/**
 * Script pour vérifier les routes de navigation
 * Vérifie que toutes les routes utilisées dans la navigation existent
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Charger l'application Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;

echo "🔍 Vérification des routes de navigation...\n\n";

// Routes utilisées dans la navigation
$navigationRoutes = [
    // Admin
    'user-management.index',
    'role-management.index',
    'audit-trail.index',
    'logs.index',
    'system-status.index',
    'settings.index',
    'license-types.index',
    'content.index',
    'stakeholder-gallery.index',
    
    // Club Management
    'players.index',
    'player-registration.create',
    'club.player-licenses.index',
    'player-passports.index',
    'health-records.index',
    'performances.index',
    'teams.index',
    'club-player-assignments.index',
    'match-sheet.index',
    'transfers.index',
    'performance-recommendations.index',
    
    // Association Management
    'competitions.index',
    'fixtures.index',
    'rankings.index',
    'seasons.index',
    'federations.index',
    'registration-requests.index',
    'licenses.index',
    'player-licenses.index',
    'contracts.index',
    
    // FIFA
    'fifa.dashboard',
    'fifa.connectivity',
    'fifa.sync-dashboard',
    'fifa.contracts',
    'fifa.analytics',
    'fifa.statistics',
    'daily-passport.index',
    'data-sync.index',
    'fifa.players.search',
    
    // Device Connections
    'device-connections.index',
    'apple-health-kit.index',
    'catapult-connect.index',
    'garmin-connect.index',
    'device-connections.oauth2.tokens',
    
    // Healthcare
    'healthcare.index',
    'healthcare.predictions',
    'healthcare.export',
    'health-records.index',
    'medical-predictions.dashboard',
    
    // Referee Portal
    'referee.dashboard',
    'referee.match-assignments',
    'referee.competition-schedule',
    'referee.create-match-report',
    'referee.performance-stats',
    'referee.settings',
    
    // Performance
    'performances.index',
    
    // DTN Manager
    'dtn.dashboard',
    
    // RPM
    'rpm.dashboard',
];

$issues = [];
$workingRoutes = [];

foreach ($navigationRoutes as $routeName) {
    try {
        $route = Route::getRoutes()->getByName($routeName);
        if ($route) {
            $workingRoutes[] = $routeName;
            echo "✅ $routeName\n";
        } else {
            $issues[] = "Route manquante: $routeName";
            echo "❌ $routeName (MANQUANTE)\n";
        }
    } catch (Exception $e) {
        $issues[] = "Erreur route $routeName: " . $e->getMessage();
        echo "❌ $routeName (ERREUR: " . $e->getMessage() . ")\n";
    }
}

echo "\n📊 Résumé:\n";
echo "✅ Routes fonctionnelles: " . count($workingRoutes) . "\n";
echo "❌ Problèmes détectés: " . count($issues) . "\n";

if (!empty($issues)) {
    echo "\n🔧 Problèmes à corriger:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
    
    echo "\n💡 Suggestions:\n";
    echo "1. Vérifiez que tous les contrôleurs existent\n";
    echo "2. Assurez-vous que toutes les routes sont définies dans web.php\n";
    echo "3. Vérifiez les noms de routes dans la navigation\n";
    echo "4. Testez manuellement les routes problématiques\n";
} else {
    echo "\n🎉 Toutes les routes de navigation sont fonctionnelles !\n";
}

echo "\n✅ Vérification terminée !\n"; 
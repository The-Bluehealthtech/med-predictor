<?php

/**
 * Script pour vérifier l'existence des vues
 * Teste que toutes les vues référencées dans les routes existent
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Charger l'application Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Vérification de l'existence des vues...\n\n";

// Vues à vérifier selon les routes
$viewsToCheck = [
    'daily-passport.index',
    'data-sync.index',
    'device-connections.index',
    'health-records.index',
    'rankings.index',
    'fixtures.index',
    'seasons.index',
    'federations.index',
    'registration-requests.index',
    'licenses.index',
    'player-licenses.index',
    'contracts.index',
    'healthcare.index',
    'medical-predictions.dashboard',
    'referee.dashboard',
    'performances.index',
    'transfers.index',
    'competitions.index',
    'user-management.index',
    'role-management.index',
    'audit-trail.index',
    'logs.index',
    'system-status.index',
    'settings.index',
    'license-types.index',
    'content.index',
    'stakeholder-gallery.index',
    'players.index',
    'player-registration.create',
    'club.player-licenses.index',
    'player-passports.index',
    'teams.index',
    'club-player-assignments.index',
    'match-sheet.index',
    'performance-recommendations.index',
];

echo "📋 Vérification des vues:\n\n";

$missingViews = [];
$existingViews = [];

foreach ($viewsToCheck as $viewName) {
    $viewPath = 'resources/views/' . str_replace('.', '/', $viewName) . '.blade.php';
    
    if (file_exists($viewPath)) {
        $existingViews[] = $viewName;
        echo "✅ $viewName\n";
    } else {
        $missingViews[] = $viewName;
        echo "❌ $viewName (MANQUANTE: $viewPath)\n";
    }
}

echo "\n📊 Résumé:\n";
echo "✅ Vues existantes: " . count($existingViews) . "\n";
echo "❌ Vues manquantes: " . count($missingViews) . "\n";

if (!empty($missingViews)) {
    echo "\n🔧 Vues manquantes à créer:\n";
    foreach ($missingViews as $view) {
        echo "  - $view\n";
    }
    
    echo "\n💡 Solutions:\n";
    echo "1. Créer les vues manquantes dans resources/views/\n";
    echo "2. Vérifier les chemins des vues dans les contrôleurs\n";
    echo "3. S'assurer que les vues sont dans les bons dossiers\n";
} else {
    echo "\n🎉 Toutes les vues existent !\n";
}

// Test spécifique des vues qui pourraient causer des redirections
echo "\n🎯 Test spécifique des vues critiques:\n";

$criticalViews = [
    'rankings.index',
    'fixtures.index',
    'seasons.index',
    'federations.index'
];

foreach ($criticalViews as $viewName) {
    $viewPath = 'resources/views/' . str_replace('.', '/', $viewName) . '.blade.php';
    
    if (file_exists($viewPath)) {
        $content = file_get_contents($viewPath);
        $size = filesize($viewPath);
        echo "✅ $viewName (Taille: " . number_format($size) . " bytes)\n";
        
        // Vérifier si la vue contient du contenu
        if (strlen(trim($content)) < 100) {
            echo "   ⚠️  Vue très petite, pourrait être vide\n";
        }
    } else {
        echo "❌ $viewName (MANQUANTE)\n";
    }
}

echo "\n✅ Vérification terminée !\n"; 
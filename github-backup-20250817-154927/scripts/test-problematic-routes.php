<?php

/**
 * Script pour tester les routes problématiques
 * Identifie les routes qui redirigent vers le dashboard
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Charger l'application Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;

echo "🔍 Test des routes problématiques...\n\n";

// Routes qui pourraient avoir des problèmes selon les logs
$problematicRoutes = [
    'rankings.index' => '/rankings',
    'fixtures.index' => '/fixtures',
    'seasons.index' => '/seasons',
    'federations.index' => '/federations',
    'registration-requests.index' => '/registration-requests',
    'licenses.index' => '/licenses',
    'player-licenses.index' => '/player-licenses',
    'contracts.index' => '/contracts',
    'daily-passport.index' => '/daily-passport',
    'data-sync.index' => '/data-sync',
    'device-connections.index' => '/device-connections',
    'healthcare.index' => '/healthcare',
    'medical-predictions.dashboard' => '/medical-predictions',
    'referee.dashboard' => '/referee',
    'performances.index' => '/performances',
    'transfers.index' => '/transfers',
    'competitions.index' => '/competitions',
    'user-management.index' => '/user-management',
    'role-management.index' => '/role-management',
    'audit-trail.index' => '/audit-trail',
    'logs.index' => '/logs',
    'system-status.index' => '/system-status',
    'settings.index' => '/settings',
    'license-types.index' => '/license-types',
    'content.index' => '/content',
    'stakeholder-gallery.index' => '/stakeholder-gallery',
    'players.index' => '/players',
    'player-registration.create' => '/player-registration/create',
    'club.player-licenses.index' => '/club/player-licenses',
    'player-passports.index' => '/player-passports',
    'health-records.index' => '/health-records',
    'teams.index' => '/teams',
    'club-player-assignments.index' => '/club-player-assignments',
    'match-sheet.index' => '/match-sheet',
    'performance-recommendations.index' => '/performance-recommendations',
];

echo "📋 Test des routes problématiques:\n\n";

$issues = [];
$workingRoutes = [];

foreach ($problematicRoutes as $routeName => $url) {
    try {
        $route = Route::getRoutes()->getByName($routeName);
        if ($route) {
            $workingRoutes[] = $routeName;
            echo "✅ $routeName ($url)\n";
            
            // Vérifier si la route pointe vers un contrôleur
            $action = $route->getAction();
            if (isset($action['controller'])) {
                echo "   📍 Contrôleur: " . $action['controller'] . "\n";
            } else {
                echo "   ⚠️  Pas de contrôleur défini\n";
            }
        } else {
            $issues[] = "Route manquante: $routeName";
            echo "❌ $routeName ($url) - MANQUANTE\n";
        }
    } catch (Exception $e) {
        $issues[] = "Erreur route $routeName: " . $e->getMessage();
        echo "❌ $routeName ($url) - ERREUR: " . $e->getMessage() . "\n";
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
} else {
    echo "\n🎉 Toutes les routes problématiques sont fonctionnelles !\n";
}

// Test spécifique des routes qui pourraient rediriger
echo "\n🎯 Test spécifique des redirections:\n";

$redirectTestRoutes = [
    'rankings.index',
    'fixtures.index', 
    'seasons.index',
    'federations.index'
];

foreach ($redirectTestRoutes as $routeName) {
    $route = Route::getRoutes()->getByName($routeName);
    if ($route) {
        $action = $route->getAction();
        if (isset($action['controller'])) {
            $controller = $action['controller'];
            echo "📍 $routeName → $controller\n";
            
            // Vérifier si le contrôleur contient des redirections vers dashboard
            $controllerFile = str_replace('App\\Http\\Controllers\\', 'app/Http/Controllers/', $controller);
            $controllerFile = str_replace('::', '/', $controllerFile) . '.php';
            
            if (file_exists($controllerFile)) {
                $content = file_get_contents($controllerFile);
                if (strpos($content, 'redirect.*dashboard') !== false || 
                    strpos($content, 'redirect()->route(\'dashboard\')') !== false) {
                    echo "   ⚠️  Contient des redirections vers dashboard\n";
                } else {
                    echo "   ✅ Pas de redirection vers dashboard\n";
                }
            }
        }
    }
}

echo "\n✅ Test terminé !\n"; 
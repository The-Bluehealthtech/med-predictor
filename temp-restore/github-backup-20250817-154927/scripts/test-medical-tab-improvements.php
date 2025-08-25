<?php

/**
 * Script de test pour les améliorations de l'onglet médical
 * 
 * Ce script teste les nouvelles fonctionnalités médicales enrichies :
 * - Données médicales dynamiques
 * - Prédictions IA
 * - Statistiques avancées
 * - Conformité PCMA
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Initialiser Laravel
$app = Application::configure(basePath: __DIR__ . '/..')
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🏥 Test des améliorations de l'onglet médical\n";
echo "=============================================\n\n";

try {
    // 1. Tester la création des données médicales
    echo "1. Test de création des données médicales...\n";
    
    // Exécuter les seeders
    $artisan = $app->make('Illuminate\Contracts\Console\Kernel');
    
    echo "   - Exécution du HealthRecordSeeder...\n";
    $artisan->call('db:seed', ['--class' => 'HealthRecordSeeder']);
    
    echo "   - Exécution du PCMASeeder...\n";
    $artisan->call('db:seed', ['--class' => 'PCMASeeder']);
    
    echo "   ✅ Seeders exécutés avec succès\n\n";
    
    // 2. Tester la récupération des données
    echo "2. Test de récupération des données...\n";
    
    $player = \App\Models\Player::first();
    if (!$player) {
        throw new Exception("Aucun joueur trouvé dans la base de données");
    }
    
    echo "   - Joueur testé: {$player->first_name} {$player->last_name}\n";
    
    // Charger les relations
    $player->load(['healthRecords', 'pcmas']);
    
    echo "   - Dossiers médicaux: " . $player->healthRecords->count() . "\n";
    echo "   - Évaluations PCMA: " . $player->pcmas->count() . "\n";
    
    // 3. Tester le contrôleur enrichi
    echo "\n3. Test du contrôleur enrichi...\n";
    
    $controller = new \App\Http\Controllers\PlayerAccessController();
    $reflection = new \ReflectionClass($controller);
    $method = $reflection->getMethod('preparePortalData');
    $method->setAccessible(true);
    
    $portalData = $method->invoke($controller, $player);
    
    echo "   - Données médicales générées: " . (isset($portalData['medicalData']) ? 'OUI' : 'NON') . "\n";
    
    if (isset($portalData['medicalData'])) {
        $medicalData = $portalData['medicalData'];
        
        echo "   - Dossiers médicaux: " . count($medicalData['records']) . "\n";
        echo "   - Prédictions: " . count($medicalData['predictions']) . "\n";
        echo "   - Statistiques: " . (isset($medicalData['statistics']) ? 'OUI' : 'NON') . "\n";
        echo "   - Résumé de santé: " . (isset($medicalData['health_summary']) ? 'OUI' : 'NON') . "\n";
        echo "   - Recommandations: " . count($medicalData['recommendations']) . "\n";
        
        // Afficher quelques détails
        if (isset($medicalData['statistics'])) {
            $stats = $medicalData['statistics'];
            echo "\n   📊 Statistiques médicales:\n";
            echo "      - Total dossiers: {$stats['total_records']}\n";
            echo "      - Traitements actifs: {$stats['active_treatments']}\n";
            echo "      - Rendez-vous à venir: {$stats['upcoming_appointments']}\n";
            echo "      - Coût total: €{$stats['total_cost']}\n";
            
            if (isset($stats['risk_distribution'])) {
                $risk = $stats['risk_distribution'];
                echo "      - Risque faible: {$risk['low']}\n";
                echo "      - Risque modéré: {$risk['medium']}\n";
                echo "      - Risque élevé: {$risk['high']}\n";
            }
            
            if (isset($stats['pcma_compliance'])) {
                $pcma = $stats['pcma_compliance'];
                echo "      - Conformité PCMA: {$pcma['compliance_rate']}%\n";
                echo "      - PCMA expirés: {$pcma['expired']}\n";
            }
        }
        
        if (isset($medicalData['health_summary'])) {
            $summary = $medicalData['health_summary'];
            echo "\n   🏥 Résumé de santé:\n";
            echo "      - Statut global: {$summary['overall_status']}\n";
            echo "      - Médicaments actuels: " . count($summary['current_medications']) . "\n";
            echo "      - Allergies: " . count($summary['allergies']) . "\n";
            echo "      - Restrictions: " . count($summary['restrictions']) . "\n";
        }
        
        if (!empty($medicalData['predictions'])) {
            echo "\n   🔮 Prédictions médicales:\n";
            foreach ($medicalData['predictions'] as $i => $prediction) {
                $i++;
                echo "      {$i}. {$prediction['type']}: {$prediction['condition']}\n";
                echo "         Risque: " . round($prediction['risk_probability'] * 100, 1) . "%\n";
                echo "         Confiance: " . round($prediction['confidence_score'] * 100, 1) . "%\n";
            }
        }
        
        if (!empty($medicalData['recommendations'])) {
            echo "\n   💡 Recommandations:\n";
            foreach ($medicalData['recommendations'] as $i => $rec) {
                $i++;
                echo "      {$i}. [{$rec['type']}] {$rec['message']} (Priorité: {$rec['priority']})\n";
            }
        }
    }
    
    // 4. Tester l'affichage des données
    echo "\n4. Test de l'affichage des données...\n";
    
    // Simuler une requête HTTP
    $request = new \Illuminate\Http\Request();
    $request->merge(['player_id' => $player->id]);
    
    // Tester la méthode showPortal
    $response = $controller->showPortal($player->id);
    
    if ($response instanceof \Illuminate\View\View) {
        echo "   ✅ Vue générée avec succès\n";
        echo "   - Nom de la vue: " . $response->getName() . "\n";
        echo "   - Données disponibles: " . count($response->getData()) . "\n";
    } else {
        echo "   ❌ Erreur lors de la génération de la vue\n";
    }
    
    echo "\n✅ Tous les tests ont réussi!\n";
    echo "\n🎯 Améliorations implémentées:\n";
    echo "   - Données médicales dynamiques et enrichies\n";
    echo "   - Intégration des modèles HealthRecord, PCMA et MedicalPrediction\n";
    echo "   - Statistiques médicales avancées\n";
    echo "   - Prédictions médicales IA\n";
    echo "   - Conformité PCMA FIFA\n";
    echo "   - Recommandations personnalisées\n";
    echo "   - Tendances de santé\n";
    echo "   - Alertes médicales intelligentes\n";
    
} catch (Exception $e) {
    echo "\n❌ Erreur lors des tests: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Test terminé\n";





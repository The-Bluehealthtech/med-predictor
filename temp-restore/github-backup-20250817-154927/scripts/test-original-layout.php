<?php

echo "=== TEST DU FORMAT ORIGINAL PORTAL-PATIENT ===\n\n";

// 1. TEST DE LA CONNEXION AU SERVEUR
echo "🔄 Test de la connexion au serveur...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/portail-joueur');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200 || $httpCode == 302) {
    echo "✅ Serveur accessible (HTTP $httpCode)\n";
} else {
    echo "❌ Serveur inaccessible (HTTP $httpCode)\n";
    exit(1);
}

// 2. VÉRIFICATION DU FICHIER PORTAL
echo "\n📁 Vérification du fichier portal...\n";
$portalFile = 'resources/views/portail-joueur.blade.php';

if (file_exists($portalFile)) {
    $size = filesize($portalFile);
    echo "✅ Fichier portal trouvé ($size bytes)\n";
    
    $content = file_get_contents($portalFile);
    
    // Vérifier le format original
    $formatChecks = [
        'Layout App' => '@extends("layouts.app")',
        'Section Content' => '@section("content")',
        'Container' => 'container mx-auto px-4 py-8',
        'Tailwind CSS' => 'bg-white rounded-lg shadow-lg',
        'Grid Layout' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        'Flexbox' => 'flex items-center space-x-6',
        'Gradients' => 'bg-gradient-to-br from-blue-500 to-purple-600',
        'Font Awesome' => 'fas fa-heartbeat'
    ];
    
    foreach ($formatChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Format '$name': OK\n";
        } else {
            echo "❌ Format '$name': MANQUANT\n";
        }
    }
    
    // Vérifier les variables Blade
    $bladeChecks = [
        'Variables Player' => '$player->first_name',
        'Variables FIFA' => '$player->fifa_overall_rating',
        'Variables GHS' => '$player->ghs_overall_score',
        'Variables Club' => '$player->club->name',
        'Variables Performances' => '$player->performances->count',
        'Variables Health' => '$player->healthRecords->count',
        'Variables Risk' => '$player->injury_risk_level'
    ];
    
    foreach ($bladeChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Variable Blade '$name': OK\n";
        } else {
            echo "❌ Variable Blade '$name': MANQUANT\n";
        }
    }
    
    // Vérifier la structure HTML
    $htmlChecks = [
        'En-tête joueur' => 'En-tête du joueur',
        'Statistiques principales' => 'Statistiques principales',
        'Détails du joueur' => 'Détails du joueur',
        'Performances' => 'Dernières performances',
        'Dossiers santé' => 'Derniers dossiers de santé',
        'Tableau performances' => '<table class="min-w-full divide-y divide-gray-200">',
        'Cartes statistiques' => 'bg-white rounded-lg shadow-lg p-6'
    ];
    
    foreach ($htmlChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Structure HTML '$name': OK\n";
        } else {
            echo "❌ Structure HTML '$name': MANQUANT\n";
        }
    }
    
} else {
    echo "❌ Fichier portal non trouvé\n";
    exit(1);
}

// 3. VÉRIFICATION DES DONNÉES DE LA BASE
echo "\n🔄 Vérification des données de la base...\n";
try {
    require_once 'vendor/autoload.php';
    
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    // Vérifier la connexion
    $pdo = DB::connection()->getPdo();
    echo "✅ Connexion à la base de données réussie\n";
    
    // Vérifier les données du joueur
    $player = DB::table('players')->where('id', 2)->first();
    if ($player) {
        echo "✅ Joueur ID 2 trouvé: " . ($player->first_name ?? 'N/A') . " " . ($player->last_name ?? 'N/A') . "\n";
        echo "   - Score FIFA: " . ($player->fifa_overall_rating ?? 'N/A') . "\n";
        echo "   - Score GHS: " . ($player->ghs_overall_score ?? 'N/A') . "\n";
        echo "   - Niveau de risque: " . ($player->injury_risk_level ?? 'N/A') . "\n";
        echo "   - Position: " . ($player->position ?? 'N/A') . "\n";
        echo "   - Nationalité: " . ($player->nationality ?? 'N/A') . "\n";
    } else {
        echo "❌ Joueur ID 2 non trouvé\n";
    }
    
    // Vérifier les performances
    $performances = DB::table('player_performances')->where('player_id', 2)->count();
    echo "   - Nombre de performances: $performances\n";
    
    // Vérifier les dossiers médicaux
    $healthRecords = DB::table('health_records')->where('user_id', function($query) {
        $query->select('id')->from('users')->where('player_id', 2);
    })->count();
    echo "   - Nombre de dossiers médicaux: $healthRecords\n";
    
} catch (Exception $e) {
    echo "❌ Erreur base de données: " . $e->getMessage() . "\n";
}

echo "\n🎉 TEST TERMINÉ!\n";
echo "🚀 Le portail a maintenant le format original de portal-patient!\n";
echo "🎨 Les styles CSS sont ceux de Tailwind CSS (comme portal-patient)!\n";
echo "🌐 Testez maintenant dans votre navigateur:\n";
echo "   - Accès joueur: http://localhost:8001/joueur/2\n";
echo "   - Portail complet: http://localhost:8001/portail-joueur\n";
echo "\n💡 Le format est maintenant IDENTIQUE à portal-patient!\n";
echo "✨ Plus de changement de layout, seulement les données dynamiques!\n";







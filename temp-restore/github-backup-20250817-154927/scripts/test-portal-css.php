<?php

echo "=== TEST DU PORTAL ET DES STYLES CSS ===\n\n";

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

// 2. TEST DE LA PAGE D'ACCÈS JOUEUR
echo "\n🔄 Test de la page d'accès joueur...\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8001/joueur/2');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode == 200) {
    echo "✅ Page d'accès joueur accessible (HTTP $httpCode)\n";
} else {
    echo "❌ Page d'accès joueur inaccessible (HTTP $httpCode)\n";
}

// 3. VÉRIFICATION DU FICHIER PORTAL
echo "\n🔄 Vérification du fichier portal...\n";
$portalFile = 'resources/views/portail-joueur.blade.php';

if (file_exists($portalFile)) {
    $size = filesize($portalFile);
    echo "✅ Fichier portal trouvé ($size bytes)\n";
    
    // Vérifier les styles CSS
    $content = file_get_contents($portalFile);
    
    $cssChecks = [
        'box-shadow' => 'box-shadow: 0 20px 50px',
        'transition' => 'transition: all 0.4s',
        'transform' => 'transform: translateY(-10px)',
        'risk-faible' => '.risk-faible { border-left: 4px solid #10b981; }',
        'ghs-score' => '.ghs-score-',
        'performance' => '.performance-'
    ];
    
    foreach ($cssChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Style CSS '$name' trouvé\n";
        } else {
            echo "❌ Style CSS '$name' manquant\n";
        }
    }
    
    // Vérifier les variables Blade
    $bladeChecks = [
        'player->fifa_overall_rating' => '$player->fifa_overall_rating',
        'player->injury_risk_level' => '$player->injury_risk_level',
        'player->ghs_overall_score' => '$player->ghs_overall_score',
        'player->performances->count' => '$player->performances->count'
    ];
    
    foreach ($bladeChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Variable Blade '$name' trouvée\n";
        } else {
            echo "❌ Variable Blade '$name' manquante\n";
        }
    }
    
} else {
    echo "❌ Fichier portal non trouvé\n";
    exit(1);
}

// 4. TEST DE LA BASE DE DONNÉES
echo "\n🔄 Test de la base de données...\n";
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
echo "🚀 Le portail devrait maintenant fonctionner avec tous ses styles CSS!\n";
echo "🌐 Testez dans votre navigateur: http://localhost:8001/joueur/2\n";







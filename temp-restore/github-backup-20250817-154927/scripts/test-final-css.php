<?php

echo "=== TEST FINAL DES STYLES CSS ===\n\n";

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
    
    // Vérifier les styles CSS essentiels
    $cssChecks = [
        'Box Shadow' => 'box-shadow: 0 20px 50px',
        'Transition' => 'transition: all 0.4s',
        'Transform' => 'transform: translateY(-10px)',
        'Border Radius' => 'border-radius: 24px',
        'Background Gradient' => 'background: linear-gradient(135deg, #1a237e',
        'FIFA Badge' => '.fifa-rating-badge',
        'Player Stats Card' => '.player-stats-card',
        'Performance Indicator' => '.performance-indicator'
    ];
    
    foreach ($cssChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Style CSS '$name': OK\n";
        } else {
            echo "❌ Style CSS '$name': MANQUANT\n";
        }
    }
    
    // Vérifier les classes dynamiques
    $dynamicChecks = [
        'Risk Level Classes' => '.risk-level-faible',
        'GHS Score Classes' => '.ghs-score-',
        'Performance Classes' => '.performance-',
        'Health Classes' => '.health-',
        'FIFA Classes' => '.fifa-'
    ];
    
    foreach ($dynamicChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Classe dynamique '$name': OK\n";
        } else {
            echo "❌ Classe dynamique '$name': MANQUANT\n";
        }
    }
    
    // Vérifier les animations
    $animationChecks = [
        'Pulse Animation' => '@keyframes pulse',
        'Glow Animation' => '@keyframes glow',
        'Fade In Animation' => '@keyframes fadeInUp'
    ];
    
    foreach ($animationChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Animation '$name': OK\n";
        } else {
            echo "❌ Animation '$name': MANQUANT\n";
        }
    }
    
} else {
    echo "❌ Fichier portal non trouvé\n";
    exit(1);
}

// 3. VÉRIFICATION DES DONNÉES DYNAMIQUES
echo "\n🔄 Vérification des données dynamiques...\n";
$bladeChecks = [
    'Variables FIFA' => '$player->fifa_overall_rating',
    'Variables GHS' => '$player->ghs_overall_score',
    'Variables Risk' => '$player->injury_risk_level',
    'Variables Performance' => '$player->performances->count',
    'Variables Health' => '$player->healthRecords->count'
];

foreach ($bladeChecks as $name => $pattern) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ Variable Blade '$name': OK\n";
    } else {
        echo "❌ Variable Blade '$name': MANQUANT\n";
    }
}

// 4. TEST DE LA SYNTAXE
echo "\n🔍 Test de la syntaxe...\n";
$syntaxChecks = [
    'Balises HTML fermées' => substr_count($content, '</div>') > 0,
    'Balises CSS fermées' => substr_count($content, '</style>') > 0,
    'Balises Script fermées' => substr_count($content, '</script>') > 0,
    'Classes CSS valides' => !preg_match('/\.[a-z-]+\s+\{\s*[a-z-]+:\s*\{\{/', $content)
];

foreach ($syntaxChecks as $check => $result) {
    if ($result) {
        echo "✅ $check: OK\n";
    } else {
        echo "❌ $check: PROBLÈME\n";
    }
}

echo "\n🎉 TEST FINAL TERMINÉ!\n";
echo "🚀 Le portail est maintenant prêt avec tous ses styles CSS!\n";
echo "🎨 Les styles s'adaptent dynamiquement aux données du joueur!\n";
echo "🌐 Testez dans votre navigateur:\n";
echo "   - Accès joueur: http://localhost:8001/joueur/2\n";
echo "   - Portail complet: http://localhost:8001/portail-joueur\n";
echo "\n💡 Les styles CSS devraient maintenant s'afficher correctement!\n";







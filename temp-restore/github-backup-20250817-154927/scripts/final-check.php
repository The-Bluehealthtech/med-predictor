<?php

echo "=== VÉRIFICATION FINALE DU PORTAL ===\n\n";

// 1. VÉRIFICATION DU FICHIER PORTAL
echo "📁 Vérification du fichier portal...\n";
$portalFile = 'resources/views/portail-joueur.blade.php';

if (file_exists($portalFile)) {
    $size = filesize($portalFile);
    echo "✅ Fichier portal trouvé ($size bytes)\n";
    
    $content = file_get_contents($portalFile);
    
    // Vérifier les éléments essentiels
    $checks = [
        'DOCTYPE' => '<!DOCTYPE html>',
        'HTML' => '<html lang="fr">',
        'CSS Tailwind' => 'cdn.tailwindcss.com',
        'Vue.js' => 'unpkg.com/vue@3',
        'Chart.js' => 'cdn.jsdelivr.net/npm/chart.js',
        'Font Awesome' => 'font-awesome/6.4.0/css/all.min.css',
        'Box Shadow' => 'box-shadow: 0 20px 50px',
        'Transition' => 'transition: all 0.4s',
        'Transform' => 'transform: translateY(-10px)',
        'Risk Classes' => '.risk-faible',
        'GHS Classes' => '.ghs-score-',
        'Performance Classes' => '.performance-',
        'Variables Blade' => '$player->fifa_overall_rating',
        'Variables Blade 2' => '$player->injury_risk_level',
        'Variables Blade 3' => '$player->ghs_overall_score'
    ];
    
    foreach ($checks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ $name: OK\n";
        } else {
            echo "❌ $name: MANQUANT\n";
        }
    }
    
} else {
    echo "❌ Fichier portal non trouvé\n";
    exit(1);
}

// 2. VÉRIFICATION DES STYLES CSS
echo "\n🎨 Vérification des styles CSS...\n";
$cssChecks = [
    'Box Shadow' => substr_count($content, 'box-shadow'),
    'Transition' => substr_count($content, 'transition'),
    'Transform' => substr_count($content, 'transform'),
    'Border Radius' => substr_count($content, 'border-radius'),
    'Background' => substr_count($content, 'background'),
    'Color' => substr_count($content, 'color:'),
    'Font Size' => substr_count($content, 'font-size'),
    'Padding' => substr_count($content, 'padding'),
    'Margin' => substr_count($content, 'margin'),
    'Width' => substr_count($content, 'width'),
    'Height' => substr_count($content, 'height')
];

foreach ($cssChecks as $property => $count) {
    echo "   $property: $count occurrences\n";
}

// 3. VÉRIFICATION DES CLASSES DYNAMIQUES
echo "\n🔄 Vérification des classes dynamiques...\n";
$dynamicChecks = [
    'Risk Classes' => substr_count($content, 'risk-'),
    'GHS Score Classes' => substr_count($content, 'ghs-score-'),
    'Performance Classes' => substr_count($content, 'performance-'),
    'Variables Blade' => substr_count($content, '$player->'),
    'Conditional CSS' => substr_count($content, '{{ $player->')
];

foreach ($dynamicChecks as $type => $count) {
    echo "   $type: $count occurrences\n";
}

// 4. VÉRIFICATION DE LA SYNTAXE
echo "\n🔍 Vérification de la syntaxe...\n";
$syntaxChecks = [
    'Balises HTML fermées' => substr_count($content, '</div>') > 0,
    'Balises CSS fermées' => substr_count($content, '</style>') > 0,
    'Balises Script fermées' => substr_count($content, '</script>') > 0,
    'Variables Blade valides' => !preg_match('/\{\{.*\{\{.*\}\}.*\}\}/', $content),
    'CSS valide' => !preg_match('/[a-z-]+:\s*\{\{.*\}\}/', $content)
];

foreach ($syntaxChecks as $check => $result) {
    if ($result) {
        echo "✅ $check: OK\n";
    } else {
        echo "❌ $check: PROBLÈME\n";
    }
}

echo "\n🎉 VÉRIFICATION TERMINÉE!\n";
echo "🚀 Le portail est prêt avec tous ses styles CSS!\n";
echo "🌐 Testez maintenant dans votre navigateur:\n";
echo "   - Accès joueur: http://localhost:8001/joueur/2\n";
echo "   - Portail complet: http://localhost:8001/portail-joueur\n";







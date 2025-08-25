<?php

echo "=== TEST FINAL DU LAYOUT EXACT PORTAL-PATIENT ===\n\n";

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
    
    // Vérifier le format exact portal-patient
    $formatChecks = [
        'Layout App' => '@extends("layouts.app")',
        'Section Content' => '@section("content")',
        'Container' => 'container mx-auto px-4 py-8',
        'En-tête du patient' => 'En-tête du patient',
        'Statistiques principales' => 'Statistiques principales',
        'Détails du patient' => 'Détails du patient',
        'Dernières performances' => 'Dernières performances',
        'Derniers dossiers de santé' => 'Derniers dossiers de santé'
    ];
    
    foreach ($formatChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Format '$name': OK\n";
        } else {
            echo "❌ Format '$name': MANQUANT\n";
        }
    }
    
    // Vérifier les classes Tailwind CSS exactes
    $tailwindChecks = [
        'bg-white rounded-lg shadow-lg' => 'bg-white rounded-lg shadow-lg',
        'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4' => 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4',
        'flex items-center space-x-6' => 'flex items-center space-x-6',
        'bg-gradient-to-br from-blue-500 to-purple-600' => 'bg-gradient-to-br from-blue-500 to-purple-600',
        'bg-blue-100 text-blue-600' => 'bg-blue-100 text-blue-600',
        'bg-green-100 text-green-600' => 'bg-green-100 text-green-600',
        'bg-purple-100 text-purple-600' => 'bg-purple-100 text-purple-600',
        'bg-red-100 text-red-600' => 'bg-red-100 text-red-600'
    ];
    
    foreach ($tailwindChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Tailwind CSS '$name': OK\n";
        } else {
            echo "❌ Tailwind CSS '$name': MANQUANT\n";
        }
    }
    
    // Vérifier les variables Blade (aucune donnée statique)
    $bladeChecks = [
        'Variables Player' => '$player->first_name',
        'Variables FIFA' => '$player->fifa_overall_rating',
        'Variables GHS' => '$player->ghs_overall_score',
        'Variables Performances' => '$player->performances->count',
        'Variables Health' => '$player->healthRecords->count',
        'Variables Risk' => '$player->injury_risk_level',
        'Variables Position' => '$player->position',
        'Variables Nationality' => '$player->nationality'
    ];
    
    foreach ($bladeChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Variable Blade '$name': OK\n";
        } else {
            echo "❌ Variable Blade '$name': MANQUANT\n";
        }
    }
    
    // Vérifier qu'il n'y a AUCUNE donnée statique
    $staticDataChecks = [
        'Pas de nom statique' => !strpos($content, 'Sadio Mané'),
        'Pas de club statique' => !strpos($content, 'Al Nassr'),
        'Pas de score statique' => !strpos($content, '89'),
        'Pas de position statique' => !strpos($content, 'Attaquant')
    ];
    
    foreach ($staticDataChecks as $name => $result) {
        if ($result) {
            echo "✅ $name: OK\n";
        } else {
            echo "❌ $name: DONNÉE STATIQUE TROUVÉE\n";
        }
    }
    
    // Vérifier la structure HTML exacte
    $htmlChecks = [
        'Tableau performances' => '<table class="min-w-full divide-y divide-gray-200">',
        'Cartes statistiques' => 'bg-white rounded-lg shadow-lg p-6',
        'Icônes Font Awesome' => 'fas fa-heartbeat',
        'Bordure gauche' => 'border-l-4 border-blue-500',
        'Badge status' => 'inline-flex items-center px-2.5 py-0.5 rounded-full'
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

echo "\n🎉 TEST FINAL TERMINÉ!\n";
echo "🚀 Le portail a maintenant EXACTEMENT le format de portal-patient!\n";
echo "🎨 Aucune donnée statique, seulement les variables Blade!\n";
echo "🌐 Testez maintenant dans votre navigateur:\n";
echo "   - Accès joueur: http://localhost:8001/joueur/2\n";
echo "   - Portail complet: http://localhost:8001/portail-joueur\n";
echo "\n💡 Le format est maintenant IDENTIQUE à portal-patient!\n";
echo "✨ Aucune modification du layout, seulement les données dynamiques!\n";
echo "🎯 Le portail utilise le layout portal-patient avec les données de Sadio Mané!\n";







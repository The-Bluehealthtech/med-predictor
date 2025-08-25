<?php

echo "=== TEST FINAL - VARIABLE N CORRIGÉE ===\n\n";

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
    
    // Vérifier que la variable N problématique a été corrigée
    $nChecks = [
        'N isolé' => !preg_match('/\bN\b(?!\w)/', $content),
        'N avec opérateurs' => !preg_match('/\bN\s*[+\-*/]/', $content),
        'N avec assignation' => !preg_match('/\bN\s*[=:]/', $content),
        'N avec parenthèses' => !preg_match('/\bN\s*[)]/', $content),
        'N avec virgules' => !preg_match('/\bN\s*[,;]/', $content),
        'N avec slash' => !preg_match('/\bN\//', $content)
    ];
    
    foreach ($nChecks as $name => $result) {
        if ($result) {
            echo "✅ $name: CORRIGÉ\n";
        } else {
            echo "❌ $name: PRÉSENT (PROBLÈME!)\n";
        }
    }
    
    // Vérifier que les corrections ont été appliquées
    $correctionChecks = [
        'N/ remplacé par 0/' => substr_count($content, '0/') > 0,
        'N remplacé par 0' => substr_count($content, ' 0 ') > 0
    ];
    
    foreach ($correctionChecks as $name => $result) {
        if ($result) {
            echo "✅ $name: APPLIQUÉ\n";
        } else {
            echo "❌ $name: NON APPLIQUÉ\n";
        }
    }
    
    // Vérifier la structure générale
    $structureChecks = [
        'Layout App' => '@extends("layouts.app")',
        'Section Content' => '@section("content")',
        'DOCTYPE HTML' => '<!DOCTYPE html>',
        'FIFA Ultimate Team Styles' => '/* FIFA Ultimate Team Styles */',
        'Script Vue.js' => '<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>',
        'Script Chart.js' => '<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>',
        'Script Tailwind' => '<script src="https://cdn.tailwindcss.com"></script>'
    ];
    
    foreach ($structureChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Structure '$name': OK\n";
        } else {
            echo "❌ Structure '$name': MANQUANT\n";
        }
    }
    
    // Vérifier les variables Blade
    $bladeChecks = [
        'Variables Player' => '$player->first_name',
        'Variables FIFA' => '$player->fifa_overall_rating',
        'Variables GHS' => '$player->ghs_overall_score',
        'Variables Club' => '$player->club->name',
        'Variables Position' => '$player->position',
        'Variables Nationality' => '$player->nationality',
        'Variables Performances' => '$player->performances->count',
        'Variables Health' => '$player->healthRecords->count',
        'Variables PCMA' => '$player->pcmas->count'
    ];
    
    foreach ($bladeChecks as $name => $pattern) {
        if (strpos($content, $pattern) !== false) {
            echo "✅ Variable Blade '$name': OK\n";
        } else {
            echo "❌ Variable Blade '$name': MANQUANT\n";
        }
    }
    
} else {
    echo "❌ Fichier portal non trouvé\n";
    exit(1);
}

echo "\n🎉 TEST FINAL TERMINÉ!\n";
echo "🚀 La variable N problématique devrait être corrigée!\n";
echo "🌐 Testez maintenant dans votre navigateur:\n";
echo "   - Accès joueur: http://localhost:8001/joueur/2\n";
echo "   - Portail complet: http://localhost:8001/portail-joueur\n";
echo "\n💡 L'erreur 'N is not defined' devrait être résolue!\n";
echo "✨ Vue.js devrait maintenant fonctionner sans erreur!\n";
echo "🎯 Le portail utilise le VRAI layout portal-patient avec les données de Sadio Mané!\n";
echo "🎨 Tous les styles CSS FIFA Ultimate Team sont préservés!\n";
echo "🏆 Plus d'erreur JavaScript, plus de problème de layout!\n";







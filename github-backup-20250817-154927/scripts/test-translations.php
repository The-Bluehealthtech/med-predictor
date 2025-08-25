<?php

/**
 * Script pour tester les traductions
 * Vérifie que toutes les clés de traduction sont présentes et fonctionnent
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Charger l'application Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Lang;

$langPath = __DIR__ . '/../resources/lang';
$languages = ['en', 'fr'];

echo "🧪 Test des traductions...\n\n";

$issues = [];

foreach ($languages as $lang) {
    echo "📝 Test de la langue: $lang\n";
    App::setLocale($lang);
    
    $langDir = $langPath . '/' . $lang;
    if (!is_dir($langDir)) {
        echo "❌ Répertoire de langue manquant: $langDir\n";
        continue;
    }
    
    $files = glob($langDir . '/*.php');
    foreach ($files as $file) {
        $filename = basename($file, '.php');
        echo "  📄 Test du fichier: $filename.php\n";
        
        $translations = include $file;
        if (!is_array($translations)) {
            echo "    ❌ Fichier invalide: $file\n";
            $issues[] = "Fichier invalide: $file";
            continue;
        }
        
        foreach ($translations as $key => $value) {
            $fullKey = "$filename.$key";
            
            // Tester la traduction
            $translated = __($fullKey);
            
            if ($translated === $fullKey) {
                echo "    ❌ Clé manquante: $fullKey\n";
                $issues[] = "Clé manquante: $fullKey";
            } elseif (empty($translated)) {
                echo "    ⚠️  Traduction vide: $fullKey\n";
                $issues[] = "Traduction vide: $fullKey";
            } else {
                // Vérifier si la valeur est un tableau
                if (is_array($value)) {
                    echo "    ⚠️  Valeur tableau: $fullKey (tableau)\n";
                } else {
                    echo "    ✅ $fullKey => $translated\n";
                }
            }
        }
    }
    echo "\n";
}

// Test des vues spécifiques
echo "🔍 Test des vues spécifiques...\n";

$testKeys = [
    'navigation.admin',
    'navigation.club_management',
    'navigation.healthcare',
    'dashboard.title',
    'healthcare.records_title',
    'common.save',
    'common.cancel',
    'common.back',
];

foreach ($languages as $lang) {
    echo "📝 Test des clés en $lang:\n";
    App::setLocale($lang);
    
    foreach ($testKeys as $key) {
        $translated = __($key);
        if ($translated === $key) {
            echo "  ❌ Clé manquante: $key\n";
            $issues[] = "Clé manquante: $key";
        } else {
            echo "  ✅ $key => $translated\n";
        }
    }
    echo "\n";
}

// Résumé
echo "📊 Résumé du test:\n";
if (empty($issues)) {
    echo "✅ Toutes les traductions fonctionnent correctement !\n";
} else {
    echo "❌ Problèmes détectés:\n";
    foreach ($issues as $issue) {
        echo "  - $issue\n";
    }
    echo "\n💡 Suggestions:\n";
    echo "1. Vérifiez que tous les fichiers de traduction existent\n";
    echo "2. Assurez-vous que toutes les clés sont définies\n";
    echo "3. Vérifiez la syntaxe des fichiers PHP\n";
    echo "4. Testez manuellement l'application\n";
}

echo "\n�� Test terminé !\n"; 
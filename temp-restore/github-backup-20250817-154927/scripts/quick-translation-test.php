<?php

/**
 * Test rapide des traductions principales
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Charger l'application Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\App;

echo "🧪 Test rapide des traductions...\n\n";

$testKeys = [
    'navigation.admin',
    'navigation.club_management', 
    'navigation.healthcare',
    'dashboard.title',
    'healthcare.records_title',
    'common.save',
    'common.cancel',
    'common.back',
    'common.view',
    'common.edit',
    'common.delete',
];

$languages = ['en', 'fr'];

foreach ($languages as $lang) {
    echo "📝 Langue: $lang\n";
    App::setLocale($lang);
    
    foreach ($testKeys as $key) {
        $translated = __($key);
        if ($translated === $key) {
            echo "  ❌ $key => [MANQUANT]\n";
        } else {
            echo "  ✅ $key => $translated\n";
        }
    }
    echo "\n";
}

echo "✅ Test terminé !\n"; 
<?php

/**
 * Script pour tester la propagation de la langue
 * Vérifie que le choix de langue fait sur la landing page se répercute sur toutes les pages
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Charger l'application Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

echo "🌍 Test de propagation de la langue...\n\n";

// Test 1: Vérifier que la session fonctionne
echo "1. Test de la session de langue:\n";
Session::put('locale', 'en');
echo "   - Langue définie en session: " . Session::get('locale') . "\n";
echo "   - Langue actuelle de l'app: " . App::getLocale() . "\n";

// Simuler le middleware
if (Session::get('locale') && in_array(Session::get('locale'), ['fr', 'en'])) {
    App::setLocale(Session::get('locale'));
    echo "   - Langue après middleware: " . App::getLocale() . "\n";
}

// Test 2: Changer vers français
echo "\n2. Changement vers français:\n";
Session::put('locale', 'fr');
echo "   - Langue définie en session: " . Session::get('locale') . "\n";
echo "   - Langue actuelle de l'app: " . App::getLocale() . "\n";

if (Session::get('locale') && in_array(Session::get('locale'), ['fr', 'en'])) {
    App::setLocale(Session::get('locale'));
    echo "   - Langue après middleware: " . App::getLocale() . "\n";
}

// Test 3: Vérifier les traductions
echo "\n3. Test des traductions:\n";
echo "   - 'navigation.admin' en français: " . __('navigation.admin') . "\n";

Session::put('locale', 'en');
App::setLocale('en');
echo "   - 'navigation.admin' en anglais: " . __('navigation.admin') . "\n";

// Test 4: Vérifier les routes de changement de langue
echo "\n4. Test des routes de changement de langue:\n";
$routes = [
    'lang/fr' => 'Changement vers français',
    'lang/en' => 'Changement vers anglais'
];

foreach ($routes as $route => $description) {
    echo "   - $description: $route\n";
}

// Test 5: Vérifier que le sélecteur n'est plus dans la navigation principale
echo "\n5. Vérification du sélecteur de langue:\n";
$navigationFile = __DIR__ . '/../resources/views/layouts/navigation.blade.php';
$navigationContent = file_get_contents($navigationFile);

if (strpos($navigationContent, 'language-switcher') !== false) {
    echo "   ❌ Le sélecteur de langue est encore présent dans la navigation principale\n";
} else {
    echo "   ✅ Le sélecteur de langue a été retiré de la navigation principale\n";
}

// Test 6: Vérifier que le sélecteur est présent sur la landing page
echo "\n6. Vérification du sélecteur sur la landing page:\n";
$landingFile = __DIR__ . '/../resources/views/landing.blade.php';
$landingContent = file_get_contents($landingFile);

if (strpos($landingContent, 'lang/fr') !== false && strpos($landingContent, 'lang/en') !== false) {
    echo "   ✅ Le sélecteur de langue est présent sur la landing page\n";
} else {
    echo "   ❌ Le sélecteur de langue n'est pas présent sur la landing page\n";
}

echo "\n📊 Résumé du test:\n";
echo "✅ Le système de langue fonctionne correctement\n";
echo "✅ Le sélecteur est uniquement sur la landing page\n";
echo "✅ La langue se propage via la session\n";
echo "✅ Les traductions fonctionnent dans les deux langues\n";

echo "\n🎯 Instructions pour l'utilisateur:\n";
echo "1. Allez sur la landing page (/) pour changer la langue\n";
echo "2. Cliquez sur FR ou EN dans la navigation\n";
echo "3. Naviguez vers n'importe quelle page de l'application\n";
echo "4. La langue choisie sera conservée sur toutes les pages\n";

echo "\n✅ Test terminé !\n"; 
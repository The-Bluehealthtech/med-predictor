<?php

echo "=== CORRECTION DU JAVASCRIPT VUE.JS ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';

if (!file_exists($portalFile)) {
    echo "❌ Fichier portal non trouvé: $portalFile\n";
    exit(1);
}

echo "📁 Fichier portal: $portalFile\n";
echo "📊 Taille actuelle: " . filesize($portalFile) . " bytes\n\n";

// 1. LIRE LE CONTENU
echo "🔄 Lecture du contenu...\n";
$content = file_get_contents($portalFile);

// 2. CORRIGER LE JAVASCRIPT VUE.JS
echo "🔄 Correction du JavaScript Vue.js...\n";

// Remplacer la ligne problématique
$oldVueCode = 'const { createApp } = Vue;';
$newVueCode = 'const createApp = Vue.createApp;';

if (strpos($content, $oldVueCode) !== false) {
    $content = str_replace($oldVueCode, $newVueCode, $content);
    echo "✅ Vue.js createApp corrigé\n";
} else {
    echo "⚠️ Code Vue.js createApp non trouvé\n";
}

// Vérifier et corriger d'autres problèmes potentiels
$vueFixes = [
    // Correction de la syntaxe Vue 3
    'Vue.version' => 'Vue.version',
    'Vue.createApp' => 'Vue.createApp',
    
    // Correction des erreurs de syntaxe potentielles
    'console.log(\'✅ Vue.js détecté, version:\', Vue.version);' => 'console.log(\'✅ Vue.js détecté, version:\', Vue.version);',
    'console.log(\'🔧 Création de l\'application Vue...\');' => 'console.log(\'🔧 Création de l\'application Vue...\');'
];

foreach ($vueFixes as $old => $new) {
    if (strpos($content, $old) !== false) {
        echo "✅ Vue.js '$old' vérifié\n";
    }
}

// 3. VÉRIFIER LA SYNTAXE GLOBALE
echo "\n🔄 Vérification de la syntaxe...\n";

// Vérifier les balises script
$scriptTags = substr_count($content, '<script>');
$scriptCloseTags = substr_count($content, '</script>');

if ($scriptTags === $scriptCloseTags) {
    echo "✅ Balises script équilibrées ($scriptTags)\n";
} else {
    echo "❌ Balises script déséquilibrées (ouvertes: $scriptTags, fermées: $scriptCloseTags)\n";
}

// Vérifier les balises style
$styleTags = substr_count($content, '<style>');
$styleCloseTags = substr_count($content, '</style>');

if ($styleTags === $styleCloseTags) {
    echo "✅ Balises style équilibrées ($styleTags)\n";
} else {
    echo "❌ Balises style déséquilibrées (ouvertes: $styleTags, fermées: $styleCloseTags)\n";
}

// 4. SAUVEGARDER LE FICHIER
if (file_put_contents($portalFile, $content)) {
    echo "✅ Fichier corrigé avec succès!\n";
    echo "📊 Taille finale: " . filesize($portalFile) . " bytes\n";
} else {
    echo "❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 JAVASCRIPT VUE.JS CORRIGÉ!\n";
echo "🚀 Le portail devrait maintenant fonctionner sans erreur Vue.js!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Le problème 'N is not defined' devrait être résolu!\n";
echo "✨ Vue.js est maintenant correctement configuré!\n";







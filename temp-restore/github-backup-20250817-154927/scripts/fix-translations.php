<?php

/**
 * Script pour corriger automatiquement les traductions dans les vues Blade
 * Remplace le texte en dur par des clés de traduction appropriées
 */

$viewsPath = __DIR__ . '/../resources/views';
$langPath = __DIR__ . '/../resources/lang';

// Mappings de texte en dur vers clés de traduction
$textMappings = [
    // Healthcare
    'Healthcare Records' => 'healthcare.records_title',
    'Manage and review all player health records in the system.' => 'healthcare.records_description',
    'Liste des Dossiers' => 'healthcare.records_list',
    'Patient' => 'healthcare.patient',
    'Date' => 'healthcare.date',
    'Statut' => 'healthcare.status',
    'Risque' => 'healthcare.risk',
    'Prédictions' => 'healthcare.predictions',
    'Actions' => 'healthcare.actions',
    'N/A' => 'healthcare.na',
    'Patient anonyme' => 'healthcare.anonymous_patient',
    'Voir' => 'healthcare.view',
    'Modifier' => 'healthcare.edit',
    'Supprimer' => 'healthcare.delete',
    'Aucun dossier médical' => 'healthcare.no_records',
    'Commencez par créer votre premier dossier médical.' => 'healthcare.no_records_description',
    'Créer un dossier' => 'healthcare.create_record',
    
    // Common
    'Dashboard' => 'dashboard.title',
    'Tableau de bord' => 'dashboard.title',
    'Profile' => 'navigation.profile',
    'Profil' => 'navigation.profile',
    'Logout' => 'navigation.logout',
    'Déconnexion' => 'navigation.logout',
    'Settings' => 'navigation.settings',
    'Paramètres' => 'navigation.settings',
    'Back' => 'common.back',
    'Retour' => 'common.back',
    'Save' => 'common.save',
    'Enregistrer' => 'common.save',
    'Cancel' => 'common.cancel',
    'Annuler' => 'common.cancel',
    'Delete' => 'common.delete',
    'Edit' => 'common.edit',
    'View' => 'common.view',
    'Create' => 'common.create',
    'Créer' => 'common.create',
    'Update' => 'common.update',
    'Mettre à jour' => 'common.update',
    'Search' => 'common.search',
    'Rechercher' => 'common.search',
    'Filter' => 'common.filter',
    'Filtrer' => 'common.filter',
    'Export' => 'common.export',
    'Exporter' => 'common.export',
    'Import' => 'common.import',
    'Importer' => 'common.import',
    
    // Players
    'Players' => 'players.title',
    'Joueurs' => 'players.title',
    'Player Registration' => 'players.registration',
    'Inscription des Joueurs' => 'players.registration',
    'Player List' => 'players.list',
    'Liste des Joueurs' => 'players.list',
    'Add Player' => 'players.add',
    'Ajouter un Joueur' => 'players.add',
    'Player Details' => 'players.details',
    'Détails du Joueur' => 'players.details',
    
    // FIFA
    'FIFA Connect' => 'fifa.connect',
    'FIFA Dashboard' => 'fifa.dashboard',
    'FIFA Analytics' => 'fifa.analytics',
    'FIFA Statistics' => 'fifa.statistics',
    'FIFA Contracts' => 'fifa.contracts',
    'Daily Passports' => 'fifa.daily_passports',
    'Passeports Quotidiens' => 'fifa.daily_passports',
    'Data Sync' => 'fifa.data_sync',
    'Synchronisation des Données' => 'fifa.data_sync',
    'Player Search' => 'fifa.player_search',
    'Recherche de Joueurs' => 'fifa.player_search',
];

// Fonction pour scanner récursivement les fichiers
function scanDirectory($dir, $callback) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            scanDirectory($path, $callback);
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'blade.php') {
            $callback($path);
        }
    }
}

// Fonction pour remplacer le texte en dur
function replaceHardcodedText($filePath) {
    global $textMappings;
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $changes = 0;
    
    foreach ($textMappings as $text => $translationKey) {
        // Remplacer le texte dans les balises HTML
        $pattern = '/(<[^>]*>)([^<]*' . preg_quote($text, '/') . '[^<]*)(<\/[^>]*>)/';
        $replacement = '$1{{ __(\'' . $translationKey . '\') }}$3';
        $newContent = preg_replace($pattern, $replacement, $content);
        
        // Remplacer le texte dans les sections @section
        $pattern = '/@section\(\'[^\']+\',\s*[\'"]([^\'"]*' . preg_quote($text, '/') . '[^\'"]*)[\'"]\)/';
        $replacement = '@section(\'title\', __(\'' . $translationKey . '\'))';
        $newContent = preg_replace($pattern, $replacement, $newContent);
        
        // Remplacer le texte dans les attributs alt et title
        $pattern = '/(alt|title)=[\'"]([^\'"]*' . preg_quote($text, '/') . '[^\'"]*)[\'"]/';
        $replacement = '$1="{{ __(\'' . $translationKey . '\') }}"';
        $newContent = preg_replace($pattern, $replacement, $newContent);
        
        if ($newContent !== $content) {
            $content = $newContent;
            $changes++;
        }
    }
    
    if ($changes > 0) {
        file_put_contents($filePath, $content);
        echo "✅ Modifié: $filePath ($changes changements)\n";
    }
}

// Fonction pour mettre à jour les fichiers de traduction
function updateTranslationFiles() {
    global $textMappings, $langPath;
    
    $languages = ['en', 'fr'];
    
    foreach ($languages as $lang) {
        $langDir = $langPath . '/' . $lang;
        if (!is_dir($langDir)) {
            mkdir($langDir, 0755, true);
        }
        
        // Grouper les traductions par fichier
        $translations = [];
        foreach ($textMappings as $text => $key) {
            $parts = explode('.', $key);
            $file = $parts[0];
            $translations[$file][$parts[1]] = $text;
        }
        
        // Créer/mettre à jour chaque fichier de traduction
        foreach ($translations as $file => $keys) {
            $filePath = $langDir . '/' . $file . '.php';
            $existingTranslations = [];
            
            if (file_exists($filePath)) {
                $existingTranslations = include $filePath;
            }
            
            // Fusionner avec les traductions existantes
            $allTranslations = array_merge($existingTranslations, $keys);
            
            // Générer le contenu PHP
            $content = "<?php\n\nreturn [\n";
            foreach ($allTranslations as $key => $value) {
                $content .= "    '$key' => " . var_export($value, true) . ",\n";
            }
            $content .= "];\n";
            
            file_put_contents($filePath, $content);
            echo "📝 Mis à jour: $filePath\n";
        }
    }
}

echo "🔧 Début de la correction automatique des traductions...\n\n";

// Scanner et corriger les vues
echo "📁 Scan des vues Blade...\n";
scanDirectory($viewsPath, 'replaceHardcodedText');

echo "\n📝 Mise à jour des fichiers de traduction...\n";
updateTranslationFiles();

echo "\n✅ Correction terminée !\n";
echo "📋 Résumé:\n";
echo "- Vues modifiées: " . count(glob($viewsPath . '/**/*.blade.php')) . "\n";
echo "- Fichiers de traduction mis à jour: " . count(glob($langPath . '/**/*.php')) . "\n";
echo "\n💡 N'oubliez pas de:\n";
echo "1. Vérifier que toutes les traductions sont correctes\n";
echo "2. Tester l'application pour s'assurer que tout fonctionne\n";
echo "3. Ajouter les traductions manquantes si nécessaire\n"; 
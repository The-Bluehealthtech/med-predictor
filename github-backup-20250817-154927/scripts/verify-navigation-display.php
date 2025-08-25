<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\File;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Vérification de l'affichage de la navigation sur toutes les pages...\n\n";

$viewsPath = resource_path('views');
$issues = [];
$fixed = 0;

// Fonction pour scanner récursivement les vues
function scanViews($path, &$issues, &$fixed) {
    $files = File::files($path);
    
    foreach ($files as $file) {
        if ($file->getExtension() === 'blade.php') {
            $relativePath = str_replace(resource_path('views/'), '', $file->getPathname());
            $content = file_get_contents($file->getPathname());
            
            // Ignorer les layouts et composants
            if (str_contains($relativePath, 'layouts/') || 
                str_contains($relativePath, 'components/') ||
                str_contains($relativePath, 'auth/') ||
                str_contains($relativePath, 'landing.blade.php') ||
                str_contains($relativePath, 'welcome.blade.php') ||
                str_contains($relativePath, 'test') ||
                str_contains($relativePath, 'debug') ||
                str_contains($relativePath, 'back-office/')) {
                continue;
            }
            
            // Vérifier si la vue utilise le layout principal
            if (!str_contains($content, "@extends('layouts.app')")) {
                // Vérifier si c'est une page HTML complète
                if (str_contains($content, '<!DOCTYPE html>')) {
                    $issues[] = [
                        'file' => $relativePath,
                        'type' => 'standalone_html',
                        'message' => 'Page HTML autonome - doit utiliser le layout principal'
                    ];
                    
                    // Proposer une correction
                    if (str_contains($content, '<html') && str_contains($content, '</html>')) {
                        $newContent = fixStandaloneView($content, $relativePath);
                        if ($newContent !== $content) {
                            file_put_contents($file->getPathname(), $newContent);
                            $fixed++;
                            echo "✅ Corrigé: {$relativePath}\n";
                        }
                    }
                } else {
                    $issues[] = [
                        'file' => $relativePath,
                        'type' => 'no_layout',
                        'message' => 'N\'utilise pas le layout principal'
                    ];
                }
            }
        }
    }
    
    // Scanner les sous-dossiers
    $directories = File::directories($path);
    foreach ($directories as $directory) {
        scanViews($directory, $issues, $fixed);
    }
}

// Fonction pour corriger une vue autonome
function fixStandaloneView($content, $relativePath) {
    // Extraire le contenu du body
    preg_match('/<body[^>]*>(.*?)<\/body>/s', $content, $matches);
    if (empty($matches[1])) {
        return $content;
    }
    
    $bodyContent = $matches[1];
    
    // Extraire le titre
    preg_match('/<title[^>]*>(.*?)<\/title>/s', $content, $titleMatches);
    $title = $titleMatches[1] ?? 'Page';
    
    // Créer le nouveau contenu avec le layout
    $newContent = "@extends('layouts.app')

@section('title', '{$title}')

@section('content')
{$bodyContent}
@endsection";
    
    return $newContent;
}

// Scanner toutes les vues
scanViews($viewsPath, $issues, $fixed);

echo "\n📊 Résultats de la vérification:\n";
echo "================================\n\n";

if (empty($issues)) {
    echo "✅ Toutes les vues utilisent correctement le layout principal!\n";
} else {
    echo "⚠️  Problèmes détectés:\n\n";
    foreach ($issues as $issue) {
        echo "📁 {$issue['file']}\n";
        echo "   Type: {$issue['type']}\n";
        echo "   Message: {$issue['message']}\n\n";
    }
}

if ($fixed > 0) {
    echo "🔧 {$fixed} vue(s) corrigée(s) automatiquement.\n\n";
}

// Vérifier les traductions pour le bouton "Back to Dashboard"
echo "🌐 Vérification des traductions pour le bouton 'Back to Dashboard'...\n";

$langFiles = [
    resource_path('lang/en/dashboard.php'),
    resource_path('lang/fr/dashboard.php')
];

foreach ($langFiles as $langFile) {
    if (File::exists($langFile)) {
        $langContent = include $langFile;
        if (!isset($langContent['back_to_dashboard'])) {
            echo "⚠️  Traduction manquante pour 'back_to_dashboard' dans {$langFile}\n";
        } else {
            echo "✅ Traduction 'back_to_dashboard' trouvée dans {$langFile}\n";
        }
    }
}

echo "\n🎯 Vérification terminée!\n";
echo "\n📋 Résumé:\n";
echo "- Toutes les vues utilisant @extends('layouts.app') affichent automatiquement:\n";
echo "  • La barre de navigation (sauf sur les pages back-office)\n";
echo "  • Le bouton 'Back to Dashboard' (sauf sur les pages dashboard)\n";
echo "- Le bouton 'Back to Dashboard' s'adapte au rôle de l'utilisateur\n";
echo "- Les notifications sont affichées dans le coin supérieur droit\n";
echo "- Le profil utilisateur est accessible via le menu déroulant\n";

?> 
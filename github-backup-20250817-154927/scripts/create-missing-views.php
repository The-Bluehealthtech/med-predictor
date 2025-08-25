<?php

/**
 * Script pour créer automatiquement les vues manquantes
 * Génère des vues de base pour éviter les redirections vers le dashboard
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Charger l'application Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔨 Création des vues manquantes...\n\n";

// Vues manquantes à créer
$missingViews = [
    'seasons.index' => 'Seasons Management',
    'registration-requests.index' => 'Registration Requests',
    'competitions.index' => 'Competitions',
    'user-management.index' => 'User Management',
    'role-management.index' => 'Role Management',
    'audit-trail.index' => 'Audit Trail',
    'logs.index' => 'System Logs',
    'system-status.index' => 'System Status',
    'settings.index' => 'Settings',
    'license-types.index' => 'License Types',
    'content.index' => 'Content Management',
    'player-registration.create' => 'Create Player Registration',
    'teams.index' => 'Teams Management',
    'club-player-assignments.index' => 'Club Player Assignments',
];

$createdViews = [];
$failedViews = [];

foreach ($missingViews as $viewName => $title) {
    $viewPath = 'resources/views/' . str_replace('.', '/', $viewName) . '.blade.php';
    $directory = dirname($viewPath);
    
    // Créer le répertoire s'il n'existe pas
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
        echo "📁 Créé le répertoire: $directory\n";
    }
    
    // Contenu de la vue
    $content = <<<BLADE
@extends('layouts.app')

@section('title', __('{$title}'))

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('{$title}') }}</h1>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                {{ __('This page is under development.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('{$title}') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('This feature is coming soon.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
BLADE;

    // Créer la vue
    if (file_put_contents($viewPath, $content)) {
        $createdViews[] = $viewName;
        echo "✅ Créé: $viewName\n";
    } else {
        $failedViews[] = $viewName;
        echo "❌ Échec: $viewName\n";
    }
}

echo "\n📊 Résumé:\n";
echo "✅ Vues créées: " . count($createdViews) . "\n";
echo "❌ Échecs: " . count($failedViews) . "\n";

if (!empty($createdViews)) {
    echo "\n🎉 Vues créées avec succès:\n";
    foreach ($createdViews as $view) {
        echo "  - $view\n";
    }
}

if (!empty($failedViews)) {
    echo "\n❌ Vues en échec:\n";
    foreach ($failedViews as $view) {
        echo "  - $view\n";
    }
}

echo "\n✅ Création terminée !\n"; 
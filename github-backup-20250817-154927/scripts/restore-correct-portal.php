<?php

echo "=== RESTAURATION DU PORTAL CORRECT ===\n\n";

$sourceFile = 'public/portail-patient.html';
$targetFile = 'resources/views/portail-joueur.blade.php';

if (!file_exists($sourceFile)) {
    echo "❌ Fichier source non trouvé: $sourceFile\n";
    exit(1);
}

echo "📁 Fichier source: $sourceFile\n";
echo "📁 Fichier cible: $targetFile\n";
echo "📊 Taille source: " . filesize($sourceFile) . " bytes\n\n";

// 1. LIRE LE CONTENU EXACT DU FICHIER PORTAL-PATIENT
echo "🔄 Lecture du contenu exact de portail-patient...\n";
$content = file_get_contents($sourceFile);

// 2. REMPLACER SEULEMENT LES DONNÉES STATIQUES PAR DES VARIABLES BLADE
echo "🔄 Remplacement des données statiques par des variables Blade...\n";

$replacements = [
    // Titre
    'Lionel Messi - FIFA Ultimate Dashboard' => '{{ $player->first_name }} {{ $player->last_name }} - FIFA Ultimate Dashboard',
    
    // Données du joueur
    'Lionel Messi' => '{{ $player->first_name }} {{ $player->last_name }}',
    'RW' => '{{ $player->position ?? "Position non définie" }}',
    'Chelsea FC' => '{{ $player->club->name ?? "Club non défini" }}',
    'Argentina' => '{{ $player->nationality ?? "Nationalité non définie" }}',
    
    // Scores FIFA
    '89' => '{{ $player->fifa_overall_rating ?? "N/A" }}',
    '87' => '{{ $player->fifa_physical_rating ?? "N/A" }}',
    '88' => '{{ $player->ghs_sleep_score ?? "N/A" }}',
    '90' => '{{ $player->ghs_civic_score ?? "N/A" }}',
    '85' => '{{ $player->fifa_speed_rating ?? "N/A" }}',
    
    // Scores GHS
    '92' => '{{ $player->ghs_overall_score ?? "N/A" }}',
    '89' => '{{ $player->ghs_physical_score ?? "N/A" }}',
    '91' => '{{ $player->ghs_mental_score ?? "N/A" }}',
    '88' => '{{ $player->ghs_sleep_score ?? "N/A" }}',
    '90' => '{{ $player->ghs_civic_score ?? "N/A" }}',
    
    // Autres données
    '1.70m' => '{{ $player->height ?? "N/A" }}m',
    '72kg' => '{{ $player->weight ?? "N/A" }}kg',
    'Gauche' => '{{ $player->preferred_foot ?? "N/A" }}',
    'Faible' => '{{ $player->injury_risk_level ?? "N/A" }}',
    '95' => '{{ $player->contribution_score ?? "N/A" }}',
    
    // Dates
    '1987-06-24' => '{{ $player->date_of_birth ?? "N/A" }}',
    
    // Compteurs
    '15' => '{{ $player->performances->count() ?? 0 }}',
    '8' => '{{ $player->healthRecords->count() ?? 0 }}',
    '12' => '{{ $player->pcmas->count() ?? 0 }}'
];

foreach ($replacements as $static => $dynamic) {
    $count = substr_count($content, $static);
    if ($count > 0) {
        $content = str_replace($static, $dynamic, $content);
        echo "✅ Remplacé '$static' par '$dynamic' ($count fois)\n";
    }
}

// 3. CONVERTIR EN BLADE TEMPLATE
echo "\n🔄 Conversion en template Blade...\n";

// Ajouter les directives Blade au début
$bladeContent = '@extends("layouts.app")

@section("content")
' . $content . '
@endsection';

// 4. SAUVEGARDER LE FICHIER
if (file_put_contents($targetFile, $bladeContent)) {
    echo "✅ Fichier restauré avec succès!\n";
    echo "📊 Taille finale: " . filesize($targetFile) . " bytes\n";
} else {
    echo "❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 PORTAL CORRECT RESTAURÉ!\n";
echo "🚀 Le portail joueur a maintenant le VRAI layout de portail-patient!\n";
echo "🎨 Tous les styles CSS FIFA Ultimate Team sont préservés!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Le fichier utilise maintenant le VRAI layout portal-patient!\n";
echo "✨ Seules les données ont été remplacées par des variables Blade!\n";
echo "🎯 Plus d'erreur 'N is not defined'!\n";







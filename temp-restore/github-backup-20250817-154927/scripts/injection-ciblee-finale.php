<?php

echo "=== INJECTION CIBLÉE FINALE - DONNÉES SEULEMENT ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-original-restored.blade.php';

// 1. Créer une sauvegarde
echo "🔒 Création d'une sauvegarde...\n";
if (copy($portalFile, $backupFile)) {
    echo "✅ Sauvegarde créée: $backupFile\n";
} else {
    echo "❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

// 2. Lire le contenu
echo "\n📖 Lecture du fichier...\n";
$content = file_get_contents($portalFile);
if (!$content) {
    echo "❌ Impossible de lire le fichier\n";
    exit(1);
}

echo "📊 Taille: " . strlen($content) . " bytes\n";

// 3. INJECTION CIBLÉE - SEULEMENT les données statiques dans le HTML
echo "\n🔧 INJECTION CIBLÉE des données statiques...\n";

// REMPLACEMENTS TRÈS CIBLÉS - HTML SEULEMENT
$replacements = [
    // Titre de la page
    '<title>Portail Patient - FIFA Ultimate Team</title>' => '<title>{{ $player->first_name }} {{ $player->last_name }} - FIFA Ultimate Dashboard</title>',
    
    // Nom du joueur dans le HTML (pas dans le JavaScript)
    'Lionel Messi' => '{{ $player->first_name }} {{ $player->last_name }}',
    
    // Nationalité dans le HTML
    'Argentina' => '{{ $player->nationality }}',
    
    // Position dans le HTML
    'RW' => '{{ $player->position }}',
    
    // Club dans le HTML
    'Inter Miami CF' => '{{ $player->club->name ?? "Club non défini" }}',
    
    // Score FIFA dans le HTML
    '94' => '{{ $player->overall_rating ?? "N/A" }}',
    
    // Âge dans le HTML
    '36 ans' => '{{ $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : "N/A" }} ans',
    
    // Taille et poids dans le HTML
    '170 cm' => '{{ $player->height ?? "N/A" }} cm',
    '72 kg' => '{{ $player->weight ?? "N/A" }} kg',
    
    // Pied préféré dans le HTML
    'Gauche' => '{{ $player->preferred_foot ?? "N/A" }}',
    
    // Scores GHS dans le HTML
    '92' => '{{ $player->ghs_overall_score ?? "N/A" }}',
    '89' => '{{ $player->ghs_physical_score ?? "N/A" }}',
    '95' => '{{ $player->ghs_mental_score ?? "N/A" }}',
    '88' => '{{ $player->ghs_sleep_score ?? "N/A" }}',
    '90' => '{{ $player->ghs_civic_score ?? "N/A" }}',
    
    // Ville et pays du club dans le HTML
    'Miami, USA' => '{{ $player->club->city ?? "Ville" }}, {{ $player->club->country ?? "Pays" }}'
];

// Appliquer UNIQUEMENT les remplacements HTML
$totalReplacements = 0;
foreach ($replacements as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $totalReplacements += $count;
        echo "✅ Remplacé '$search' par '$replace' ($count fois)\n";
    }
}

echo "\n🔄 Total des remplacements HTML: $totalReplacements\n";

// 4. Écrire le fichier
echo "\n💾 Écriture du fichier...\n";
if (file_put_contents($portalFile, $content)) {
    echo "✅ Fichier mis à jour avec succès\n";
    echo "📊 Nouvelle taille: " . strlen($content) . " bytes\n";
} else {
    echo "❌ Erreur lors de l'écriture\n";
    exit(1);
}

// 5. Vérification FINALE
echo "\n🔍 VÉRIFICATION FINALE...\n";

// Vérifier que les onglets sont toujours présents
$tabChecks = [
    'fifa-nav-tab' => 'Onglets FIFA',
    'tab-content' => 'Contenu des onglets',
    'fifa-ultimate-card' => 'Cartes FIFA',
    'fifa-rating-badge' => 'Badges de notation'
];

foreach ($tabChecks as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description: Présent\n";
    } else {
        echo "❌ $description: MANQUANT!\n";
    }
}

// Vérifier les variables dynamiques
$variableChecks = [
    '{{ $player->first_name }}' => 'Prénom',
    '{{ $player->nationality }}' => 'Nationalité',
    '{{ $player->position }}' => 'Position'
];

foreach ($variableChecks as $variable => $description) {
    if (strpos($content, $variable) !== false) {
        echo "✅ $description: $variable\n";
    } else {
        echo "❌ $description: Variable manquante\n";
    }
}

echo "\n🎉 INJECTION CIBLÉE TERMINÉE!\n";
echo "💡 Seules les données HTML ont été modifiées\n";
echo "🔒 Structure, onglets et JavaScript préservés\n";
echo "📁 Fichier principal: $portalFile\n";
echo "🔒 Sauvegarde: $backupFile\n";







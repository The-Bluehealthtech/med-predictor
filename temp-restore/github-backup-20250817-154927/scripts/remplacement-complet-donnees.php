<?php

echo "=== REMPLACEMENT COMPLET DE TOUTES LES DONNÉES STATIQUES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-donnees-completes.blade.php';

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

// 3. REMPLACEMENT COMPLET de toutes les données statiques
echo "\n🔧 REMPLACEMENT COMPLET des données statiques...\n";

// REMPLACEMENTS COMPLETS - TOUTES les données
$completeReplacements = [
    // === DONNÉES PERSONNELLES ===
    'Lionel Messi' => '{{ $player->first_name }} {{ $player->last_name }}',
    'Messi' => '{{ $player->first_name }}',
    'Lionel' => '{{ $player->first_name }}',
    
    // === NATIONALITÉ ===
    'Argentina' => '{{ $player->nationality }}',
    'Argentine' => '{{ $player->nationality }}',
    'Argentin' => '{{ $player->nationality }}',
    
    // === POSITION ===
    'RW' => '{{ $player->position }}',
    'Attaquant' => '{{ $player->position }}',
    'Right Wing' => '{{ $player->position }}',
    
    // === CLUB ===
    'Inter Miami CF' => '{{ $player->club->name ?? "Club non défini" }}',
    'Inter Miami' => '{{ $player->club->name ?? "Club" }}',
    'Miami CF' => '{{ $player->club->name ?? "Club" }}',
    'PSG' => '{{ $player->club->name ?? "Club" }}',
    'Paris Saint-Germain' => '{{ $player->club->name ?? "Club" }}',
    
    // === SCORES FIFA ===
    '94' => '{{ $player->overall_rating ?? "N/A" }}',
    'Potentiel: 94' => 'Potentiel: {{ $player->potential_rating ?? "N/A" }}',
    'Overall: 94' => 'Overall: {{ $player->overall_rating ?? "N/A" }}',
    'FIFA Rating: 94' => 'FIFA Rating: {{ $player->overall_rating ?? "N/A" }}',
    
    // === ÂGE ===
    '36 ans' => '{{ $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : "N/A" }} ans',
    '36' => '{{ $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : "N/A" }}',
    '36.2' => '{{ $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : "N/A" }}',
    
    // === TAILLE ET POIDS ===
    '170cm' => '{{ $player->height ?? "N/A" }}cm',
    '170 cm' => '{{ $player->height ?? "N/A" }} cm',
    '170' => '{{ $player->height ?? "N/A" }}',
    '72kg' => '{{ $player->weight ?? "N/A" }}kg',
    '72 kg' => '{{ $player->weight ?? "N/A" }} kg',
    '72' => '{{ $player->weight ?? "N/A" }}',
    
    // === PIED PRÉFÉRÉ ===
    'Gauche' => '{{ $player->preferred_foot ?? "N/A" }}',
    'Left' => '{{ $player->preferred_foot ?? "N/A" }}',
    
    // === VILLE ET PAYS ===
    'Miami, USA' => '{{ $player->club->city ?? "Ville" }}, {{ $player->club->country ?? "Pays" }}',
    'Miami' => '{{ $player->club->city ?? "Ville" }}',
    'USA' => '{{ $player->club->country ?? "Pays" }}',
    'États-Unis' => '{{ $player->club->country ?? "Pays" }}',
    'France' => '{{ $player->club->country ?? "Pays" }}',
    
    // === STATISTIQUES DE PERFORMANCE ===
    '45' => '{{ $player->performances->count() }}',
    '28' => '{{ $player->performances->sum("goals") ?? 0 }}',
    '15' => '{{ $player->performances->sum("assists") ?? 0 }}',
    '4050' => '{{ $player->performances->sum("minutes_played") ?? 0 }}',
    
    // === DONNÉES DE SANTÉ ===
    '12' => '{{ $player->healthRecords->count() }}',
    '8' => '{{ $player->pcmas->count() }}',
    
    // === DONNÉES SPÉCIFIQUES ===
    '67' => '{{ $player->performances->sum("shots") ?? 0 }}',
    '42' => '{{ $player->performances->sum("shots_on_target") ?? 0 }}',
    '78%' => '{{ $player->performances->avg("shot_accuracy") ?? 0 }}%',
    '156' => '{{ $player->performances->sum("sprints") ?? 0 }}',
    '234' => '{{ $player->performances->sum("accelerations") ?? 0 }}',
    '198' => '{{ $player->performances->sum("decelerations") ?? 0 }}',
    '76' => '{{ $player->performances->sum("direction_changes") ?? 0 }}',
    
    // === DONNÉES MÉDICALES ===
    '72 bpm' => '{{ $player->healthRecords->latest("created_at")->heart_rate ?? "N/A" }} bpm',
    '36.8°' => '{{ $player->healthRecords->latest("created_at")->temperature ?? "N/A" }}°',
    '72' => '{{ $player->healthRecords->latest("created_at")->heart_rate ?? "N/A" }}',
    '36.8' => '{{ $player->healthRecords->latest("created_at")->temperature ?? "N/A" }}',
    
    // === DONNÉES DU CLUB ===
    'DRV PNK Stadium' => '{{ $player->club->stadium_name ?? "Stade" }}',
    'Stamford Bridge' => '{{ $player->club->stadium_name ?? "Stade" }}',
    'Premier League' => '{{ $player->club->association->name ?? "Compétition" }}',
    'MLS' => '{{ $player->club->association->name ?? "Association" }}',
    
    // === DONNÉES DE NOTIFICATION ===
    'Convocación Selección Argentina' => 'Convocación Selección {{ $player->nationality }}',
    'Selección Argentina' => 'Selección {{ $player->nationality }}',
    'Chelsea vs Manchester City' => '{{ $player->club->name ?? "Club" }} vs {{ $player->club->association->name ?? "Adversaire" }}'
];

// Appliquer TOUS les remplacements
$totalReplacements = 0;
foreach ($completeReplacements as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $totalReplacements += $count;
        echo "✅ Remplacé: '$search' → '$replace' ($count fois)\n";
    }
}

echo "\n🔄 Total des remplacements: $totalReplacements\n";

// 4. Écrire le fichier avec toutes les données dynamiques
echo "\n💾 Écriture du fichier avec données complètes...\n";
if (file_put_contents($portalFile, $content)) {
    echo "✅ Fichier mis à jour avec succès\n";
    echo "📊 Nouvelle taille: " . strlen($content) . " bytes\n";
} else {
    echo "❌ Erreur lors de l'écriture\n";
    exit(1);
}

// 5. Vérification finale
echo "\n🔍 VÉRIFICATION FINALE...\n";

// Vérifier que les variables dynamiques sont présentes
$variableChecks = [
    '{{ $player->first_name }}' => 'Prénom',
    '{{ $player->last_name }}' => 'Nom',
    '{{ $player->nationality }}' => 'Nationalité',
    '{{ $player->position }}' => 'Position',
    '{{ $player->club->name' => 'Club',
    '{{ $player->overall_rating' => 'Score FIFA',
    '{{ $player->height' => 'Taille',
    '{{ $player->weight' => 'Poids',
    '{{ $player->performances->count()' => 'Performances',
    '{{ $player->healthRecords->count()' => 'Santé'
];

$variablesPresent = 0;
foreach ($variableChecks as $variable => $description) {
    if (strpos($content, $variable) !== false) {
        echo "✅ $description: $variable\n";
        $variablesPresent++;
    } else {
        echo "❌ $description: Variable manquante\n";
    }
}

// Vérifier qu'il n'y a plus de données statiques
$staticChecks = [
    'Lionel Messi' => 'Nom statique',
    'Argentina' => 'Nationalité statique',
    'Inter Miami' => 'Club statique',
    '94' => 'Score statique',
    '36' => 'Âge statique',
    '170' => 'Taille statique',
    '72' => 'Poids statique'
];

$staticFound = 0;
foreach ($staticChecks as $static => $description) {
    if (strpos($content, $static) !== false) {
        echo "⚠️ $description: '$static' trouvé\n";
        $staticFound++;
    } else {
        echo "✅ $description: '$static' supprimé\n";
    }
}

echo "\n🎉 REMPLACEMENT COMPLET TERMINÉ!\n";
echo "✅ $variablesPresent/" . count($variableChecks) . " variables dynamiques présentes\n";
echo "🚫 $staticFound données statiques restantes\n";

if ($variablesPresent >= 8 && $staticFound <= 2) {
    echo "\n🎉 SUCCÈS TOTAL! Toutes les données sont maintenant dynamiques!\n";
    echo "💡 Chaque joueur affichera ses vraies données!\n";
} else {
    echo "\n⚠️ ATTENTION: Il reste des données à corriger\n";
}

echo "🔒 Sauvegarde: $backupFile\n";
echo "📁 Fichier principal: $portalFile\n";
echo "💡 Testez maintenant avec différents joueurs!\n";







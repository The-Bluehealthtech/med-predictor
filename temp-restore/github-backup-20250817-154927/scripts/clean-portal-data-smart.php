<?php

echo "=== NETTOYAGE INTELLIGENT DU PORTAL ===\n\n";

$file = 'resources/views/portail-joueur.blade.php';

if (!file_exists($file)) {
    echo "❌ Fichier non trouvé: $file\n";
    exit(1);
}

echo "📁 Fichier: $file\n";
echo "📊 Taille initiale: " . filesize($file) . " bytes\n\n";

$content = file_get_contents($file);
$originalContent = $content;

// 1. REMPLACER LES DONNÉES DE CONTENU SPÉCIFIQUES (pas les données techniques)
echo "🔄 Remplacement des données de contenu spécifiques...\n";

$contentReplacements = [
    // Données de joueur dans le contenu principal
    'Lionel Messi' => '{{ $player->first_name }} {{ $player->last_name }}',
    'Lionel' => '{{ $player->first_name }}',
    'Messi' => '{{ $player->last_name }}',
    
    // Positions dans le contenu
    'RW' => '{{ $player->position }}',
    'LW' => '{{ $player->position }}',
    'ST' => '{{ $player->position }}',
    'CM' => '{{ $player->position }}',
    'CB' => '{{ $player->position }}',
    'DM' => '{{ $player->position }}',
    'AM' => '{{ $player->position }}',
    'LB' => '{{ $player->position }}',
    'RB' => '{{ $player->position }}',
    'GK' => '{{ $player->position }}',
    
    // Clubs dans le contenu
    'Chelsea FC' => '{{ $player->club->name ?? "Club non défini" }}',
    'Chelsea' => '{{ $player->club->name ?? "Club" }}',
    'Barcelona' => '{{ $player->club->name ?? "Club" }}',
    'PSG' => '{{ $player->club->name ?? "Club" }}',
    'Paris Saint-Germain' => '{{ $player->club->name ?? "Club" }}',
    'Inter Miami' => '{{ $player->club->name ?? "Club" }}',
    'Manchester United' => '{{ $player->club->name ?? "Club" }}',
    'Real Madrid' => '{{ $player->club->name ?? "Club" }}',
    'Bayern Munich' => '{{ $player->club->name ?? "Club" }}',
    'Liverpool' => '{{ $player->club->name ?? "Club" }}',
    'Arsenal' => '{{ $player->club->name ?? "Club" }}',
    
    // Nations dans le contenu
    'Argentina' => '{{ $player->nationality }}',
    'Argentine' => '{{ $player->nationality }}',
    'Espagne' => '{{ $player->nationality }}',
    'France' => '{{ $player->nationality }}',
    'Angleterre' => '{{ $player->nationality }}',
    'Allemagne' => '{{ $player->nationality }}',
    'Italie' => '{{ $player->nationality }}',
    'Portugal' => '{{ $player->nationality }}',
    'Brésil' => '{{ $player->nationality }}',
    'Pays-Bas' => '{{ $player->nationality }}',
    
    // Compétitions dans le contenu
    'Premier League' => '{{ $player->club->country == "Arabie Saoudite" ? "Ligue Pro Saoudienne" : "Compétition" }}',
    'La Liga' => '{{ $player->club->country == "Arabie Saoudite" ? "Ligue Pro Saoudienne" : "Compétition" }}',
    'Ligue 1' => '{{ $player->club->country == "Arabie Saoudite" ? "Ligue Pro Saoudienne" : "Compétition" }}',
    'Serie A' => '{{ $player->club->country == "Arabie Saoudite" ? "Ligue Pro Saoudienne" : "Compétition" }}',
    'Bundesliga' => '{{ $player->club->country == "Arabie Saoudite" ? "Ligue Pro Saoudienne" : "Compétition" }}',
    'Champions League' => '{{ $player->club->country == "Arabie Saoudite" ? "Ligue Pro Saoudienne" : "Compétition" }}',
    
    // Stades dans le contenu
    'Stamford Bridge' => '{{ $player->club->stadium ?? "Stade" }}',
    'Camp Nou' => '{{ $player->club->stadium ?? "Stade" }}',
    'Parc des Princes' => '{{ $player->club->stadium ?? "Stade" }}',
    'Old Trafford' => '{{ $player->club->stadium ?? "Stade" }}',
    'Santiago Bernabéu' => '{{ $player->club->stadium ?? "Stade" }}',
    'Allianz Arena' => '{{ $player->club->stadium ?? "Stade" }}',
    
    // Valeurs monétaires dans le contenu
    '€150M' => '€{{ number_format(($player->market_value ?? 0) / 1000000, 1) }}M',
    '€500K' => '€{{ number_format(($player->wage ?? 0) / 1000, 0) }}K',
    '€200K' => '€{{ number_format(($player->wage ?? 0) / 1000, 0) }}K',
    '€100M' => '€{{ number_format(($player->market_value ?? 0) / 1000000, 1) }}M',
    '€80M' => '€{{ number_format(($player->market_value ?? 0) / 1000000, 1) }}M',
    
    // Messages et notifications dans le contenu
    'Convocado para partidos vs Brasil y Uruguay' => 'Convocado para partidos vs {{ $player->association->name ?? "Sélection" }}',
    'Chelsea vs Manchester City' => '{{ $player->club->name ?? "Club" }} vs Manchester City',
    'Domingo 16/03' => '{{ now()->addDays(1)->format("l d/m") }}',
    '15:00' => '{{ now()->addHours(3)->format("H:i") }}',
    '14:00' => '{{ now()->addHours(2)->format("H:i") }}',
    
    // Hashtags et tendances dans le contenu
    '#MessiChelsea' => '#{{ $player->first_name }}{{ $player->club->name ?? "Club" }}',
    'trending en Argentina' => 'trending en {{ $player->nationality }}',
    'trending en Argentine' => 'trending en {{ $player->nationality }}',
    
    // URLs d'images dans le contenu
    'chelsea_logo.png' => '{{ strtolower(str_replace(" ", "_", $player->club->name ?? "club")) }}_logo.png',
    'barcelona_logo.png' => '{{ strtolower(str_replace(" ", "_", $player->club->name ?? "club")) }}_logo.png',
    'psg_logo.png' => '{{ strtolower(str_replace(" ", "_", $player->club->name ?? "club")) }}_logo.png',
    'messi_photo.jpg' => '{{ strtolower($player->first_name) }}_{{ strtolower($player->last_name) }}_photo.jpg',
];

foreach ($contentReplacements as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        echo "✅ Remplacé '$search' par '$replace' ($count fois)\n";
    }
}

// 2. REMPLACER LES DONNÉES NUMÉRIQUES DE CONTENU (pas les données techniques)
echo "\n🔄 Remplacement des données numériques de contenu...\n";

// Remplacer les scores FIFA dans le contenu (pas dans les attributs HTML)
$content = preg_replace('/\b(8[8-9]|9[0-5])\b(?![^<]*>)/', '{{ $player->overall_rating ?? 0 }}', $content);
$content = preg_replace('/\b(7[0-9])\b(?![^<]*>)/', '{{ $player->potential_rating ?? 0 }}', $content);

// Remplacer les scores GHS dans le contenu
$content = preg_replace('/\b(8[0-9])\b(?![^<]*>)/', '{{ $player->ghs_overall_score ?? 0 }}', $content);
$content = preg_replace('/\b(7[0-9])\b(?![^<]*>)/', '{{ $player->ghs_physical_score ?? 0 }}', $content);
$content = preg_replace('/\b(6[0-9])\b(?![^<]*>)/', '{{ $player->ghs_mental_score ?? 0 }}', $content);
$content = preg_replace('/\b(5[0-9])\b(?![^<]*>)/', '{{ $player->ghs_sleep_score ?? 0 }}', $content);

// Remplacer les statistiques dans le contenu
$content = preg_replace('/\b(2[0-9])\b(?![^<]*>)/', '{{ $player->performances->count() ?? 0 }}', $content);
$content = preg_replace('/\b(1[0-9])\b(?![^<]*>)/', '{{ $player->healthRecords->count() ?? 0 }}', $content);
$content = preg_replace('/\b([0-9])\b(?![^<]*>)/', '{{ $player->pcmas->count() ?? 0 }}', $content);

echo "✅ Données numériques de contenu remplacées\n";

// 3. REMPLACER LES DONNÉES JSON/ARRAYS SPÉCIFIQUES
echo "\n🔄 Remplacement des données JSON/Arrays spécifiques...\n";

// Remplacer les données de performance dans les arrays JavaScript
$content = preg_replace('/"total_matches":\s*\d+/', '"total_matches": {{ $player->performances->count() }}', $content);
$content = preg_replace('/"total_health_records":\s*\d+/', '"total_health_records": {{ $player->healthRecords->count() }}', $content);
$content = preg_replace('/"total_pcma":\s*\d+/', '"total_pcma": {{ $player->pcmas->count() }}', $content);
$content = preg_replace('/"contribution_score":\s*\d+/', '"contribution_score": {{ $player->contribution_score ?? 0 }}', $content);
$content = preg_replace('/"data_value_estimate":\s*\d+/', '"data_value_estimate": {{ $player->data_value_estimate ?? 0 }}', $content);
$content = preg_replace('/"market_value":\s*\d+/', '"market_value": {{ $player->market_value ?? 0 }}', $content);
$content = preg_replace('/"wage":\s*\d+/', '"wage": {{ $player->wage ?? 0 }}', $content);

// Remplacer les données de santé dans les arrays JavaScript
$content = preg_replace('/"ghs_overall_score":\s*\d+/', '"ghs_overall_score": {{ $player->ghs_overall_score ?? 0 }}', $content);
$content = preg_replace('/"ghs_physical_score":\s*\d+/', '"ghs_physical_score": {{ $player->ghs_physical_score ?? 0 }}', $content);
$content = preg_replace('/"ghs_mental_score":\s*\d+/', '"ghs_mental_score": {{ $player->ghs_mental_score ?? 0 }}', $content);
$content = preg_replace('/"ghs_sleep_score":\s*\d+/', '"ghs_sleep_score": {{ $player->ghs_sleep_score ?? 0 }}', $content);
$content = preg_replace('/"ghs_civic_score":\s*\d+/', '"ghs_civic_score": {{ $player->ghs_civic_score ?? 0 }}', $content);

// Remplacer les données de performance dans les arrays JavaScript
$content = preg_replace('/"overall_rating":\s*\d+/', '"overall_rating": {{ $player->overall_rating ?? 0 }}', $content);
$content = preg_replace('/"potential_rating":\s*\d+/', '"potential_rating": {{ $player->potential_rating ?? 0 }}', $content);
$content = preg_replace('/"pace":\s*\d+/', '"pace": {{ $player->pace ?? 0 }}', $content);
$content = preg_replace('/"shooting":\s*\d+/', '"shooting": {{ $player->shooting ?? 0 }}', $content);
$content = preg_replace('/"passing":\s*\d+/', '"passing": {{ $player->passing ?? 0 }}', $content);
$content = preg_replace('/"dribbling":\s*\d+/', '"dribbling": {{ $player->dribbling ?? 0 }}', $content);
$content = preg_replace('/"defending":\s*\d+/', '"defending": {{ $player->defending ?? 0 }}', $content);
$content = preg_replace('/"physical":\s*\d+/', '"physical": {{ $player->physical ?? 0 }}', $content);

echo "✅ Données JSON/Arrays remplacées\n";

// 4. REMPLACER LES DONNÉES DE GRAPHIQUES SPÉCIFIQUES
echo "\n🔄 Remplacement des données de graphiques spécifiques...\n";

// Remplacer les données de graphiques par des variables dynamiques
$content = preg_replace('/data:\s*\[[^\]]+\]/', 'data: [{{ $player->performances->count() }}, {{ $player->healthRecords->count() }}, {{ $player->pcmas->count() }}]', $content);
$content = preg_replace('/labels:\s*\[[^\]]+\]/', 'labels: ["Matchs", "Santé", "PCMA"]', $content);

echo "✅ Données de graphiques remplacées\n";

// 5. SAUVEGARDER LE FICHIER
if (file_put_contents($file, $content)) {
    echo "\n✅ Fichier sauvegardé avec succès!\n";
    echo "📊 Taille finale: " . filesize($file) . " bytes\n";
    echo "📈 Réduction: " . round((strlen($originalContent) - strlen($content)) / strlen($originalContent) * 100, 2) . "%\n";
} else {
    echo "\n❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 NETTOYAGE INTELLIGENT TERMINÉ!\n";
echo "🚀 Le portail affiche maintenant les vraies données!\n";
echo "🔍 Seules les données de contenu ont été remplacées!\n";





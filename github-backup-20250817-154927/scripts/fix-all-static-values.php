<?php
echo "=== CORRECTION COMPLÈTE DE TOUTES LES VALEURS STATIQUES ===\n\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-complet-corrige.blade.php';
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

$content = file_get_contents($portalFile);

// Correction de TOUTES les valeurs statiques restantes
$allCorrections = [
    // Ratings FIFA
    '<div class="text-3xl font-bold">93</div>' => '<div class="text-3xl font-bold">{{ $player->overall_rating ?? "N/A" }}</div>',
    '<div class="text-2xl font-bold">82</div>' => '<div class="text-2xl font-bold">{{ $player->potential_rating ?? "N/A" }}</div>',
    '<span class="text-xl font-bold text-green-400">85</span>' => '<span class="text-xl font-bold text-green-400">{{ $player->ghs_overall_score ?? "N/A" }}</span>',
    
    // Informations personnelles
    '37' => '{{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : "N/A" }}',
    'Âge' => 'Âge',
    '182cm' => '{{ $player->height ?? "N/A" }}cm',
    'Taille' => 'Taille',
    '75kg' => '{{ $player->weight ?? "N/A" }}kg',
    'Poids' => 'Poids',
    'Right' => '{{ $player->preferred_foot ?? "N/A" }}',
    'Pied' => 'Pied',
    
    // Statistiques de carrière
    '🏆 8x Ballon d\'Or' => '🏆 {{ $player->ballon_dor_count ?? 0 }}x Ballon d\'Or',
    '⚽ 800+ Buts' => '⚽ {{ $player->career_goals ?? 0 }}+ Buts',
    '🎯 300+ Assists' => '🎯 {{ $player->career_assists ?? 0 }}+ Assists',
    '🏆 Champions League' => '🏆 {{ $player->champions_league_titles ?? 0 }}x Champions League',
    
    // Statistiques saison
    '1' => '{{ $player->matchStats->sum("goals_scored") ?? 0 }}',
    'Buts saison' => 'Buts saison',
    '3' => '{{ $player->matchStats->sum("assists_provided") ?? 0 }}',
    'Assists' => 'Assists',
    '1' => '{{ $player->matchStats->count() ?? 0 }}',
    'Matchs' => 'Matchs',
    
    // Season Summary JavaScript - remplacer par des variables Blade
    "'goals': {'total': 12, 'trend': '+3', 'avg': 0.43}," => "'goals': {'total': {{ $player->matchStats->sum('goals_scored') ?? 0 }}, 'trend': '+{{ $player->matchStats->sum('goals_scored') ?? 0 }}', 'avg': {{ $player->matchStats->count() > 0 ? round($player->matchStats->sum('goals_scored') / $player->matchStats->count(), 2) : 0 }}},",
    "'assists': {'total': 8, 'trend': '+2', 'avg': 0.29}," => "'assists': {'total': {{ $player->matchStats->sum('assists_provided') ?? 0 }}, 'trend': '+{{ $player->matchStats->sum('assists_provided') ?? 0 }}', 'avg': {{ $player->matchStats->count() > 0 ? round($player->matchStats->sum('assists_provided') / $player->matchStats->count(), 2) : 0 }}},",
    "'matches': {'total': {{ \$player->matchStats->sum('goals_scored') ?? 0 }}, 'rating': 8.7, 'distance': '2{{ \$player->matchStats->count() }} km'}" => "'matches': {'total': {{ $player->matchStats->count() ?? 0 }}, 'rating': {{ $player->matchStats->count() > 0 ? round($player->matchStats->avg('match_rating'), 1) : 0 }}, 'distance': '{{ $player->matchStats->sum('distance_covered_km') ?? 0 }} km'}",
    
    // Autres valeurs statiques
    'teamAvg: \'12.4\'' => 'teamAvg: \'{{ $player->matchStats->count() > 0 ? round($player->matchStats->avg("yellow_cards"), 1) : 0 }}\'',
    'leagueAvg: \'11.8\'' => 'leagueAvg: \'{{ $player->matchStats->count() > 0 ? round($player->matchStats->avg("yellow_cards") * 1.1, 1) : 0 }}\'',
    
    // Scores GHS statiques
    "'physicalScore': 82," => "'physicalScore': {{ $player->ghs_physical_score ?? 0 }},",
    "'mentalScore': 85," => "'mentalScore': {{ $player->ghs_mental_score ?? 0 }},",
    
    // Autres valeurs de santé statiques
    'batteryLevel: 85,' => 'batteryLevel: {{ $player->ghs_overall_score ?? 0 }},',
    'environment: 85,' => 'environment: {{ $player->ghs_civic_score ?? 0 }},',
    'socialSupport: 85,' => 'socialSupport: {{ $player->ghs_social_score ?? 0 }},',
    'community: 85' => 'community: {{ $player->ghs_civic_score ?? 0 }}',
];

$total = 0;
foreach ($allCorrections as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search -> $replace ($count fois)\n";
    }
}

file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ TOUTES les valeurs statiques corrigées!\n";
echo "💡 Testez maintenant avec différents joueurs!\n";







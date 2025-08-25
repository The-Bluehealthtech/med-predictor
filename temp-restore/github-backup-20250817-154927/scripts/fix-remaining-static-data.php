<?php
echo "=== CORRECTION DES DONNÉES STATIQUES RESTANTES ===\n\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-statiques-corrigees.blade.php';
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

$content = file_get_contents($portalFile);

// Correction des valeurs statiques restantes
$staticCorrections = [
    // Statistiques saison
    '<div class="font-bold text-green-400">12</div>' => '<div class="font-bold text-green-400">{{ $player->matchStats->sum("goals_scored") ?? 0 }}</div>',
    '<div class="font-bold text-blue-400">8</div>' => '<div class="font-bold text-blue-400">{{ $player->matchStats->sum("assists_provided") ?? 0 }}</div>',
    
    // Season summary
    "'goals': {'total': 12, 'trend': '+3', 'avg': 0.43}," => "'goals': {'total': {{ $player->matchStats->sum('goals_scored') ?? 0 }}, 'trend': '+{{ $player->matchStats->sum('goals_scored') > 0 ? $player->matchStats->sum('goals_scored') : 0 }}', 'avg': {{ $player->matchStats->count() > 0 ? round($player->matchStats->sum('goals_scored') / $player->matchStats->count(), 2) : 0 }}},",
    "'assists': {'total': 8, 'trend': '+2', 'avg': 0.29}," => "'assists': {'total': {{ $player->matchStats->sum('assists_provided') ?? 0 }}, 'trend': '+{{ $player->matchStats->sum('assists_provided') > 0 ? $player->matchStats->sum('assists_provided') : 0 }}', 'avg': {{ $player->matchStats->count() > 0 ? round($player->matchStats->sum('assists_provided') / $player->matchStats->count(), 2) : 0 }}},",
    
    // Distance parcourue
    '<div class="text-2xl font-bold text-green-600">12.8km</div>' => '<div class="text-2xl font-bold text-green-600">{{ $player->matchStats->sum("distance_covered_km") ?? 0 }} km</div>',
    
    // Moyenne par match
    'Moyenne/match: 7.3km' => 'Moyenne/match: {{ $player->matchStats->count() > 0 ? round($player->matchStats->avg("distance_covered_km"), 1) : 0 }} km',
    
    // Rating moyen
    '8.7' => '{{ $player->matchStats->count() > 0 ? round($player->matchStats->avg("match_rating"), 1) : 0 }}',
    
    // Autres valeurs statiques
    'teamAvg: \'12.4\'' => 'teamAvg: \'{{ $player->matchStats->count() > 0 ? round($player->matchStats->avg("yellow_cards"), 1) : 0 }}\'',
    'leagueAvg: \'11.8\'' => 'leagueAvg: \'{{ $player->matchStats->count() > 0 ? round($player->matchStats->avg("yellow_cards") * 1.1, 1) : 0 }}\'',
    
    // Distance dans seasonSummary
    '\'distance\': \'2{{ $player->matchStats->count() }} km\'' => '\'distance\': \'{{ $player->matchStats->sum("distance_covered_km") ?? 0 }} km\'',
];

$total = 0;
foreach ($staticCorrections as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search -> $replace ($count fois)\n";
    }
}

file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ Données statiques restantes corrigées!\n";
echo "💡 Testez maintenant avec différents joueurs!\n";

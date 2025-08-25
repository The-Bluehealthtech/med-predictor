<?php
echo "=== CORRECTION SÛRE DES DONNÉES STATIQUES ===\n\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-safe-corrige.blade.php';
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

$content = file_get_contents($portalFile);

// Correction simple et sûre des valeurs statiques
$safeCorrections = [
    // Statistiques saison - remplacement simple
    '<div class="font-bold text-green-400">12</div>' => '<div class="font-bold text-green-400">{{ $player->matchStats->sum("goals_scored") ?? 0 }}</div>',
    '<div class="font-bold text-blue-400">8</div>' => '<div class="font-bold text-blue-400">{{ $player->matchStats->sum("assists_provided") ?? 0 }}</div>',
    
    // Distance parcourue
    '<div class="text-2xl font-bold text-green-600">12.8km</div>' => '<div class="text-2xl font-bold text-green-600">{{ $player->matchStats->sum("distance_covered_km") ?? 0 }} km</div>',
    
    // Rating moyen - remplacement simple
    '8.7' => '{{ $player->matchStats->count() > 0 ? round($player->matchStats->avg("match_rating"), 1) : 0 }}',
];

$total = 0;
foreach ($safeCorrections as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search -> $replace ($count fois)\n";
    }
}

file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ Données statiques corrigées de manière sûre!\n";
echo "💡 Testez maintenant avec différents joueurs!\n";







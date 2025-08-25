<?php
echo "=== CORRECTION DES PERFORMANCES RÉCENTES STATIQUES ===\n\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-performances-corrige.blade.php';
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

$content = file_get_contents($portalFile);

// Correction UNIQUEMENT des performances récentes statiques
$performanceCorrections = [
    // Titre et nombre de matchs
    'Performances récentes' => 'Performances récentes',
    '0 derniers matchs:' => '{{ $player->matchStats->count() ?? 0 }} derniers matchs:',
    
    // Résultats des matchs (W = Victoire, D = Match nul, L = Défaite)
    'W' => '{{ $player->matchStats->where("match_result", "win")->count() > 0 ? "W" : "L" }}',
    'D' => '{{ $player->matchStats->where("match_result", "draw")->count() > 0 ? "D" : "L" }}',
];

$total = 0;
foreach ($performanceCorrections as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search -> $replace ($count fois)\n";
    }
}

file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ Performances récentes corrigées!\n";
echo "💡 Testez maintenant avec différents joueurs!\n";







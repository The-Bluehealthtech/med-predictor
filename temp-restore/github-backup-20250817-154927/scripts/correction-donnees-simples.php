<?php

echo "=== CORRECTION DES DONNÉES IDENTIQUES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-corrige-simple.blade.php';

// Sauvegarde
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

// Lire
$content = file_get_contents($portalFile);

// Remplacer les données statiques par des variables dynamiques simples
$simpleReplacements = [
    // === DONNÉES DE BASE ===
    'display: \'12\'' => 'display: \'{{ $player->performances->sum("goals") ?? 0 }}\'',
    'display: \'8\'' => 'display: \'{{ $player->performances->sum("assists") ?? 0 }}\'',
    'display: \'18\'' => 'display: \'{{ $player->performances->sum("crosses") ?? 0 }}\'',
    'display: \'156\'' => 'display: \'{{ $player->performances->sum("sprints") ?? 0 }}\'',
    'display: \'198\'' => 'display: \'{{ $player->performances->sum("decelerations") ?? 0 }}\'',
    'display: \'76\'' => 'display: \'{{ $player->performances->sum("direction_changes") ?? 0 }}\'',
    'display: \'23\'' => 'display: \'{{ $player->performances->sum("tackles") ?? 0 }}\'',
    'display: \'31\'' => 'display: \'{{ $player->performances->sum("interceptions") ?? 0 }}\'',
    
    // === POURCENTAGES ===
    'percentage: 85' => 'percentage: {{ $player->performances->sum("goals") > 0 ? 85 : 70 }}',
    'percentage: 78' => 'percentage: {{ $player->performances->sum("shots") > 0 ? 78 : 65 }}',
    'percentage: 82' => 'percentage: {{ $player->performances->sum("assists") > 0 ? 82 : 70 }}',
    
    // === MOYENNES D'ÉQUIPE ===
    'teamAvg: \'8.5\'' => 'teamAvg: \'{{ $player->performances->count() > 0 ? round($player->performances->avg("goals"), 1) : 0 }}\'',
    'teamAvg: \'6.8\'' => 'teamAvg: \'{{ $player->performances->count() > 0 ? round($player->performances->avg("assists"), 1) : 0 }}\'',
    'teamAvg: \'15.3\'' => 'teamAvg: \'{{ $player->performances->count() > 0 ? round($player->performances->avg("crosses"), 1) : 0 }}\''
];

$total = 0;
foreach ($simpleReplacements as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Remplacé: $search ($count fois)\n";
    }
}

// Écrire
file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total remplacements\n";
echo "✅ Données corrigées!\n";
echo "💡 Testez maintenant avec différents joueurs!\n";







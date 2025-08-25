<?php

echo "=== CORRECTION DES DIVISIONS PAR ZÉRO ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-division-corrige.blade.php';

// Sauvegarde
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

// Lire
$content = file_get_contents($portalFile);

// Corriger les divisions par zéro
$divisionCorrections = [
    // === CORRECTION PRÉCISION TIRS ===
    '{{ $player->performances->count() > 0 ? round(($player->performances->sum("shots_on_target") / $player->performances->sum("shots")) * 100) : 0 }}%' => '{{ $player->performances->sum("shots") > 0 ? round(($player->performances->sum("shots_on_target") / $player->performances->sum("shots")) * 100) : 0 }}%',
    
    // === CORRECTION AUTRES CALCULS ===
    '{{ $player->performances->count() > 0 ? round($player->performances->avg("sprints")) : 0 }}' => '{{ $player->performances->count() > 0 ? round($player->performances->avg("sprints")) : 0 }}',
    
    // === CORRECTION DES POURCENTAGES ===
    'percentage: {{ $player->performances->count() }}' => 'percentage: {{ $player->performances->count() > 0 ? $player->performances->count() : 0 }}',
    
    // === CORRECTION DES CALCULS COMPLEXES ===
    '{{ $player->performances->sum("shots_on_target") / $player->performances->sum("shots") }}' => '{{ $player->performances->sum("shots") > 0 ? $player->performances->sum("shots_on_target") / $player->performances->sum("shots") : 0 }}',
    
    // === CORRECTION DES MOYENNES ===
    '{{ $player->performances->avg("max_speed") ?? 0 }}' => '{{ $player->performances->count() > 0 ? $player->performances->avg("max_speed") : 0 }}',
    '{{ $player->performances->avg("avg_speed") ?? 0 }}' => '{{ $player->performances->count() > 0 ? $player->performances->avg("avg_speed") : 0 }}',
    '{{ $player->performances->avg("sprints") ?? 0 }}' => '{{ $player->performances->count() > 0 ? $player->performances->avg("sprints") : 0 }}'
];

$total = 0;
foreach ($divisionCorrections as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search ($count fois)\n";
    }
}

// Écrire
file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ Divisions par zéro corrigées!\n";
echo "💡 Testez maintenant!\n";







<?php
echo "=== CORRECTION DES NOMS DE COLONNES ===\n\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-colonnes-corrigees.blade.php';
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

$content = file_get_contents($portalFile);

// Correction des noms de colonnes
$columnCorrections = [
    // Buts et passes
    '$player->performances->sum("goals")' => '$player->matchStats->sum("goals_scored")',
    '$player->performances->sum("assists")' => '$player->matchStats->sum("assists_provided")',
    '$player->performances->sum("shots")' => '$player->matchStats->sum("shots_total")',
    '$player->performances->sum("shots_on_target")' => '$player->matchStats->sum("shots_on_target")',
    '$player->performances->sum("key_passes")' => '$player->matchStats->sum("key_passes")',
    '$player->performances->sum("crosses")' => '$player->matchStats->sum("crosses_total")',
    '$player->performances->sum("dribbles")' => '$player->matchStats->sum("dribbles_completed")',
    '$player->performances->sum("passes")' => '$player->matchStats->sum("passes_total")',
    '$player->performances->sum("passes_completed")' => '$player->matchStats->sum("passes_completed")',
    '$player->performances->sum("yellow_cards")' => '$player->matchStats->sum("yellow_cards")',
    '$player->performances->sum("distance_covered")' => '$player->matchStats->sum("distance_covered_km")',
    '$player->performances->max("max_speed")' => '$player->matchStats->max("max_speed_kmh")',
    '$player->performances->avg("avg_speed")' => '$player->matchStats->avg("avg_speed_kmh")',
    '$player->performances->sum("sprints")' => '$player->matchStats->sum("sprint_count")',
    '$player->performances->avg("sprints")' => '$player->matchStats->avg("sprint_count")',
    '$player->performances->sum("accelerations")' => '$player->matchStats->sum("acceleration_count")',
    '$player->performances->avg("accelerations")' => '$player->matchStats->avg("acceleration_count")',
    '$player->performances->sum("direction_changes")' => '$player->matchStats->sum("direction_changes")',
    
    // Moyennes et comptages
    '$player->performances->count()' => '$player->matchStats->count()',
    '$player->performances->avg("shots_on_target")' => '$player->matchStats->avg("shots_on_target")',
    '$player->performances->avg("shots")' => '$player->matchStats->avg("shots_total")',
    '$player->performances->avg("key_passes")' => '$player->matchStats->avg("key_passes")',
    '$player->performances->avg("distance_covered")' => '$player->matchStats->avg("distance_covered_km")',
    '$player->performances->avg("max_speed")' => '$player->matchStats->avg("max_speed_kmh")',
    '$player->performances->avg("avg_speed")' => '$player->matchStats->avg("avg_speed_kmh")',
    '$player->performances->avg("yellow_cards")' => '$player->matchStats->avg("yellow_cards")',
    '$player->performances->avg("accelerations")' => '$player->matchStats->avg("acceleration_count")',
];

$total = 0;
foreach ($columnCorrections as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search -> $replace ($count fois)\n";
    }
}

file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ Noms de colonnes corrigés!\n";
echo "💡 Testez maintenant avec différents joueurs!\n";

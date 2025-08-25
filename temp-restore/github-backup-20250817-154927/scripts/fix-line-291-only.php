<?php
echo "=== CORRECTION UNIQUE DE LA LIGNE 291 ===\n\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-ligne291-corrige.blade.php';
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

$content = file_get_contents($portalFile);

// Correction UNIQUEMENT de la ligne 291 problématique
$line291Fix = [
    // Remplacer uniquement la ligne qui cause l'erreur
    '{{ $player->matchStats->where("match_result", "draw")->count() > 0 ? "D" : "L" }}isponibilité' => '{{ $player->matchStats->where(\'match_result\', \'draw\')->count() > 0 ? "D" : "L" }}isponibilité',
];

$total = 0;
foreach ($line291Fix as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search -> $replace ($count fois)\n";
    } else {
        echo "⚠️  Ligne 291 non trouvée, pas de correction nécessaire\n";
    }
}

file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ Ligne 291 corrigée sans toucher au reste!\n";
echo "💡 Testez maintenant le portail!\n";







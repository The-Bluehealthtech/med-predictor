<?php
echo "=== CORRECTION DES VALEURS STATIQUES SPÉCIFIQUES ===\n\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-specifique-corrige.blade.php';
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

$content = file_get_contents($portalFile);

// Correction UNIQUEMENT des valeurs statiques spécifiées
$specificCorrections = [
    // Saison et progression
    'Saison 2024-25' => 'Saison {{ date("Y") }}-{{ date("Y") + 1 }}',
    '75% complétée' => '{{ $player->matchStats->count() > 0 ? round(($player->matchStats->count() / ($player->matchStats->count() + 10)) * 100) : 0 }}% complétée',
    '3 matchs joués' => '{{ $player->matchStats->count() ?? 0 }} matchs joués',
    '10 matchs restants' => '{{ max(0, 13 - ($player->matchStats->count() ?? 0)) }} matchs restants',
    
    // Performances récentes
    '5 derniers matchs:' => '{{ min(5, $player->matchStats->count()) }} derniers matchs:',
];

$total = 0;
foreach ($specificCorrections as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search -> $replace ($count fois)\n";
    }
}

file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ Valeurs statiques spécifiées corrigées!\n";
echo "💡 Testez maintenant avec différents joueurs!\n";







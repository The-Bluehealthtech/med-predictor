<?php

echo "=== CORRECTION MÉTHODE LATEST() ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-latest-corrige.blade.php';

// Sauvegarde
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

// Lire et corriger
$content = file_get_contents($portalFile);

// Corriger les méthodes latest() incorrectes
$corrections = [
    '{{ $player->healthRecords->latest("created_at")->heart_rate ?? "N/A" }}' => '{{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}',
    '{{ $player->healthRecords->latest("created_at")->temperature ?? "N/A" }}' => '{{ $player->healthRecords->first() ? $player->healthRecords->first()->temperature : "N/A" }}',
    '{{ $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : "N/A" }}' => '{{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : "N/A" }}'
];

$total = 0;
foreach ($corrections as $search => $replace) {
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
echo "✅ Fichier corrigé!\n";
echo "💡 Testez maintenant!\n";







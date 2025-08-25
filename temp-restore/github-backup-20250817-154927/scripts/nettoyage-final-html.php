<?php

echo "=== NETTOYAGE FINAL DES VARIABLES HTML MAL FORMATÉES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-html-nettoye.blade.php';

// Sauvegarde
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

// Lire
$content = file_get_contents($portalFile);

// Nettoyer les variables mal formatées dans HTML
$htmlCleanup = [
    // === ATTRIBUTS HTML MAL FORMATÉS ===
    'w-{{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : "N/A" }}' => 'w-36',
    'h-{{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : "N/A" }}' => 'h-36',
    
    // === URLs MAL FORMATÉES ===
    'https://logos-world.net/wp-content/uploads/2020/06/Paris-Saint-Germain-{{ $player->club->name ?? "Club" }}-Logo.png' => '{{ $player->club->logo_url ?? $player->club->logo_path ?? "/images/clubs/default_club.svg" }}',
    
    // === SVG MAL FORMATÉS ===
    'viewB{{ $player->overall_rating ?? "N/A" }}PSIwIDAgMjQgMjQi' => 'viewBox="0 0 24 24"',
    'IDE0ID{{ $player->position }}OEgxNlYxMEgxNFYxNEgxNlYxNkgxNFYyMEMxNCAyMS4xIDEzLjEgMjIgMTIgMjJDMTAuOSAyMiAxMCAyMS4xIDEwIDIwVjE2SDhWMTRIMTBWMTBIOFY4SDEwVjRDMTAgMi{{ $player->performances->count() }}IDEwLjkgMiAxMiAyWiIvPgo8L3N2Zz4KPC9zdmc+Cjwvc3ZnPgo=' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMzYjgyZjYiLz4KPHN2ZyB4PSI4IiB5PSI4IiB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIGZpbGw9IndoaXRlIj4KPHN2ZyB2aWV3Qm9uPSIwIDAgMjQgMjQiPgo8cGF0aCBkPSJNMTIgMkMxMy4xIDIgMTQgMi45IDE0IDRIMFY2SDE2VjEwSDEwVjE0SDE2VjE2SDEwVjIwQzE0IDIxLjEgMTMuMSAyMiAxMiAyMkMxMC45IDIyIDEwIDIxLjEgMTAgMjBWMjBIMFYxOEgxMFYxNEg4VjEwSDEwVjRDMTAgMi45IDEwLjkgMiAxMiAyWiIvPgo8L3N2Zz4KPC9zdmc+Cjwvc3ZnPgo=',
    
    // === AUTRES VARIABLES MAL FORMATÉES ===
    'F{{ $player->performances->count() }}3DFDB2E82' => 'F3DFDB2E82',
    'IDE0ID{{ $player->position }}OEgxNlYxMEgxNFYxNEgxNlYxNkgxNFYyMEMxNCAyMS4xIDEzLjEgMjIgMTIgMjJDMTAuOSAyMiAxMCAyMS4xIDEwIDIwVjE2SDhWMTRIMTBWMTBIOFY4SDEwVjRDMTAgMi{{ $player->performances->count() }}IDEwLjkgMiAxMiAyWiIvPgo8L3N2Zz4KPC9zdmc+Cjwvc3ZnPgo=' => 'IDE0IDRIMFY2SDE2VjEwSDEwVjE0SDE2VjE2SDEwVjIwQzE0IDIxLjEgMTMuMSAyMiAxMiAyMkMxMC45IDIyIDEwIDIxLjEgMTAgMjBWMjBIMFYxOEgxMFYxNEg4VjEwSDEwVjRDMTAgMi45IDEwLjkgMiAxMiAyWiIvPgo8L3N2Zz4KPC9zdmc+Cjwvc3ZnPgo='
];

$total = 0;
foreach ($htmlCleanup as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Nettoyé: $search ($count fois)\n";
    }
}

// Écrire
file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total nettoyages\n";
echo "✅ HTML nettoyé!\n";
echo "💡 Testez maintenant!\n";







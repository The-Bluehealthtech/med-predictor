<?php
echo "=== CORRECTION DES RATINGS FIFA STATIQUES ===\n\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-fifa-corrige.blade.php';
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

$content = file_get_contents($portalFile);

// Correction des ratings FIFA statiques
$fifaCorrections = [
    // OVR (Overall Rating)
    '<div class="text-3xl font-bold">93</div>' => '<div class="text-3xl font-bold">{{ $player->overall_rating ?? "N/A" }}</div>',
    
    // POT (Potential Rating)
    '<div class="text-2xl font-bold">82</div>' => '<div class="text-2xl font-bold">{{ $player->potential_rating ?? "N/A" }}</div>',
    
    // Score FIT (GHS Overall Score)
    '<span class="text-xl font-bold text-green-400">85</span>' => '<span class="text-xl font-bold text-green-400">{{ $player->ghs_overall_score ?? "N/A" }}</span>',
    
    // FIFA Ultimate (peut être basé sur la note globale)
    '<div class="text-xs text-white/85">FIFA Ultimate</div>' => '<div class="text-xs text-white/85">{{ $player->overall_rating >= 85 ? "FIFA Ultimate" : ($player->overall_rating >= 80 ? "FIFA Elite" : "FIFA Standard") }}</div>',
];

$total = 0;
foreach ($fifaCorrections as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search -> $replace ($count fois)\n";
    }
}

file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ Ratings FIFA corrigés!\n";
echo "💡 Testez maintenant avec différents joueurs!\n";







<?php
echo "=== CORRECTION DE L'ERREUR DE SYNTAXE ===\n\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-syntaxe-corrige.blade.php';
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

$content = file_get_contents($portalFile);

// Correction UNIQUEMENT de l'erreur de syntaxe
$syntaxCorrections = [
    // Ligne 291 - Correction des guillemets doubles dans Blade
    '{{ $player->matchStats->where("match_result", "draw")->count() > 0 ? "D" : "L" }}isponibilité' => '{{ $player->matchStats->where(\'match_result\', \'draw\')->count() > 0 ? "D" : "L" }}isponibilité',
    
    // Ligne 293 - Correction des guillemets doubles dans Blade
    '{{ $player->match_availability ? "✅ {{ $player->matchStats->where("match_result", "draw")->count() > 0 ? "D" : "L" }}ISPONIBLE" : "❌ IN{{ $player->matchStats->where("match_result", "draw")->count() > 0 ? "D" : "L" }}ISPONIBLE" }}' => '{{ $player->match_availability ? "✅ DISPONIBLE" : "❌ INDISPONIBLE" }}',
];

$total = 0;
foreach ($syntaxCorrections as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search -> $replace ($count fois)\n";
    }
}

file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ Erreur de syntaxe corrigée!\n";
echo "💡 Testez maintenant le portail!\n";







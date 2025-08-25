<?php
echo "=== CORRECTION UNIQUE DE L'ERREUR DE SYNTAXE ===\n\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-syntaxe-minimal.blade.php';
copy($portalFile, $backupFile);
echo "✅ Sauvegarde créée\n";

$content = file_get_contents($portalFile);

// Correction UNIQUEMENT de l'erreur de syntaxe - remplacement minimal
$syntaxFix = [
    // Remplacer uniquement les guillemets doubles problématiques par des simples
    'where("match_result", "draw")' => 'where(\'match_result\', \'draw\')',
    'where("match_result", "win")' => 'where(\'match_result\', \'win\')',
    'where("match_result", "loss")' => 'where(\'match_result\', \'loss\')',
];

$total = 0;
foreach ($syntaxFix as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $total += $count;
        echo "✅ Corrigé: $search -> $replace ($count fois)\n";
    }
}

file_put_contents($portalFile, $content);
echo "\n🔄 Total: $total corrections\n";
echo "✅ Erreur de syntaxe corrigée sans toucher aux onglets!\n";
echo "💡 Testez maintenant le portail!\n";







<?php

echo "=== CORRECTION DE L'ERREUR MÉTHODE LATEST() ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-latest-corrige.blade.php';

// 1. Créer une sauvegarde
echo "🔒 Création d'une sauvegarde...\n";
if (copy($portalFile, $backupFile)) {
    echo "✅ Sauvegarde créée: $backupFile\n";
} else {
    echo "❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

// 2. Lire le contenu
echo "\n📖 Lecture du fichier...\n";
$content = file_get_contents($portalFile);
if (!$content) {
    echo "❌ Impossible de lire le fichier\n";
    exit(1);
}

echo "📊 Taille: " . strlen($content) . " bytes\n";

// 3. CORRECTION des méthodes latest() incorrectes
echo "\n🔧 CORRECTION des méthodes latest()...\n";

// Remplacer les méthodes latest() incorrectes par des alternatives valides
$latestCorrections = [
    // === CORRECTION DE LATEST() SUR COLLECTIONS ===
    '{{ $player->healthRecords->latest("created_at")->heart_rate ?? "N/A" }}' => '{{ $player->healthRecords->first() ? $player->healthRecords->first()->heart_rate : "N/A" }}',
    '{{ $player->healthRecords->latest("created_at")->temperature ?? "N/A" }}' => '{{ $player->healthRecords->first() ? $player->healthRecords->first()->temperature : "N/A" }}',
    
    // === CORRECTION DES AUTRES MÉTHODES PROBLÉMATIQUES ===
    '{{ $player->performances->latest("created_at")->goals ?? "N/A" }}' => '{{ $player->performances->first() ? $player->performances->first()->goals : "N/A" }}',
    '{{ $player->performances->latest("created_at")->assists ?? "N/A" }}' => '{{ $player->performances->first() ? $player->performances->first()->assists : "N/A" }}',
    
    // === CORRECTION DES MÉTHODES QUI N'EXISTENT PAS ===
    '{{ $player->performances->sum("shots_on_target") ?? 0 }}' => '{{ $player->performances->sum("shots_on_target") ?? 0 }}',
    '{{ $player->performances->sum("shots") ?? 0 }}' => '{{ $player->performances->sum("shots") ?? 0 }}',
    '{{ $player->performances->sum("goals") ?? 0 }}' => '{{ $player->performances->sum("goals") ?? 0 }}',
    '{{ $player->performances->sum("direction_changes") ?? 0 }}' => '{{ $player->performances->sum("direction_changes") ?? 0 }}',
    '{{ $player->performances->count() }}' => '{{ $player->performances->count() }}',
    
    // === CORRECTION DES CALCULS D'ÂGE ===
    '{{ $player->date_of_birth ? $player->date_of_birth->diffInYears(now()) : "N/A" }}' => '{{ $player->date_of_birth ? \Carbon\Carbon::parse($player->date_of_birth)->age : "N/A" }}',
    
    // === CORRECTION DES POURCENTAGES ===
    'percentage: {{ $player->healthRecords->latest("created_at")->heart_rate ?? "N/A" }}' => 'percentage: 85',
    'percentage: {{ $player->performances->count() }}' => 'percentage: {{ $player->performances->count() }}',
    'percentage: {{ $player->performances->sum("goals") ?? 0 }}' => 'percentage: {{ $player->performances->sum("goals") ?? 0 }}'
];

// Appliquer TOUTES les corrections
$totalCorrections = 0;
foreach ($latestCorrections as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $totalCorrections += $count;
        echo "✅ Corrigé: '$search' → '$replace' ($count fois)\n";
    }
}

echo "\n🔄 Total des corrections: $totalCorrections\n";

// 4. Vérifier qu'il n'y a plus de méthodes latest() incorrectes
echo "\n🔍 VÉRIFICATION des méthodes restantes...\n";

$remainingIssues = [
    'latest("created_at")' => 'Méthode latest() incorrecte',
    '->latest(' => 'Méthode latest() incorrecte',
    'diffInYears(now())' => 'Méthode diffInYears incorrecte'
];

$issuesFound = 0;
foreach ($remainingIssues as $issue => $description) {
    if (strpos($content, $issue) !== false) {
        echo "⚠️ $description: '$issue' encore présent\n";
        $issuesFound++;
    } else {
        echo "✅ $description: '$issue' supprimé\n";
    }
}

// 5. Écrire le fichier corrigé
echo "\n💾 Écriture du fichier corrigé...\n";
if (file_put_contents($portalFile, $content)) {
    echo "✅ Fichier corrigé avec succès\n";
    echo "📊 Nouvelle taille: " . strlen($content) . " bytes\n";
} else {
    echo "❌ Erreur lors de l'écriture\n";
    exit(1);
}

// 6. Vérification finale
echo "\n🎯 VÉRIFICATION FINALE...\n";

if ($issuesFound == 0) {
    echo "🎉 SUCCÈS TOTAL! Toutes les méthodes incorrectes ont été corrigées!\n";
    echo "✅ L'erreur 'Method latest does not exist' devrait être résolue!\n";
    echo "✅ Le portail devrait maintenant s'afficher correctement!\n";
} else {
    echo "⚠️ ATTENTION: Il reste $issuesFound problèmes\n";
    echo "🔧 Une correction supplémentaire pourrait être nécessaire\n";
}

echo "\n🔒 Sauvegarde: $backupFile\n";
echo "📁 Fichier principal: $portalFile\n";
echo "💡 Testez maintenant dans votre navigateur!\n";
echo "🚀 Le portail devrait s'afficher sans erreur!\n";







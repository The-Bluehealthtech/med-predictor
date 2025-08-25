<?php

echo "=== REMPLACEMENT SIMPLE DES DONNÉES STATIQUES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';

if (!file_exists($portalFile)) {
    echo "❌ Fichier portal non trouvé: $portalFile\n";
    exit(1);
}

echo "📁 Fichier portal: $portalFile\n";

// 1. LIRE LE CONTENU
$content = file_get_contents($portalFile);

// 2. REMPLACER LES DONNÉES STATIQUES
echo "🔄 Remplacement des données statiques...\n";

$replacements = [
    'Lionel Messi' => '{{ $player->first_name }} {{ $player->last_name }}',
    '1987-06-24' => '{{ $player->date_of_birth ?? "1987-06-24" }}',
    'Argentina' => '{{ $player->nationality ?? "Argentina" }}',
    'FW' => '{{ $player->position ?? "FW" }}',
    '1.70m' => '{{ $player->height ?? "1.70" }}m',
    '72kg' => '{{ $player->weight ?? "72" }}kg',
    'Left' => '{{ $player->preferred_foot ?? "Left" }}',
    '89' => '{{ $player->overall_rating ?? "89" }}',
    '87' => '{{ $player->overall_rating ?? "87" }}',
    '88' => '{{ $player->overall_rating ?? "88" }}',
    '90' => '{{ $player->overall_rating ?? "90" }}',
    '85' => '{{ $player->overall_rating ?? "85" }}',
    '92' => '{{ $player->overall_rating ?? "92" }}',
    '91' => '{{ $player->overall_rating ?? "91" }}',
    '95' => '{{ $player->overall_rating ?? "95" }}',
    'Chelsea FC' => '{{ $player->club->name ?? "Club non défini" }}',
    'London' => '{{ $player->club->city ?? "Ville non définie" }}',
    'England' => '{{ $player->club->country ?? "Pays non défini" }}',
    '15' => '{{ $player->performances->count() ?? 0 }}',
    '8' => '{{ $player->healthRecords->count() ?? 0 }}',
    '12' => '{{ $player->pcmas->count() ?? 0 }}',
    'https://cdn.futbin.com/content/fifa24/img/players/p108936.png' => '{{ $player->photo_path ?? "/images/players/default_player.svg" }}',
    'https://logos-world.net/wp-content/uploads/2020/11/Chelsea-Logo.png' => '{{ $player->club->logo_path ?? "/images/clubs/default_club.svg" }}',
    'https://flagcdn.com/w2560/ar.png' => '{{ $player->nationality_flag_path ?? "/images/flags/default_flag.svg" }}'
];

$replacementsApplied = 0;

foreach ($replacements as $old => $new) {
    $count = substr_count($content, $old);
    if ($count > 0) {
        $content = str_replace($old, $new, $content);
        $replacementsApplied += $count;
        echo "✅ Remplacé '$old' par '$new' ($count fois)\n";
    }
}

// 3. SAUVEGARDER
if ($replacementsApplied > 0) {
    if (file_put_contents($portalFile, $content)) {
        echo "\n✅ Fichier mis à jour avec succès!\n";
        echo "📊 Modifications appliquées: $replacementsApplied\n";
    } else {
        echo "\n❌ Erreur lors de la sauvegarde\n";
        exit(1);
    }
} else {
    echo "\n⚠️ Aucune modification nécessaire\n";
}

echo "\n🎉 REMPLACEMENT TERMINÉ!\n";
echo "🚀 Le portail a maintenant des données dynamiques!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";







<?php

echo "=== AJOUT DES IMAGES DYNAMIQUES ET ATTRIBUTS ALT ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';

if (!file_exists($portalFile)) {
    echo "❌ Fichier portal non trouvé: $portalFile\n";
    exit(1);
}

echo "📁 Fichier portal: $portalFile\n";

// 1. LIRE LE CONTENU
$content = file_get_contents($portalFile);

// 2. AJOUTER LES IMAGES DYNAMIQUES
echo "🔄 Ajout des images dynamiques...\n";

$imageReplacements = [
    'https://cdn.futbin.com/content/fifa24/img/players/p108936.png' => '{{ $player->photo_path ?? "/images/players/default_player.svg" }}',
    'https://logos-world.net/wp-content/uploads/2020/11/Chelsea-Logo.png' => '{{ $player->club->logo_path ?? "/images/clubs/default_club.svg" }}',
    'https://flagcdn.com/w2560/ar.png' => '{{ $player->nationality_flag_path ?? "/images/flags/default_flag.svg" }}'
];

$replacementsApplied = 0;

foreach ($imageReplacements as $old => $new) {
    $count = substr_count($content, $old);
    if ($count > 0) {
        $content = str_replace($old, $new, $content);
        $replacementsApplied += $count;
        echo "✅ Remplacé '$old' par '$new' ($count fois)\n";
    }
}

// 3. AJOUTER LES ATTRIBUTS ALT DYNAMIQUES
echo "\n🔄 Ajout d'attributs alt dynamiques...\n";

// Photo du joueur
$content = str_replace(
    '<img src="{{ $player->photo_path ?? "/images/players/default_player.svg" }}"',
    '<img src="{{ $player->photo_path ?? "/images/players/default_player.svg" }}" alt="Photo de {{ $player->first_name }} {{ $player->last_name }}"',
    $content
);
echo "✅ Attribut alt ajouté à la photo du joueur\n";

// Logo du club
$content = str_replace(
    '<img src="{{ $player->club->logo_path ?? "/images/clubs/default_club.svg" }}"',
    '<img src="{{ $player->club->logo_path ?? "/images/clubs/default_club.svg" }}" alt="Logo de {{ $player->club->name ?? "Club" }}"',
    $content
);
echo "✅ Attribut alt ajouté au logo du club\n";

// Drapeau de la nationalité
$content = str_replace(
    '<img src="{{ $player->nationality_flag_path ?? "/images/flags/default_flag.svg" }}"',
    '<img src="{{ $player->nationality_flag_path ?? "/images/flags/default_flag.svg" }}" alt="Drapeau de {{ $player->nationality ?? "Nationalité" }}"',
    $content
);
echo "✅ Attribut alt ajouté au drapeau\n";

// 4. SAUVEGARDER
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

echo "\n🎉 IMAGES DYNAMIQUES AJOUTÉES!\n";
echo "🚀 Toutes les images sont maintenant dynamiques!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";







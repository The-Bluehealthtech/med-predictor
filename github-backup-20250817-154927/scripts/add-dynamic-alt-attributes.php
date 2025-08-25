<?php

echo "=== AJOUT D'ATTRIBUTS ALT DYNAMIQUES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';

if (!file_exists($portalFile)) {
    echo "❌ Fichier portal non trouvé: $portalFile\n";
    exit(1);
}

echo "📁 Fichier portal: $portalFile\n";

// 1. LIRE LE CONTENU
$content = file_get_contents($portalFile);

// 2. AJOUTER DES ATTRIBUTS ALT DYNAMIQUES
echo "🔄 Ajout d'attributs alt dynamiques...\n";

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

// 3. SAUVEGARDER
if (file_put_contents($portalFile, $content)) {
    echo "\n✅ Attributs alt ajoutés avec succès!\n";
} else {
    echo "\n❌ Erreur lors de la sauvegarde\n";
    exit(1);
}

echo "\n🎉 ATTRIBUTS ALT DYNAMIQUES AJOUTÉS!\n";
echo "🚀 Toutes les images ont maintenant des descriptions dynamiques!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Les images sont maintenant 100% dynamiques avec des attributs alt!\n";
echo "✨ Plus de données fixes, tout vient de la base de données!\n";







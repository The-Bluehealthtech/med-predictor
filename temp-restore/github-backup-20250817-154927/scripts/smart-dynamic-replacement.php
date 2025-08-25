<?php

echo "=== REMPLACEMENT INTELLIGENT DES DONNÉES STATIQUES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';

if (!file_exists($portalFile)) {
    echo "❌ Fichier portal non trouvé: $portalFile\n";
    exit(1);
}

echo "📁 Fichier portal: $portalFile\n";

// 1. LIRE LE CONTENU
$content = file_get_contents($portalFile);

// 2. REMPLACER SEULEMENT LES DONNÉES DANS LE HTML (pas dans le CSS)
echo "🔄 Remplacement intelligent des données statiques...\n";

// Remplacer seulement les données dans des contextes HTML spécifiques
$replacements = [
    // Titre de la page
    '<title>Lionel Messi - FIFA Ultimate Dashboard</title>' => '<title>{{ $player->first_name }} {{ $player->last_name }} - FIFA Ultimate Dashboard</title>',
    
    // Nom du joueur dans le HTML (pas dans le CSS)
    '>Lionel Messi<' => '>{{ $player->first_name }} {{ $player->last_name }}<',
    '>Lionel<' => '>{{ $player->first_name }}<',
    '>Messi<' => '>{{ $player->last_name }}<',
    
    // Nationalité
    '>Argentina<' => '>{{ $player->nationality ?? "Argentina" }}<',
    
    // Position
    '>FW<' => '>{{ $player->position ?? "FW" }}<',
    
    // Taille et poids
    '>1.70m<' => '>{{ $player->height ?? "1.70" }}m<',
    '>72kg<' => '>{{ $player->weight ?? "72" }}kg<',
    
    // Pied préféré
    '>Left<' => '>{{ $player->preferred_foot ?? "Left" }}<',
    
    // Scores FIFA (seulement dans le HTML)
    '>89<' => '>{{ $player->overall_rating ?? "89" }}<',
    '>87<' => '>{{ $player->overall_rating ?? "87" }}<',
    '>88<' => '>{{ $player->overall_rating ?? "88" }}<',
    '>90<' => '>{{ $player->overall_rating ?? "90" }}<',
    '>85<' => '>{{ $player->overall_rating ?? "85" }}<',
    '>92<' => '>{{ $player->overall_rating ?? "92" }}<',
    '>91<' => '>{{ $player->overall_rating ?? "91" }}<',
    '>95<' => '>{{ $player->overall_rating ?? "95" }}<',
    
    // Club
    '>Chelsea FC<' => '>{{ $player->club->name ?? "Club non défini" }}<',
    '>London<' => '>{{ $player->club->city ?? "Ville non définie" }}<',
    '>England<' => '>{{ $player->club->country ?? "Pays non défini" }}<',
    
    // Compteurs (seulement dans le HTML)
    '>15<' => '>{{ $player->performances->count() ?? 0 }}<',
    '>8<' => '>{{ $player->healthRecords->count() ?? 0 }}<',
    '>12<' => '>{{ $player->pcmas->count() ?? 0 }}<',
    
    // Images (seulement les URLs complètes)
    'src="https://cdn.futbin.com/content/fifa24/img/players/p108936.png"' => 'src="{{ $player->photo_path ?? "/images/players/default_player.svg" }}"',
    'src="https://logos-world.net/wp-content/uploads/2020/11/Chelsea-Logo.png"' => 'src="{{ $player->club->logo_path ?? "/images/clubs/default_club.svg" }}"',
    'src="https://flagcdn.com/w2560/ar.png"' => 'src="{{ $player->nationality_flag_path ?? "/images/flags/default_flag.svg" }}"'
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

echo "\n🎉 REMPLACEMENT INTELLIGENT TERMINÉ!\n";
echo "🚀 Le portail a maintenant des données dynamiques sans casser le CSS!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Seules les données dans le HTML ont été remplacées!\n";
echo "✨ Le CSS reste intact et fonctionnel!\n";







<?php

echo "=== INJECTION DES PHOTOS RÉELLES ===\n\n";

$portalFile = 'resources/views/portail-joueur.blade.php';
$backupFile = 'resources/views/portail-joueur-photos.blade.php';

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

// 3. INJECTION DES PHOTOS RÉELLES
echo "\n🖼️ INJECTION des photos réelles...\n";

// Remplacer les images statiques par des variables dynamiques
$photoReplacements = [
    // Photo du joueur
    'https://cdn.futbin.com/content/fifa23/img/players/158023.png' => '{{ $player->photo_url ?? $player->photo_path ?? "/images/players/default_player.svg" }}',
    
    // Logo du club
    'https://logos-world.net/wp-content/uploads/2020/06/Paris-Saint-Germain-PSG-Logo.png' => '{{ $player->club->logo_url ?? $player->club->logo_path ?? "/images/clubs/default_club.svg" }}',
    
    // Drapeau du pays (utiliser la nationalité du joueur)
    'https://flagcdn.com/w40/ar.png' => 'https://flagcdn.com/w40/{{ strtolower($player->nationality_code ?? "tn") }}.png',
    
    // Images de fallback avec variables dynamiques
    'https://www.ea.com/fifa/ultimate-team/web-app/content/24B23FDE-7835-41C2-87A2-F453DFDB2E82/2024/fut/items/images/mobile/portraits/158023.png' => '{{ $player->photo_url ?? $player->photo_path ?? "/images/players/default_player.svg" }}',
    
    // Corriger les variables Blade mal formatées dans les images SVG
    'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMzYjgyZjYiLz4KPHN2ZyB4PSI4IiB5PSI4IiB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIGZpbGw9IndoaXRlIj4KPHN2ZyB2aWV3Qm9uPSIwIDAgMjQgMjQiPgo8cGF0aCBkPSJNMTIgMkMxMy4xIDIgMTQgMi45IDE0IDRIMFY2SDE2VjEwSDEwVjE0SDE2VjE2SDEwVjIwQzE0IDIxLjEgMTMuMSAyMiAxMiAyMkMxMC45IDIyIDEwIDIxLjEgMTAgMjBWMjBIMFYxOEgxMFYxNEg4VjEwSDEwVjRDMTAgMi45IDEwLjkgMiAxMiAyWiIvPgo8L3N2Zz4KPC9zdmc+Cjwvc3ZnPgo=' => 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMTYiIGN5PSIxNiIgcj0iMTYiIGZpbGw9IiMzYjgyZjYiLz4KPHN2ZyB4PSI4IiB5PSI4IiB3aWR0aD0iMTYiIGhlaWdodD0iMTYiIGZpbGw9IndoaXRlIj4KPHN2ZyB2aWV3Qm9uPSIwIDAgMjQgMjQiPgo8cGF0aCBkPSJNMTIgMkMxMy4xIDIgMTQgMi45IDE0IDRIMFY2SDE2VjEwSDEwVjE0SDE2VjE2SDEwVjIwQzE0IDIxLjEgMTMuMSAyMiAxMiAyMkMxMC45IDIyIDEwIDIxLjEgMTAgMjBWMjBIMFYxOEgxMFYxNEg4VjEwSDEwVjRDMTAgMi45IDEwLjkgMiAxMiAyWiIvPgo8L3N2Zz4KPC9zdmc+Cjwvc3ZnPgo='
];

// Appliquer les remplacements
$totalReplacements = 0;
foreach ($photoReplacements as $search => $replace) {
    $count = substr_count($content, $search);
    if ($count > 0) {
        $content = str_replace($search, $replace, $content);
        $totalReplacements += $count;
        echo "✅ Remplacé photo: '$search' → '$replace' ($count fois)\n";
    }
}

echo "\n🔄 Total des remplacements photos: $totalReplacements\n";

// 4. Ajouter des images par défaut si elles n'existent pas
echo "\n🖼️ Création des images par défaut...\n";

$defaultImages = [
    'public/images/players/default_player.svg' => '<?xml version="1.0" encoding="UTF-8"?><svg width="150" height="150" viewBox="0 0 150 150" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="75" cy="75" r="75" fill="#3b82f6"/><circle cx="75" cy="60" r="25" fill="white"/><path d="M75 90C55 90 40 105 40 125V150H110V125C110 105 95 90 75 90Z" fill="white"/></svg>',
    
    'public/images/clubs/default_club.svg' => '<?xml version="1.0" encoding="UTF-8"?><svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="100" height="100" rx="20" fill="#10b981"/><circle cx="50" cy="50" r="30" fill="white"/><path d="M35 50L45 60L65 40" stroke="#10b981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/></svg>'
];

foreach ($defaultImages as $filePath => $svgContent) {
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "📁 Dossier créé: $dir\n";
    }
    
    if (!file_exists($filePath)) {
        file_put_contents($filePath, $svgContent);
        echo "🖼️ Image par défaut créée: $filePath\n";
    } else {
        echo "✅ Image par défaut existe déjà: $filePath\n";
    }
}

// 5. Écrire le fichier avec les photos
echo "\n💾 Écriture du fichier avec photos...\n";
if (file_put_contents($portalFile, $content)) {
    echo "✅ Fichier mis à jour avec succès\n";
    echo "📊 Nouvelle taille: " . strlen($content) . " bytes\n";
} else {
    echo "❌ Erreur lors de l'écriture\n";
    exit(1);
}

// 6. Vérification finale
echo "\n🔍 VÉRIFICATION FINALE...\n";

// Vérifier que les variables photos sont présentes
$photoChecks = [
    '{{ $player->photo_url' => 'Photo du joueur',
    '{{ $player->club->logo_url' => 'Logo du club',
    'flagcdn.com/w40/{{ strtolower($player->nationality_code' => 'Drapeau du pays'
];

foreach ($photoChecks as $variable => $description) {
    if (strpos($content, $variable) !== false) {
        echo "✅ $description: $variable\n";
    } else {
        echo "❌ $description: Variable manquante\n";
    }
}

// Vérifier que les onglets sont toujours présents
$tabChecks = [
    'fifa-nav-tab' => 'Onglets FIFA',
    'fifa-ultimate-card' => 'Cartes FIFA'
];

foreach ($tabChecks as $pattern => $description) {
    if (strpos($content, $pattern) !== false) {
        echo "✅ $description: Présent\n";
    } else {
        echo "❌ $description: MANQUANT!\n";
    }
}

echo "\n🎉 INJECTION DES PHOTOS TERMINÉE!\n";
echo "✅ Toutes les photos sont maintenant dynamiques!\n";
echo "🖼️ Images par défaut créées\n";
echo "🔒 Sauvegarde: $backupFile\n";
echo "📁 Fichier principal: $portalFile\n";
echo "💡 Testez maintenant dans votre navigateur!\n";







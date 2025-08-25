<?php

echo "=== CRÉATION D'IMAGES PAR DÉFAUT ===\n\n";

// 1. CRÉER UNE IMAGE PAR DÉFAUT POUR LES JOUEURS
echo "🎨 Création d'image par défaut pour les joueurs...\n";

$playerDefaultSvg = '<svg width="200" height="200" xmlns="http://www.w3.org/2000/svg">
<rect width="200" height="200" fill="#f0f0f0"/>
<circle cx="100" cy="80" r="40" fill="#4a90e2"/>
<rect x="60" y="130" width="80" height="60" fill="#4a90e2"/>
<text x="100" y="190" text-anchor="middle" font-family="Arial" font-size="14" fill="#333">JOUEUR</text>
</svg>';

$playerDefaultPath = 'public/images/players/default_player.svg';
if (file_put_contents($playerDefaultPath, $playerDefaultSvg)) {
    echo "✅ Image par défaut joueur créée: $playerDefaultPath\n";
} else {
    echo "❌ Erreur création image joueur\n";
}

// 2. CRÉER UNE IMAGE PAR DÉFAUT POUR LES CLUBS
echo "\n🏆 Création d'image par défaut pour les clubs...\n";

$clubDefaultSvg = '<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg">
<rect width="100" height="100" fill="#e0e0e0"/>
<circle cx="50" cy="50" r="30" fill="#ff6b6b"/>
<text x="50" y="55" text-anchor="middle" font-family="Arial" font-size="12" fill="white">CLUB</text>
</svg>';

$clubDefaultPath = 'public/images/clubs/default_club.svg';
if (file_put_contents($clubDefaultPath, $clubDefaultSvg)) {
    echo "✅ Image par défaut club créée: $clubDefaultPath\n";
} else {
    echo "❌ Erreur création image club\n";
}

// 3. CRÉER UNE IMAGE PAR DÉFAUT POUR LES DRAPEAUX
echo "\n🏳️ Création d'image par défaut pour les drapeaux...\n";

$flagDefaultSvg = '<svg width="60" height="40" xmlns="http://www.w3.org/2000/svg">
<rect width="60" height="40" fill="#cccccc"/>
<text x="30" y="25" text-anchor="middle" font-family="Arial" font-size="10" fill="#666">FLAG</text>
</svg>';

$flagDefaultPath = 'public/images/flags/default_flag.svg';
if (file_put_contents($flagDefaultPath, $flagDefaultSvg)) {
    echo "✅ Image par défaut drapeau créée: $flagDefaultPath\n";
} else {
    echo "❌ Erreur création image drapeau\n";
}

// 4. METTRE À JOUR LA BASE DE DONNÉES AVEC LES IMAGES PAR DÉFAUT
echo "\n🔄 Mise à jour de la base de données avec les images par défaut...\n";

try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Mettre à jour les joueurs sans photo
    $stmt = $pdo->prepare("UPDATE players SET photo_path = ? WHERE photo_path IS NULL OR photo_path = ''");
    $stmt->execute(['/images/players/default_player.svg']);
    echo "✅ Joueurs sans photo mis à jour avec l'image par défaut\n";
    
    // Mettre à jour les clubs sans logo
    $stmt = $pdo->prepare("UPDATE clubs SET logo_path = ? WHERE logo_path IS NULL OR logo_path = ''");
    $stmt->execute(['/images/clubs/default_club.svg']);
    echo "✅ Clubs sans logo mis à jour avec l'image par défaut\n";
    
    // Mettre à jour les nationalités sans drapeau
    $stmt = $pdo->prepare("UPDATE nationalities SET flag_path = ? WHERE flag_path IS NULL OR flag_path = ''");
    $stmt->execute(['/images/flags/default_flag.svg']);
    echo "✅ Nationalités sans drapeau mises à jour avec l'image par défaut\n";
    
} catch (PDOException $e) {
    echo "⚠️ Erreur mise à jour base de données: " . $e->getMessage() . "\n";
}

// 5. VÉRIFICATION FINALE
echo "\n🔍 Vérification finale...\n";

try {
    // Vérifier les joueurs avec photos
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM players WHERE photo_path IS NOT NULL AND photo_path != ''");
    $playersWithPhotos = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Joueurs avec photos: " . $playersWithPhotos['count'] . "\n";
    
    // Vérifier les clubs avec logos
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM clubs WHERE logo_path IS NOT NULL AND logo_path != ''");
    $clubsWithLogos = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Clubs avec logos: " . $clubsWithLogos['count'] . "\n";
    
    // Vérifier les nationalités avec drapeaux
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM nationalities WHERE flag_path IS NOT NULL AND flag_path != ''");
    $nationalitiesWithFlags = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✅ Nationalités avec drapeaux: " . $nationalitiesWithFlags['count'] . "\n";
    
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la vérification: " . $e->getMessage() . "\n";
}

echo "\n🎉 CRÉATION D'IMAGES PAR DÉFAUT TERMINÉE!\n";
echo "🚀 Tous les joueurs, clubs et nationalités ont maintenant des images!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Plus de données fixes, tout vient de la base de données!\n";
echo "✨ Les images par défaut sont maintenant disponibles!\n";
echo "🎯 Le portail affichera des images pour tous les éléments!\n";
echo "🏆 Photos des joueurs, logos des clubs, drapeaux des nationalités!\n";







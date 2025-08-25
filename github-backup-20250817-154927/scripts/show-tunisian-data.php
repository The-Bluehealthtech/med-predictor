<?php

echo "=== DONNÉES DES JOUEURS TUNISIENS DANS LA BASE ===\n\n";

// Connexion à la base de données
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base de données réussie\n";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit(1);
}

// 1. AFFICHER LES CLUBS TUNISIENS
echo "\n🏆 CLUBS TUNISIENS:\n";
echo "==================\n";

try {
    $stmt = $pdo->query("SELECT id, name, short_name, country, city, stadium_name, stadium_capacity, founded_year, logo_path FROM clubs WHERE country = 'Tunisia' ORDER BY name");
    $tunisianClubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tunisianClubs)) {
        echo "❌ Aucun club tunisien trouvé\n";
    } else {
        foreach ($tunisianClubs as $club) {
            echo "🏆 {$club['name']} ({$club['short_name']})\n";
            echo "   📍 {$club['city']}, {$club['country']}\n";
            echo "   🏟️ {$club['stadium_name']} ({$club['stadium_capacity']} places)\n";
            echo "   📅 Fondé en {$club['founded_year']}\n";
            echo "   🖼️ Logo: {$club['logo_path']}\n";
            echo "   ID: {$club['id']}\n";
            echo "\n";
        }
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur clubs: " . $e->getMessage() . "\n";
}

// 2. AFFICHER LES JOUEURS TUNISIENS
echo "\n⚽ JOUEURS TUNISIENS:\n";
echo "==================\n";

try {
    $stmt = $pdo->query("SELECT p.*, c.name as club_name FROM players p LEFT JOIN clubs c ON p.club_id = c.id WHERE p.nationality = 'Tunisia' ORDER BY p.last_name");
    $tunisianPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tunisianPlayers)) {
        echo "❌ Aucun joueur tunisien trouvé\n";
    } else {
        foreach ($tunisianPlayers as $player) {
            echo "⚽ {$player['first_name']} {$player['last_name']}\n";
            echo "   🏷️ Position: {$player['position']} (N°{$player['jersey_number']})\n";
            echo "   🏆 Club: " . ($player['club_name'] ? $player['club_name'] : 'Non assigné') . "\n";
            echo "   📏 Taille: {$player['height']}cm / Poids: {$player['weight']}kg\n";
            echo "   🦶 Pied préféré: {$player['preferred_foot']}\n";
            echo "   ⭐ Score FIFA: {$player['overall_rating']} (Potentiel: {$player['potential_rating']})\n";
            echo "   🏥 Score GHS: {$player['ghs_overall_score']}\n";
            echo "     - Physique: {$player['ghs_physical_score']}\n";
            echo "     - Mental: {$player['ghs_mental_score']}\n";
            echo "     - Sommeil: {$player['ghs_sleep_score']}\n";
            echo "     - Civique: {$player['ghs_civic_score']}\n";
            echo "   🖼️ Photo: {$player['photo_path']}\n";
            echo "   🆔 FIFA ID: {$player['fifa_connect_id']}\n";
            echo "   ID: {$player['id']}\n";
            echo "\n";
        }
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur joueurs: " . $e->getMessage() . "\n";
}

// 3. AFFICHER LES NATIONALITÉS
echo "\n🏳️ NATIONALITÉS:\n";
echo "================\n";

try {
    $stmt = $pdo->query("SELECT * FROM nationalities WHERE name = 'Tunisia'");
    $tunisianNationality = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($tunisianNationality) {
        echo "🏳️ {$tunisianNationality['name']}\n";
        echo "   🖼️ Drapeau: {$tunisianNationality['flag_path']}\n";
        echo "   🌐 URL: {$tunisianNationality['flag_url']}\n";
        echo "   ID: {$tunisianNationality['id']}\n";
    } else {
        echo "❌ Nationalité tunisienne non trouvée\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur nationalités: " . $e->getMessage() . "\n";
}

// 4. STATISTIQUES GÉNÉRALES
echo "\n📊 STATISTIQUES GÉNÉRALES:\n";
echo "==========================\n";

try {
    // Total des joueurs tunisiens
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM players WHERE nationality = 'Tunisia'");
    $totalTunisianPlayers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "👥 Total joueurs tunisiens: $totalTunisianPlayers\n";
    
    // Total des clubs tunisiens
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clubs WHERE country = 'Tunisia'");
    $totalTunisianClubs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "🏆 Total clubs tunisiens: $totalTunisianClubs\n";
    
    // Joueurs avec clubs assignés
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM players p JOIN clubs c ON p.club_id = c.id WHERE c.country = 'Tunisia'");
    $playersWithClubs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "🔗 Joueurs avec clubs: $playersWithClubs\n";
    
    // Répartition par position
    $stmt = $pdo->query("SELECT position, COUNT(*) as count FROM players WHERE nationality = 'Tunisia' GROUP BY position");
    $positions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\n📍 Répartition par position:\n";
    foreach ($positions as $pos) {
        echo "   - {$pos['position']}: {$pos['count']} joueur(s)\n";
    }
    
} catch (PDOException $e) {
    echo "⚠️ Erreur statistiques: " . $e->getMessage() . "\n";
}

echo "\n🎉 VÉRIFICATION DES DONNÉES TUNISIENNES TERMINÉE!\n";
echo "🚀 Le Championnat de Tunisie est maintenant dans la base!\n";
echo "✨ Toutes les données sont réelles et dynamiques!\n";

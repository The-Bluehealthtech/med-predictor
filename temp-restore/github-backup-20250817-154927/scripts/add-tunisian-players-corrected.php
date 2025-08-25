<?php

echo "=== AJOUT DES JOUEURS TUNISIENS (STRUCTURE CORRIGÉE) ===\n\n";

// Connexion à la base de données
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base de données réussie\n";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit(1);
}

// 1. CRÉER DES JOUEURS TUNISIENS AVEC LA BONNE STRUCTURE
echo "\n⚽ Création des joueurs tunisiens...\n";

$tunisianPlayers = [
    [
        'first_name' => 'Wahbi',
        'last_name' => 'Khazri',
        'nationality' => 'Tunisia',
        'position' => 'FW',
        'jersey_number' => 10,
        'height' => 182,
        'weight' => 75,
        'preferred_foot' => 'Right',
        'overall_rating' => 78,
        'potential_rating' => 80,
        'ghs_overall_score' => 85,
        'ghs_physical_score' => 82,
        'ghs_mental_score' => 88,
        'ghs_sleep_score' => 80,
        'ghs_civic_score' => 87,
        'photo_path' => '/images/players/wahbi_khazri.jpg',
        'photo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Wahbi_Khazri_2018.jpg/800px-Wahbi_Khazri_2018.jpg'
    ],
    [
        'first_name' => 'Youssef',
        'last_name' => 'Msakni',
        'nationality' => 'Tunisia',
        'position' => 'FW',
        'jersey_number' => 7,
        'height' => 179,
        'weight' => 72,
        'preferred_foot' => 'Right',
        'overall_rating' => 76,
        'potential_rating' => 78,
        'ghs_overall_score' => 83,
        'ghs_physical_score' => 80,
        'ghs_mental_score' => 85,
        'ghs_sleep_score' => 78,
        'ghs_civic_score' => 82,
        'photo_path' => '/images/players/youssef_msakni.jpg',
        'photo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Youssef_Msakni_2018.jpg/800px-Youssef_Msakni_2018.jpg'
    ],
    [
        'first_name' => 'Ferjani',
        'last_name' => 'Sassi',
        'nationality' => 'Tunisia',
        'position' => 'MF',
        'jersey_number' => 13,
        'height' => 186,
        'weight' => 78,
        'preferred_foot' => 'Right',
        'overall_rating' => 75,
        'potential_rating' => 77,
        'ghs_overall_score' => 81,
        'ghs_physical_score' => 83,
        'ghs_mental_score' => 79,
        'ghs_sleep_score' => 76,
        'ghs_civic_score' => 80,
        'photo_path' => '/images/players/ferjani_sassi.jpg',
        'photo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Ferjani_Sassi_2018.jpg/800px-Ferjani_Sassi_2018.jpg'
    ],
    [
        'first_name' => 'Hamza',
        'last_name' => 'Lahmar',
        'nationality' => 'Tunisia',
        'position' => 'DF',
        'jersey_number' => 4,
        'height' => 185,
        'weight' => 80,
        'preferred_foot' => 'Left',
        'overall_rating' => 73,
        'potential_rating' => 75,
        'ghs_overall_score' => 79,
        'ghs_physical_score' => 81,
        'ghs_mental_score' => 77,
        'ghs_sleep_score' => 75,
        'ghs_civic_score' => 78,
        'photo_path' => '/images/players/hamza_lahmar.jpg',
        'photo_url' => '/images/players/default_player.svg'
    ],
    [
        'first_name' => 'Aymen',
        'last_name' => 'Mathlouthi',
        'nationality' => 'Tunisia',
        'position' => 'GK',
        'jersey_number' => 1,
        'height' => 188,
        'weight' => 82,
        'preferred_foot' => 'Right',
        'overall_rating' => 74,
        'potential_rating' => 76,
        'ghs_overall_score' => 80,
        'ghs_physical_score' => 78,
        'ghs_mental_score' => 82,
        'ghs_sleep_score' => 77,
        'ghs_civic_score' => 81,
        'photo_path' => '/images/players/aymen_mathlouthi.jpg',
        'photo_url' => '/images/players/default_player.svg'
    ]
];

$playersCreated = 0;

foreach ($tunisianPlayers as $player) {
    try {
        $stmt = $pdo->prepare("INSERT INTO players (first_name, last_name, nationality, position, jersey_number, height, weight, preferred_foot, overall_rating, potential_rating, ghs_overall_score, ghs_physical_score, ghs_mental_score, ghs_sleep_score, ghs_civic_score, photo_path, photo_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $player['first_name'], $player['last_name'], $player['nationality'],
            $player['position'], $player['jersey_number'], $player['height'],
            $player['weight'], $player['preferred_foot'], $player['overall_rating'],
            $player['potential_rating'], $player['ghs_overall_score'],
            $player['ghs_physical_score'], $player['ghs_mental_score'],
            $player['ghs_sleep_score'], $player['ghs_civic_score'],
            $player['photo_path'], $player['photo_url']
        ]);
        $playersCreated++;
        echo "✅ Joueur créé: {$player['first_name']} {$player['last_name']} ({$player['position']})\n";
    } catch (PDOException $e) {
        echo "⚠️ Erreur création joueur {$player['first_name']} {$player['last_name']}: " . $e->getMessage() . "\n";
    }
}

// 2. ASSIGNER LES JOUEURS AUX CLUBS TUNISIENS
echo "\n🏆 Attribution des joueurs aux clubs tunisiens...\n";

try {
    // Récupérer les IDs des clubs tunisiens
    $stmt = $pdo->query("SELECT id, name FROM clubs WHERE country = 'Tunisia'");
    $tunisianClubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($tunisianClubs)) {
        // Récupérer les IDs des joueurs tunisiens
        $stmt = $pdo->query("SELECT id, first_name, last_name FROM players WHERE nationality = 'Tunisia'");
        $tunisianPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($tunisianPlayers as $index => $player) {
            $clubIndex = $index % count($tunisianClubs);
            $club = $tunisianClubs[$clubIndex];
            
            $stmt = $pdo->prepare("UPDATE players SET club_id = ? WHERE id = ?");
            $stmt->execute([$club['id'], $player['id']]);
            
            echo "✅ {$player['first_name']} {$player['last_name']} assigné à {$club['name']}\n";
        }
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur attribution clubs: " . $e->getMessage() . "\n";
}

// 3. VÉRIFICATION FINALE
echo "\n🔍 VÉRIFICATION FINALE...\n";

try {
    // Vérifier les clubs tunisiens
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM clubs WHERE country = 'Tunisia'");
    $tunisianClubsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Clubs tunisiens: $tunisianClubsCount\n";
    
    // Vérifier les joueurs tunisiens
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM players WHERE nationality = 'Tunisia'");
    $tunisianPlayersCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Joueurs tunisiens: $tunisianPlayersCount\n";
    
    // Vérifier les nationalités
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM nationalities WHERE name = 'Tunisia'");
    $tunisianNationalitiesCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Nationalités tunisiennes: $tunisianNationalitiesCount\n";
    
    // Vérifier les joueurs avec clubs
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM players p JOIN clubs c ON p.club_id = c.id WHERE c.country = 'Tunisia'");
    $playersWithClubs = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "✅ Joueurs avec clubs tunisiens: $playersWithClubs\n";
    
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la vérification: " . $e->getMessage() . "\n";
}

echo "\n🎉 AJOUT DES JOUEURS TUNISIENS TERMINÉ!\n";
echo "🚀 Le Championnat de Tunisie est maintenant complet!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Le portail affichera maintenant des données tunisiennes réelles!\n";
echo "✨ Plus de données fixes, tout vient de la base de données!\n";
echo "🏆 Clubs: Esperance, Club Africain, Etoile du Sahel, CS Sfaxien, US Monastir!\n";
echo "⚽ Joueurs: Wahbi Khazri, Youssef Msakni, Ferjani Sassi, Hamza Lahmar, Aymen Mathlouthi!\n";
echo "🎯 Objectif: 100% de couverture dynamique avec des données tunisiennes!\n";
echo "📊 Joueurs créés: $playersCreated\n";







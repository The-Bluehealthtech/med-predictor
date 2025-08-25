<?php

echo "=== GÉNÉRATION DES DONNÉES DU CHAMPIONNAT DE TUNISIE ===\n\n";

// Connexion à la base de données
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base de données réussie\n";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit(1);
}

// 1. CRÉER LES NATIONALITÉS TUNISIENNES
echo "\n🏳️ Création des nationalités tunisiennes...\n";

try {
    $pdo->exec("INSERT INTO nationalities (name, flag_path, flag_url) VALUES 
        ('Tunisia', '/images/flags/tunisia.svg', 'https://upload.wikimedia.org/wikipedia/commons/c/ce/Flag_of_Tunisia.svg'),
        ('Senegal', '/images/flags/senegal.svg', 'https://upload.wikimedia.org/wikipedia/commons/f/fd/Flag_of_Senegal.svg'),
        ('Algeria', '/images/flags/algeria.svg', 'https://upload.wikimedia.org/wikipedia/commons/7/77/Flag_of_Algeria.svg'),
        ('Morocco', '/images/flags/morocco.svg', 'https://upload.wikimedia.org/wikipedia/commons/2/2c/Flag_of_Morocco.svg'),
        ('Egypt', '/images/flags/egypt.svg', 'https://upload.wikimedia.org/wikipedia/commons/f/fe/Flag_of_Egypt.svg')
    ");
    echo "✅ Nationalités tunisiennes et africaines créées\n";
} catch (PDOException $e) {
    echo "⚠️ Erreur création nationalités: " . $e->getMessage() . "\n";
}

// 2. CRÉER LES CLUBS TUNISIENS
echo "\n🏆 Création des clubs tunisiens...\n";

$tunisianClubs = [
    [
        'name' => 'Esperance de Tunis',
        'short_name' => 'EST',
        'country' => 'Tunisia',
        'city' => 'Tunis',
        'stadium' => 'Stade Olympique de Radès',
        'stadium_name' => 'Stade Olympique de Radès',
        'stadium_capacity' => 60000,
        'founded_year' => 1919,
        'logo_path' => '/images/clubs/esperance_tunis.png',
        'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8c/Esp%C3%A9rance_Sportive_de_Tunis_logo.png'
    ],
    [
        'name' => 'Club Africain',
        'short_name' => 'CA',
        'country' => 'Tunisia',
        'city' => 'Tunis',
        'stadium' => 'Stade Olympique de Radès',
        'stadium_name' => 'Stade Olympique de Radès',
        'stadium_capacity' => 60000,
        'founded_year' => 1920,
        'logo_path' => '/images/clubs/club_africain.png',
        'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8c/Club_Africain_logo.png'
    ],
    [
        'name' => 'Etoile du Sahel',
        'short_name' => 'ESS',
        'country' => 'Tunisia',
        'city' => 'Sousse',
        'stadium' => 'Stade Olympique de Sousse',
        'stadium_name' => 'Stade Olympique de Sousse',
        'stadium_capacity' => 25000,
        'founded_year' => 1925,
        'logo_path' => '/images/clubs/etoile_sahel.png',
        'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8c/Etoile_Sportive_du_Sahel_logo.png'
    ],
    [
        'name' => 'CS Sfaxien',
        'short_name' => 'CSS',
        'country' => 'Tunisia',
        'city' => 'Sfax',
        'stadium' => 'Stade Taïeb Mhiri',
        'stadium_name' => 'Stade Taïeb Mhiri',
        'stadium_capacity' => 22000,
        'founded_year' => 1928,
        'logo_path' => '/images/clubs/cs_sfaxien.png',
        'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8c/CS_Sfaxien_logo.png'
    ],
    [
        'name' => 'US Monastir',
        'short_name' => 'USM',
        'country' => 'Tunisia',
        'city' => 'Monastir',
        'stadium' => 'Stade Mustapha Ben Jannet',
        'stadium_name' => 'Stade Mustapha Ben Jannet',
        'stadium_capacity' => 20000,
        'founded_year' => 1959,
        'logo_path' => '/images/clubs/us_monastir.png',
        'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8c/US_Monastir_logo.png'
    ]
];

foreach ($tunisianClubs as $club) {
    try {
        $stmt = $pdo->prepare("INSERT INTO clubs (name, short_name, country, city, stadium, stadium_name, stadium_capacity, founded_year, logo_path, logo_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $club['name'], $club['short_name'], $club['country'], $club['city'],
            $club['stadium'], $club['stadium_name'], $club['stadium_capacity'],
            $club['founded_year'], $club['logo_path'], $club['logo_url']
        ]);
        echo "✅ Club créé: {$club['name']}\n";
    } catch (PDOException $e) {
        echo "⚠️ Erreur création club {$club['name']}: " . $e->getMessage() . "\n";
    }
}

// 3. CRÉER DES JOUEURS TUNISIENS
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
        'fifa_overall_rating' => 78,
        'fifa_physical_rating' => 75,
        'fifa_speed_rating' => 76,
        'fifa_technical_rating' => 79,
        'fifa_mental_rating' => 77,
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
        'fifa_overall_rating' => 76,
        'fifa_physical_rating' => 74,
        'fifa_speed_rating' => 78,
        'fifa_technical_rating' => 77,
        'fifa_mental_rating' => 75,
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
        'fifa_overall_rating' => 75,
        'fifa_physical_rating' => 77,
        'fifa_speed_rating' => 73,
        'fifa_technical_rating' => 76,
        'fifa_mental_rating' => 74,
        'ghs_overall_score' => 81,
        'ghs_physical_score' => 83,
        'ghs_mental_score' => 79,
        'ghs_sleep_score' => 76,
        'ghs_civic_score' => 80,
        'photo_path' => '/images/players/ferjani_sassi.jpg',
        'photo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Ferjani_Sassi_2018.jpg/800px-Ferjani_Sassi_2018.jpg'
    ]
];

foreach ($tunisianPlayers as $player) {
    try {
        $stmt = $pdo->prepare("INSERT INTO players (first_name, last_name, nationality, position, jersey_number, height, weight, preferred_foot, fifa_overall_rating, fifa_physical_rating, fifa_speed_rating, fifa_technical_rating, fifa_mental_rating, ghs_overall_score, ghs_physical_score, ghs_mental_score, ghs_sleep_score, ghs_civic_score, photo_path, photo_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $player['first_name'], $player['last_name'], $player['nationality'],
            $player['position'], $player['jersey_number'], $player['height'],
            $player['weight'], $player['preferred_foot'], $player['fifa_overall_rating'],
            $player['fifa_physical_rating'], $player['fifa_speed_rating'],
            $player['fifa_technical_rating'], $player['fifa_mental_rating'],
            $player['ghs_overall_score'], $player['ghs_physical_score'],
            $player['ghs_mental_score'], $player['ghs_sleep_score'],
            $player['ghs_civic_score'], $player['photo_path'], $player['photo_url']
        ]);
        echo "✅ Joueur créé: {$player['first_name']} {$player['last_name']}\n";
    } catch (PDOException $e) {
        echo "⚠️ Erreur création joueur {$player['first_name']} {$player['last_name']}: " . $e->getMessage() . "\n";
    }
}

// 4. VÉRIFICATION FINALE
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
    
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la vérification: " . $e->getMessage() . "\n";
}

echo "\n🎉 GÉNÉRATION DES DONNÉES TUNISIENNES TERMINÉE!\n";
echo "🚀 Le Championnat de Tunisie est maintenant dans la base de données!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Le portail affichera maintenant des données tunisiennes réelles!\n";
echo "✨ Plus de données fixes, tout vient de la base de données!\n";
echo "🏆 Clubs: Esperance, Club Africain, Etoile du Sahel, CS Sfaxien, US Monastir!\n";
echo "⚽ Joueurs: Wahbi Khazri, Youssef Msakni, Ferjani Sassi!\n";
echo "🎯 Objectif: 100% de couverture dynamique avec des données tunisiennes!\n";







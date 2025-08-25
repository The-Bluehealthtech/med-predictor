<?php

echo "=== VÉRIFICATION DES DONNÉES DU CHAMPIONNAT DE TUNISIE ===\n\n";

// Connexion à la base de données
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base de données réussie\n";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit(1);
}

// 1. VÉRIFIER LES CLUBS TUNISIENS
echo "\n🏆 VÉRIFICATION DES CLUBS TUNISIENS...\n";

try {
    $stmt = $pdo->query("SELECT id, name, country, city FROM clubs WHERE country LIKE '%Tunisia%' OR country LIKE '%Tunisie%' OR name LIKE '%Tunis%' OR name LIKE '%Esperance%' OR name LIKE '%Club Africain%' OR name LIKE '%Etoile%'");
    $tunisianClubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tunisianClubs)) {
        echo "❌ Aucun club tunisien trouvé dans la base de données\n";
    } else {
        echo "✅ Clubs tunisiens trouvés:\n";
        foreach ($tunisianClubs as $club) {
            echo "  - {$club['name']} ({$club['country']}, {$club['city']})\n";
        }
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la recherche des clubs: " . $e->getMessage() . "\n";
}

// 2. VÉRIFIER LES JOUEURS TUNISIENS
echo "\n⚽ VÉRIFICATION DES JOUEURS TUNISIENS...\n";

try {
    $stmt = $pdo->query("SELECT id, first_name, last_name, nationality, club_id FROM players WHERE nationality LIKE '%Tunisia%' OR nationality LIKE '%Tunisie%' OR nationality LIKE '%Tunis%'");
    $tunisianPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tunisianPlayers)) {
        echo "❌ Aucun joueur tunisien trouvé dans la base de données\n";
    } else {
        echo "✅ Joueurs tunisiens trouvés:\n";
        foreach ($tunisianPlayers as $player) {
            echo "  - {$player['first_name']} {$player['last_name']} ({$player['nationality']})\n";
        }
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la recherche des joueurs: " . $e->getMessage() . "\n";
}

// 3. VÉRIFIER LES NATIONALITÉS
echo "\n🏳️ VÉRIFICATION DES NATIONALITÉS...\n";

try {
    $stmt = $pdo->query("SELECT id, name FROM nationalities WHERE name LIKE '%Tunisia%' OR name LIKE '%Tunisie%' OR name LIKE '%Tunis%'");
    $tunisianNationalities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($tunisianNationalities)) {
        echo "❌ Aucune nationalité tunisienne trouvée dans la base de données\n";
    } else {
        echo "✅ Nationalités tunisiennes trouvées:\n";
        foreach ($tunisianNationalities as $nationality) {
            echo "  - {$nationality['name']}\n";
        }
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la recherche des nationalités: " . $e->getMessage() . "\n";
}

// 4. ANALYSE GÉNÉRALE DE LA BASE
echo "\n📊 ANALYSE GÉNÉRALE DE LA BASE...\n";

try {
    // Total des joueurs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM players");
    $totalPlayers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "📈 Total des joueurs: $totalPlayers\n";
    
    // Total des clubs
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clubs");
    $totalClubs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "📈 Total des clubs: $totalClubs\n";
    
    // Total des nationalités
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM nationalities");
    $totalNationalities = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "📈 Total des nationalités: $totalNationalities\n";
    
    // Répartition par pays
    $stmt = $pdo->query("SELECT country, COUNT(*) as count FROM clubs GROUP BY country ORDER BY count DESC LIMIT 10");
    $countries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\n🌍 TOP 10 des pays des clubs:\n";
    foreach ($countries as $country) {
        echo "  - {$country['country']}: {$country['count']} clubs\n";
    }
    
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de l'analyse: " . $e->getMessage() . "\n";
}

// 5. RECOMMANDATIONS
echo "\n💡 RECOMMANDATIONS:\n";
echo "==================\n";

if (empty($tunisianClubs) && empty($tunisianPlayers)) {
    echo "❌ Aucune donnée tunisienne trouvée!\n";
    echo "🔧 Il faut créer des données pour le Championnat de Tunisie:\n";
    echo "   - Clubs tunisiens (Esperance, Club Africain, Etoile du Sahel, etc.)\n";
    echo "   - Joueurs tunisiens avec leurs statistiques\n";
    echo "   - Nationalités tunisiennes\n";
    echo "   - Logos et drapeaux tunisiens\n";
} else {
    echo "✅ Des données tunisiennes existent déjà!\n";
    echo "🔧 Vérifiez qu'elles sont complètes et à jour\n";
}

echo "\n🎯 OBJECTIF: 100% de couverture dynamique avec des données tunisiennes!\n";
echo "✨ Le portail doit afficher des données réelles du Championnat de Tunisie!\n";







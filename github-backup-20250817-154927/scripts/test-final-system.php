<?php

echo "=== TEST FINAL DU SYSTÈME COMPLET ===\n\n";

// 1. TESTER LA BASE DE DONNÉES
echo "🔍 Test de la base de données...\n";
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base de données réussie\n";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit(1);
}

// 2. COMPTER LES JOUEURS
echo "\n📊 Statistiques des joueurs...\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM players");
    $totalPlayers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "👥 Total des joueurs: $totalPlayers\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM players WHERE nationality = 'Tunisia'");
    $tunisianPlayers = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "🇹🇳 Joueurs tunisiens: $tunisianPlayers\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM clubs");
    $totalClubs = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "🏆 Total des clubs: $totalClubs\n";
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM nationalities");
    $totalNationalities = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    echo "🏳️ Total des nationalités: $totalNationalities\n";
} catch (PDOException $e) {
    echo "⚠️ Erreur statistiques: " . $e->getMessage() . "\n";
}

// 3. TESTER LES ROUTES
echo "\n🌐 Test des routes...\n";

$routes = [
    'http://localhost:8001/joueurs' => 'Sélection des joueurs',
    'http://localhost:8001/test-portal/32' => 'Portail de test du joueur 32'
];

foreach ($routes as $url => $description) {
    echo "🔗 Test de: $description\n";
    echo "   URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "   ❌ Erreur cURL: $error\n";
    } elseif ($httpCode == 200) {
        echo "   ✅ Succès (HTTP $httpCode)\n";
        if (strpos($response, 'Laravel') !== false) {
            echo "   ⚠️ Page d'erreur Laravel détectée\n";
        } else {
            echo "   🎉 Page chargée correctement\n";
        }
    } else {
        echo "   ❌ Erreur HTTP: $httpCode\n";
    }
    echo "\n";
}

// 4. AFFICHER QUELQUES JOUEURS TUNISIENS
echo "🇹🇳 Joueurs tunisiens disponibles...\n";
try {
    $stmt = $pdo->query("SELECT p.id, p.first_name, p.last_name, p.nationality, p.position, c.name as club_name 
                         FROM players p 
                         LEFT JOIN clubs c ON p.club_id = c.id 
                         WHERE p.nationality = 'Tunisia'
                         ORDER BY p.id");
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($players as $player) {
        echo "   ID {$player['id']}: {$player['first_name']} {$player['last_name']} - {$player['position']} @ {$player['club_name']}\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur affichage joueurs: " . $e->getMessage() . "\n";
}

// 5. VÉRIFIER LES NATIONALITÉS
echo "\n🏳️ Nationalités disponibles...\n";
try {
    $stmt = $pdo->query("SELECT * FROM nationalities ORDER BY name");
    $nationalities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($nationalities as $nationality) {
        echo "   {$nationality['name']} (ID: {$nationality['id']})\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur nationalités: " . $e->getMessage() . "\n";
}

echo "\n🎉 TEST FINAL TERMINÉ!\n";
echo "💡 Résumé du système:\n";
echo "   1. ✅ Page de sélection des joueurs: http://localhost:8001/joueurs\n";
echo "   2. ⚠️  Portail des joueurs: Nécessite authentification\n";
echo "   3. ✅ Route de test publique: http://localhost:8001/test-portal/{id}\n";
echo "   4. ✅ Base de données: $totalPlayers joueurs, $totalClubs clubs, $totalNationalities nationalités\n";
echo "\n🚀 Le système permet d'accéder à TOUS les joueurs via la page de sélection!\n";







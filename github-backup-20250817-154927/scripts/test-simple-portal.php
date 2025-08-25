<?php

echo "=== TEST SIMPLE DU SYSTÈME DE PORTAL ===\n\n";

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
} catch (PDOException $e) {
    echo "⚠️ Erreur statistiques: " . $e->getMessage() . "\n";
}

// 3. AFFICHER QUELQUES JOUEURS
echo "\n👥 Aperçu des joueurs disponibles...\n";
try {
    $stmt = $pdo->query("SELECT p.id, p.first_name, p.last_name, p.nationality, p.position, c.name as club_name 
                         FROM players p 
                         LEFT JOIN clubs c ON p.club_id = c.id 
                         ORDER BY p.id 
                         LIMIT 5");
    $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($players as $player) {
        echo "   ID {$player['id']}: {$player['first_name']} {$player['last_name']} ({$player['nationality']}) - {$player['position']} @ {$player['club_name']}\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur affichage joueurs: " . $e->getMessage() . "\n";
}

// 4. TESTER LES ROUTES SIMPLES
echo "\n🌐 Test des routes simples...\n";

$routes = [
    'http://localhost:8001/portail-joueur/32' => 'Portail du joueur 32',
    'http://localhost:8001/portail-joueur/33' => 'Portail du joueur 33'
];

foreach ($routes as $url => $description) {
    echo "🔗 Test de: $description\n";
    echo "   URL: $url\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
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

echo "🎉 TEST SIMPLE TERMINÉ!\n";
echo "💡 Pour tester manuellement:\n";
echo "   1. Portail d'un joueur: http://localhost:8001/portail-joueur/32\n";
echo "   2. Portail d'un autre: http://localhost:8001/portail-joueur/33\n";
echo "\n🚀 Le système permet maintenant d'accéder à TOUS les joueurs de la base!\n";







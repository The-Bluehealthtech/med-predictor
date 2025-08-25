<?php

echo "=== TEST DU PORTAL ===\n\n";

// Test avec différents joueurs
$players = [
    1 => 'Cristiano Ronaldo',
    29 => 'Moussa Diaby', 
    32 => 'Wahbi Khazri'
];

foreach ($players as $id => $name) {
    echo "🔍 Test du joueur: $name (ID: $id)\n";
    
    // Simuler une requête au portail
    $url = "http://localhost:8001/portail-joueur/$id";
    
    // Utiliser curl pour tester
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLOPT_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200) {
        echo "✅ Portail accessible (HTTP $httpCode)\n";
        
        // Vérifier si les données dynamiques sont présentes
        if (strpos($response, '{{ $player->performances->sum("goals") ?? 0 }}') !== false) {
            echo "✅ Variables dynamiques présentes\n";
        } else {
            echo "⚠️ Variables dynamiques manquantes\n";
        }
        
        // Vérifier le titre
        if (strpos($response, $name) !== false) {
            echo "✅ Nom du joueur affiché: $name\n";
        } else {
            echo "⚠️ Nom du joueur non trouvé\n";
        }
        
    } else {
        echo "❌ Erreur HTTP: $httpCode\n";
    }
    
    echo "---\n";
}

echo "💡 Testez maintenant dans votre navigateur:\n";
echo "1. http://localhost:8001/portail-joueur/29 (Moussa Diaby)\n";
echo "2. http://localhost:8001/portail-joueur/32 (Wahbi Khazri)\n";
echo "3. http://localhost:8001/portail-joueur/1 (Cristiano Ronaldo)\n";
echo "\n🎯 Vérifiez que les statistiques changent entre les joueurs!\n";







<?php

echo "=== DÉBOGAGE DE L'ERREUR DU PORTAL ===\n\n";

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

// 2. TESTER LA RÉCUPÉRATION D'UN JOUEUR SPÉCIFIQUE
echo "\n🔍 Test de récupération du joueur 32...\n";
try {
    $stmt = $pdo->prepare("SELECT * FROM players WHERE id = ?");
    $stmt->execute([32]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($player) {
        echo "✅ Joueur 32 trouvé: {$player['first_name']} {$player['last_name']}\n";
        echo "   Nationalité: {$player['nationality']}\n";
        echo "   Position: {$player['position']}\n";
        echo "   Club ID: {$player['club_id']}\n";
    } else {
        echo "❌ Joueur 32 non trouvé\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur récupération joueur: " . $e->getMessage() . "\n";
}

// 3. TESTER LA RÉCUPÉRATION DU CLUB
echo "\n🔍 Test de récupération du club...\n";
try {
    if (isset($player['club_id']) && $player['club_id']) {
        $stmt = $pdo->prepare("SELECT * FROM clubs WHERE id = ?");
        $stmt->execute([$player['club_id']]);
        $club = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($club) {
            echo "✅ Club trouvé: {$club['name']}\n";
        } else {
            echo "❌ Club non trouvé\n";
        }
    } else {
        echo "⚠️ Pas de club_id pour ce joueur\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur récupération club: " . $e->getMessage() . "\n";
}

// 4. TESTER LES RELATIONS
echo "\n🔍 Test des relations...\n";
try {
    $stmt = $pdo->query("SELECT p.id, p.first_name, p.last_name, p.club_id, c.name as club_name 
                         FROM players p 
                         LEFT JOIN clubs c ON p.club_id = c.id 
                         WHERE p.id = 32");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        echo "✅ Relation testée: {$result['first_name']} {$result['last_name']} @ {$result['club_name']}\n";
    } else {
        echo "❌ Relation échouée\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur relation: " . $e->getMessage() . "\n";
}

// 5. TESTER LA VUE BLADE
echo "\n🔍 Test de la vue Blade...\n";
$portalFile = 'resources/views/portail-joueur.blade.php';
if (file_exists($portalFile)) {
    echo "✅ Fichier portal trouvé: " . filesize($portalFile) . " bytes\n";
    
    // Vérifier la syntaxe PHP
    $content = file_get_contents($portalFile);
    if (strpos($content, '{{ $player->') !== false) {
        echo "✅ Variables Blade détectées\n";
    } else {
        echo "⚠️ Variables Blade manquantes\n";
    }
    
    // Vérifier les erreurs de syntaxe
    $tempFile = tempnam(sys_get_temp_dir(), 'blade_test');
    file_put_contents($tempFile, '<?php ' . $content);
    
    $output = [];
    $returnCode = 0;
    exec("php -l $tempFile 2>&1", $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "✅ Syntaxe PHP valide\n";
    } else {
        echo "❌ Erreur de syntaxe PHP:\n";
        foreach ($output as $line) {
            echo "   $line\n";
        }
    }
    
    unlink($tempFile);
} else {
    echo "❌ Fichier portal non trouvé\n";
}

echo "\n🎉 DÉBOGAGE TERMINÉ!\n";
echo "💡 Vérifiez les logs Laravel pour plus de détails\n";







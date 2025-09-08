<?php
/**
 * Script d'import complet des données de production
 * Importe tous les joueurs, clubs, associations depuis fit3.tbhc.uk
 */

// Configuration de la base de données locale
$host = 'mysql';
$dbname = 'med_predictor';
$username = 'root';
$password = 'root_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base MySQL réussie\n";
} catch (PDOException $e) {
    die("❌ Erreur de connexion MySQL: " . $e->getMessage() . "\n");
}

$productionUrl = 'https://fit3.tbhc.uk/api';

function fetchData($url, $maxRetries = 3) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Med-Predictor Import Script');
    
    $retries = 0;
    while ($retries < $maxRetries) {
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if ($response !== false && $httpCode === 200) {
            curl_close($ch);
            return json_decode($response, true);
        }
        
        $retries++;
        echo "⚠️ Tentative $retries/$maxRetries échouée pour $url\n";
        sleep(2);
    }
    
    curl_close($ch);
    return null;
}

function importPlayers($pdo, $productionUrl) {
    echo "🏃 Import des joueurs...\n";
    
    $page = 1;
    $totalImported = 0;
    $maxPages = 1000; // Limite de sécurité
    
    while ($page <= $maxPages) {
        $url = "$productionUrl/players?page=$page&per_page=100";
        echo "📄 Page $page: $url\n";
        
        $data = fetchData($url);
        if (!$data || !isset($data['data']) || empty($data['data'])) {
            echo "✅ Fin des données à la page $page\n";
            break;
        }
        
        $players = $data['data'];
        echo "📊 " . count($players) . " joueurs trouvés sur cette page\n";
        
        foreach ($players as $playerData) {
            try {
                // Vérifier si le joueur existe déjà (par nom)
                $playerName = $playerData['name'] ?? ($playerData['first_name'] . ' ' . $playerData['last_name']);
                $stmt = $pdo->prepare("SELECT id FROM players WHERE name = ?");
                $stmt->execute([$playerName]);
                
                if ($stmt->fetch()) {
                    echo "⏭️ Joueur $playerName déjà existant\n";
                    continue;
                }
                
                // Insérer le joueur
                $sql = "INSERT INTO players (
                    name, first_name, last_name, date_of_birth, 
                    nationality, position, club_id, association_id, created_at, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $playerName,
                    $playerData['first_name'] ?? null,
                    $playerData['last_name'] ?? null,
                    $playerData['birth_date'] ?? null,
                    $playerData['nationality'] ?? null,
                    $playerData['position'] ?? null,
                    $playerData['club_id'] ?? null,
                    $playerData['association_id'] ?? null
                ]);
                
                $totalImported++;
                echo "✅ Joueur {$playerData['id']} importé\n";
                
            } catch (PDOException $e) {
                echo "❌ Erreur import joueur {$playerData['id']}: " . $e->getMessage() . "\n";
            }
        }
        
        $page++;
        
        // Pause entre les pages pour éviter la surcharge
        sleep(1);
    }
    
    echo "🎉 Import terminé: $totalImported joueurs importés\n";
    return $totalImported;
}

function importClubs($pdo, $productionUrl) {
    echo "🏢 Import des clubs...\n";
    
    $url = "$productionUrl/clubs";
    $data = fetchData($url);
    
    if (!$data || !isset($data['data'])) {
        echo "❌ Aucune donnée de clubs trouvée\n";
        return 0;
    }
    
    $clubs = $data['data'];
    $totalImported = 0;
    
    foreach ($clubs as $clubData) {
        try {
            // Vérifier si le club existe déjà (par nom)
            $stmt = $pdo->prepare("SELECT id FROM clubs WHERE name = ?");
            $stmt->execute([$clubData['name'] ?? null]);
            
            if ($stmt->fetch()) {
                echo "⏭️ Club {$clubData['name']} déjà existant\n";
                continue;
            }
            
            // Insérer le club
            $sql = "INSERT INTO clubs (
                name, short_name, city, country, created_at, updated_at
            ) VALUES (?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $clubData['name'] ?? null,
                $clubData['short_name'] ?? null,
                $clubData['city'] ?? null,
                $clubData['country'] ?? null
            ]);
            
            $totalImported++;
            echo "✅ Club {$clubData['id']} importé\n";
            
        } catch (PDOException $e) {
            echo "❌ Erreur import club {$clubData['id']}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "🎉 Import terminé: $totalImported clubs importés\n";
    return $totalImported;
}

function importAssociations($pdo, $productionUrl) {
    echo "🌍 Import des associations...\n";
    
    $url = "$productionUrl/associations";
    $data = fetchData($url);
    
    if (!$data || !isset($data['data'])) {
        echo "❌ Aucune donnée d'associations trouvée\n";
        return 0;
    }
    
    $associations = $data['data'];
    $totalImported = 0;
    
    foreach ($associations as $assocData) {
        try {
            // Vérifier si l'association existe déjà (par nom)
            $stmt = $pdo->prepare("SELECT id FROM associations WHERE name = ?");
            $stmt->execute([$assocData['name'] ?? null]);
            
            if ($stmt->fetch()) {
                echo "⏭️ Association {$assocData['name']} déjà existante\n";
                continue;
            }
            
            // Insérer l'association
            $sql = "INSERT INTO associations (
                name, short_name, country, continent, created_at, updated_at
            ) VALUES (?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $assocData['name'] ?? null,
                $assocData['short_name'] ?? null,
                $assocData['country'] ?? null,
                $assocData['continent'] ?? null
            ]);
            
            $totalImported++;
            echo "✅ Association {$assocData['id']} importée\n";
            
        } catch (PDOException $e) {
            echo "❌ Erreur import association {$assocData['id']}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "🎉 Import terminé: $totalImported associations importées\n";
    return $totalImported;
}

// Exécution de l'import
echo "🚀 Début de l'import complet des données de production\n";
echo "🌐 Source: $productionUrl\n";
echo "📊 Base de destination: MySQL med_predictor\n\n";

// 1. Import des associations
$associationsImported = importAssociations($pdo, $productionUrl);

// 2. Import des clubs
$clubsImported = importClubs($pdo, $productionUrl);

// 3. Import des joueurs
$playersImported = importPlayers($pdo, $productionUrl);

// Résumé final
echo "\n🎯 RÉSUMÉ DE L'IMPORT:\n";
echo "📊 Associations: $associationsImported\n";
echo "🏢 Clubs: $clubsImported\n";
echo "🏃 Joueurs: $playersImported\n";
echo "🎉 Total: " . ($associationsImported + $clubsImported + $playersImported) . " enregistrements\n";

// Vérification finale
$stmt = $pdo->query("SELECT COUNT(*) as total FROM players");
$totalPlayers = $stmt->fetch()['total'];
echo "\n✅ Vérification finale: $totalPlayers joueurs dans la base\n";

echo "\n🎊 Import terminé avec succès!\n";
?>

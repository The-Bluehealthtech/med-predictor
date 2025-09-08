<?php

/**
 * Script d'import des données de production depuis fit3.tbhc.uk
 * Importe les joueurs, clubs, associations et autres données
 */

echo "🚀 IMPORT DES DONNÉES DE PRODUCTION DEPUIS FIT3.TBHC.UK\n";
echo "======================================================\n\n";

// Configuration de la base de données locale
$host = 'mysql';
$dbname = 'med_predictor';
$username = 'root';
$password = 'root_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base de données locale réussie\n\n";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit(1);
}

// URL de l'API de production
$productionUrl = 'https://fit3.tbhc.uk/api';

// Fonction pour récupérer les données depuis l'API
function fetchData($url, $maxRetries = 3) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'FIT-Import-Script/1.0');
    
    for ($i = 0; $i < $maxRetries; $i++) {
        $response = curl_exec($ch);
        if ($response !== false) {
            curl_close($ch);
            return json_decode($response, true);
        }
        echo "⚠️ Tentative " . ($i + 1) . " échouée, retry...\n";
        sleep(2);
    }
    
    curl_close($ch);
    return false;
}

// 1. IMPORT DES ASSOCIATIONS
echo "🏛️ IMPORT DES ASSOCIATIONS\n";
echo "---------------------------\n";

// Créer la table associations si elle n'existe pas
$pdo->exec("
    CREATE TABLE IF NOT EXISTS associations (
        id INT PRIMARY KEY AUTO_INCREMENT,
        tenant_id INT NULL,
        confederation_id INT NULL,
        name VARCHAR(255) NOT NULL,
        short_name VARCHAR(50),
        country VARCHAR(100),
        confederation TEXT,
        fifa_ranking INT,
        fifa_version VARCHAR(50),
        fifa_sync_status VARCHAR(50),
        fifa_sync_date DATETIME,
        fifa_last_error TEXT,
        association_logo_url VARCHAR(500),
        nation_flag_url VARCHAR(500),
        founded_year INT,
        logo_path VARCHAR(500),
        address TEXT,
        phone VARCHAR(50),
        email VARCHAR(100),
        website VARCHAR(200),
        status VARCHAR(50) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )
");

// Insérer l'association FTF
$pdo->exec("
    INSERT IGNORE INTO associations (
        id, tenant_id, confederation_id, name, short_name, country,
        association_logo_url, nation_flag_url, logo_path, status
    ) VALUES (
        1, NULL, 1, 'Fédération Tunisienne de Football', 'FTF', 'Tunisie',
        'associations/logos/k5NmFyIiPWPbZGqQVAytEA9XyQirDZq5bHNvr59P.png',
        'associations/flags/f0MkML9eprF4SRC72Z3BtxcH4vTQzKFL2NMQ519Y.png',
        'associations/logos/federation-tunisienne.png', 'active'
    )
");

echo "✅ Association FTF créée\n\n";

// 2. IMPORT DES CLUBS
echo "🏆 IMPORT DES CLUBS\n";
echo "-------------------\n";

// Créer la table clubs si elle n'existe pas
$pdo->exec("
    CREATE TABLE IF NOT EXISTS clubs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        tenant_id INT NULL,
        name VARCHAR(255) NOT NULL,
        short_name VARCHAR(50),
        association_id INT,
        logo_path VARCHAR(500),
        address TEXT,
        phone VARCHAR(50),
        email VARCHAR(100),
        website VARCHAR(200),
        founded_year INT,
        status VARCHAR(50) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (association_id) REFERENCES associations(id)
    )
");

// Clubs tunisiens principaux
$clubs = [
    ['id' => 1, 'tenant_id' => 1, 'name' => 'Club Africain', 'short_name' => 'CA', 'association_id' => 1, 'logo_path' => 'club_logos/uBSg4JJYzOUi91bbsXZrlBYOm6GapUNyRCwS5EPQ.png', 'address' => 'Stade Olympique de Radès, Radès, Tunisie', 'phone' => '+216 71 234 567', 'email' => 'contact@clubafricain.com', 'website' => 'https://www.clubafricain.com', 'founded_year' => 1920],
    ['id' => 2, 'tenant_id' => 1, 'name' => 'Espérance Sportive de Tunis', 'short_name' => 'EST', 'association_id' => 1, 'logo_path' => 'club_logos/hL7bn3WEr4ynOlbgKsx6taXJP2gnpdum4T3fOgXL.png', 'address' => 'Stade Olympique de Radès, Radès, Tunisie', 'phone' => '+216 71 345 678', 'email' => 'contact@esperance.com', 'website' => 'https://www.esperance.com', 'founded_year' => 1919],
    ['id' => 6, 'tenant_id' => NULL, 'name' => 'Étoile du Sahel', 'short_name' => 'ESS', 'association_id' => 1, 'logo_path' => 'club_logos/club_6_1756651488.svg', 'address' => 'Stade Olympique de Sousse, Sousse, Tunisie', 'phone' => '+216 73 000 000', 'email' => 'contact@etoile-du-sahel.com', 'website' => 'http://www.etoile-du-sahel.com', 'founded_year' => NULL],
    ['id' => 7, 'tenant_id' => NULL, 'name' => 'CS Sfaxien', 'short_name' => 'CSS', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Taïeb Mhiri, Sfax', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1928],
    ['id' => 8, 'tenant_id' => NULL, 'name' => 'US Monastir', 'short_name' => 'USM', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Mustapha Ben Jannet, Monastir', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1959],
    ['id' => 9, 'tenant_id' => NULL, 'name' => 'CA Bizertin', 'short_name' => 'CAB', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade 15 Octobre, Bizerte', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1928],
    ['id' => 10, 'tenant_id' => NULL, 'name' => 'JS Kairouan', 'short_name' => 'JSK', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Hamda Laouani, Kairouan', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1942],
    ['id' => 11, 'tenant_id' => NULL, 'name' => 'AS Gabès', 'short_name' => 'ASG', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Municipal de Gabès, Gabès', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1977],
    ['id' => 12, 'tenant_id' => NULL, 'name' => 'Stade Tunisien', 'short_name' => 'ST', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Chedli Zouiten, Tunis', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1948],
    ['id' => 13, 'tenant_id' => NULL, 'name' => 'US Ben Guerdane', 'short_name' => 'USBG', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade du 7 Mars, Ben Guerdane', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1936],
    ['id' => 15, 'tenant_id' => NULL, 'name' => 'AS Soliman', 'short_name' => 'ASS', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Municipal de Soliman, Soliman', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1958],
    ['id' => 16, 'tenant_id' => NULL, 'name' => 'CS Hammam-Lif', 'short_name' => 'CSHL', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Municipal de Hammam-Lif, Hammam-Lif', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1944],
    ['id' => 17, 'tenant_id' => NULL, 'name' => 'JS Médenine', 'short_name' => 'JSM', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Municipal de Médenine, Médenine', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1956],
    ['id' => 18, 'tenant_id' => NULL, 'name' => 'US Tataouine', 'short_name' => 'UST', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Municipal de Tataouine, Tataouine', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1980],
    ['id' => 19, 'tenant_id' => NULL, 'name' => 'AS Marsa', 'short_name' => 'ASM', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Abdelaziz Chtioui, La Marsa', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1948],
    ['id' => 20, 'tenant_id' => NULL, 'name' => 'ES Métlaoui', 'short_name' => 'ESM', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Municipal de Métlaoui, Métlaoui', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1950],
    ['id' => 21, 'tenant_id' => NULL, 'name' => 'US Siliana', 'short_name' => 'USS', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Municipal de Siliana, Siliana', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1975],
    ['id' => 22, 'tenant_id' => NULL, 'name' => 'AS Djerba', 'short_name' => 'ASD', 'association_id' => 1, 'logo_path' => NULL, 'address' => 'Stade Municipal de Djerba, Djerba', 'phone' => NULL, 'email' => NULL, 'website' => NULL, 'founded_year' => 1960]
];

$stmt = $pdo->prepare("
    INSERT IGNORE INTO clubs (id, tenant_id, name, short_name, association_id, logo_path, address, phone, email, website, founded_year, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
");

foreach ($clubs as $club) {
    $stmt->execute([
        $club['id'], $club['tenant_id'], $club['name'], $club['short_name'], $club['association_id'],
        $club['logo_path'], $club['address'], $club['phone'], $club['email'], $club['website'], $club['founded_year']
    ]);
}

echo "✅ " . count($clubs) . " clubs tunisiens créés\n\n";

// 3. IMPORT DES JOUEURS
echo "⚽ IMPORT DES JOUEURS\n";
echo "---------------------\n";

// Créer la table players si elle n'existe pas
$pdo->exec("
    CREATE TABLE IF NOT EXISTS players (
        id INT PRIMARY KEY AUTO_INCREMENT,
        tenant_id INT NULL,
        name VARCHAR(255) NOT NULL,
        first_name VARCHAR(100),
        last_name VARCHAR(100),
        date_of_birth DATE,
        position VARCHAR(10),
        nationality VARCHAR(100),
        club_id INT,
        association_id INT,
        address TEXT,
        contact_phone VARCHAR(50),
        contact_email VARCHAR(100),
        legal_guardian VARCHAR(255),
        school_professional_status VARCHAR(100),
        parental_consent TEXT,
        license_type VARCHAR(50),
        previous_clubs TEXT,
        previous_license_number VARCHAR(100),
        player_picture VARCHAR(500),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (club_id) REFERENCES clubs(id),
        FOREIGN KEY (association_id) REFERENCES associations(id)
    )
");

// Récupérer les joueurs depuis l'API de production
echo "📡 Récupération des joueurs depuis l'API de production...\n";
$playersData = fetchData($productionUrl . '/players');

if ($playersData && is_array($playersData)) {
    $stmt = $pdo->prepare("
        INSERT IGNORE INTO players (
            id, tenant_id, name, first_name, last_name, date_of_birth, position, nationality,
            club_id, association_id, address, contact_phone, contact_email, legal_guardian,
            school_professional_status, parental_consent, license_type, previous_clubs,
            previous_license_number, player_picture
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $importedCount = 0;
    foreach ($playersData as $player) {
        try {
            $stmt->execute([
                $player['id'],
                $player['tenant_id'],
                $player['name'],
                $player['first_name'],
                $player['last_name'],
                $player['date_of_birth'],
                $player['position'],
                $player['nationality'],
                $player['club_id'],
                $player['association_id'],
                $player['address'],
                $player['contact_phone'],
                $player['contact_email'],
                $player['legal_guardian'],
                $player['school_professional_status'],
                $player['parental_consent'],
                $player['license_type'],
                $player['previous_clubs'],
                $player['previous_license_number'],
                $player['player_picture']
            ]);
            $importedCount++;
        } catch (Exception $e) {
            echo "⚠️ Erreur import joueur {$player['id']}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "✅ $importedCount joueurs importés depuis l'API\n";
} else {
    echo "❌ Impossible de récupérer les joueurs depuis l'API\n";
}

echo "\n🎉 IMPORT TERMINÉ !\n";
echo "==================\n";
echo "✅ Association FTF créée\n";
echo "✅ " . count($clubs) . " clubs tunisiens créés\n";
echo "✅ Joueurs importés depuis l'API de production\n";
echo "\n📊 Vérification des données...\n";

// Vérification finale
$associationsCount = $pdo->query("SELECT COUNT(*) FROM associations")->fetchColumn();
$clubsCount = $pdo->query("SELECT COUNT(*) FROM clubs")->fetchColumn();
$playersCount = $pdo->query("SELECT COUNT(*) FROM players")->fetchColumn();

echo "📈 Statistiques finales:\n";
echo "   - Associations: $associationsCount\n";
echo "   - Clubs: $clubsCount\n";
echo "   - Joueurs: $playersCount\n";

?>

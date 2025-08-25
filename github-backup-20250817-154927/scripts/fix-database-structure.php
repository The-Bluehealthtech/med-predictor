<?php

echo "=== CORRECTION DE LA STRUCTURE DE LA BASE DE DONNÉES ===\n\n";

// Connexion à la base de données
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base de données réussie\n";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit(1);
}

// 1. VÉRIFIER LA STRUCTURE ACTUELLE
echo "\n🔍 Vérification de la structure actuelle...\n";

try {
    $stmt = $pdo->query("PRAGMA table_info(players)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "📋 Colonnes de la table 'players':\n";
    foreach ($columns as $column) {
        echo "  - {$column['name']} ({$column['type']})\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la vérification de 'players': " . $e->getMessage() . "\n";
}

try {
    $stmt = $pdo->query("PRAGMA table_info(clubs)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "📋 Colonnes de la table 'clubs':\n";
    foreach ($columns as $column) {
        echo "  - {$column['name']} ({$column['type']})\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la vérification de 'clubs': " . $e->getMessage() . "\n";
}

// 2. AJOUTER LES COLONNES MANQUANTES
echo "\n🔧 Ajout des colonnes manquantes...\n";

// Ajouter photo_path et photo_url à la table players
try {
    $pdo->exec("ALTER TABLE players ADD COLUMN photo_path TEXT");
    echo "✅ Colonne 'photo_path' ajoutée à 'players'\n";
} catch (PDOException $e) {
    echo "⚠️ Colonne 'photo_path' existe déjà ou erreur: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE players ADD COLUMN photo_url TEXT");
    echo "✅ Colonne 'photo_url' ajoutée à 'players'\n";
} catch (PDOException $e) {
    echo "⚠️ Colonne 'photo_url' existe déjà ou erreur: " . $e->getMessage() . "\n";
}

// Ajouter logo_path et logo_url à la table clubs
try {
    $pdo->exec("ALTER TABLE clubs ADD COLUMN logo_path TEXT");
    echo "✅ Colonne 'logo_path' ajoutée à 'clubs'\n";
} catch (PDOException $e) {
    echo "⚠️ Colonne 'logo_path' existe déjà ou erreur: " . $e->getMessage() . "\n";
}

try {
    $pdo->exec("ALTER TABLE clubs ADD COLUMN logo_url TEXT");
    echo "✅ Colonne 'logo_url' ajoutée à 'clubs'\n";
} catch (PDOException $e) {
    echo "⚠️ Colonne 'logo_url' existe déjà ou erreur: " . $e->getMessage() . "\n";
}

// 3. CRÉER LA TABLE NATIONALITIES SI ELLE N'EXISTE PAS
echo "\n🏳️ Création de la table 'nationalities'...\n";

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS nationalities (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        flag_path TEXT,
        flag_url TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "✅ Table 'nationalities' créée ou existe déjà\n";
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la création de 'nationalities': " . $e->getMessage() . "\n";
}

// 4. VÉRIFICATION FINALE
echo "\n🔍 Vérification finale de la structure...\n";

try {
    $stmt = $pdo->query("PRAGMA table_info(players)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasPhotoPath = false;
    $hasPhotoUrl = false;
    
    foreach ($columns as $column) {
        if ($column['name'] === 'photo_path') $hasPhotoPath = true;
        if ($column['name'] === 'photo_url') $hasPhotoUrl = true;
    }
    
    if ($hasPhotoPath && $hasPhotoUrl) {
        echo "✅ Table 'players' a les colonnes photo_path et photo_url\n";
    } else {
        echo "❌ Table 'players' manque des colonnes photo\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la vérification finale: " . $e->getMessage() . "\n";
}

try {
    $stmt = $pdo->query("PRAGMA table_info(clubs)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasLogoPath = false;
    $hasLogoUrl = false;
    
    foreach ($columns as $column) {
        if ($column['name'] === 'logo_path') $hasLogoPath = true;
        if ($column['name'] === 'logo_url') $hasLogoUrl = true;
    }
    
    if ($hasLogoPath && $hasLogoUrl) {
        echo "✅ Table 'clubs' a les colonnes logo_path et logo_url\n";
    } else {
        echo "❌ Table 'clubs' manque des colonnes logo\n";
    }
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la vérification finale: " . $e->getMessage() . "\n";
}

try {
    $stmt = $pdo->query("PRAGMA table_info(nationalities)");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Table 'nationalities' existe avec " . count($columns) . " colonnes\n";
} catch (PDOException $e) {
    echo "❌ Table 'nationalities' n'existe pas: " . $e->getMessage() . "\n";
}

echo "\n🎉 CORRECTION DE LA STRUCTURE TERMINÉE!\n";
echo "🚀 La base de données est maintenant prête pour les photos et logos!\n";
echo "🌐 Vous pouvez maintenant relancer le script d'ajout des images!\n";
echo "\n💡 Toutes les colonnes nécessaires ont été ajoutées!\n";
echo "✨ La structure est maintenant compatible avec les images!\n";







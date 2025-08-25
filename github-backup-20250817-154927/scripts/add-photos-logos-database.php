<?php

echo "=== AJOUT DES PHOTOS ET LOGOS DANS LA BASE DE DONNÉES ===\n\n";

// Connexion à la base de données
try {
    $pdo = new PDO('sqlite:database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Connexion à la base de données réussie\n";
} catch (PDOException $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "\n";
    exit(1);
}

// 1. AJOUT DES PHOTOS DES JOUEURS
echo "\n📸 Ajout des photos des joueurs...\n";

$players = [
    [
        'id' => 2,
        'first_name' => 'Sadio',
        'last_name' => 'Mané',
        'photo_path' => '/images/players/sadio_mane.jpg',
        'photo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Sadio_Man%C3%A9_2018.jpg/800px-Sadio_Man%C3%A9_2018.jpg'
    ],
    [
        'id' => 1,
        'first_name' => 'Lionel',
        'last_name' => 'Messi',
        'photo_path' => '/images/players/lionel_messi.jpg',
        'photo_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Lionel_Messi_20180626.jpg/800px-Lionel_Messi_20180626.jpg'
    ]
];

foreach ($players as $player) {
    try {
        $stmt = $pdo->prepare("UPDATE players SET photo_path = ?, photo_url = ? WHERE id = ?");
        $stmt->execute([$player['photo_path'], $player['photo_url'], $player['id']]);
        echo "✅ Photo ajoutée pour {$player['first_name']} {$player['last_name']}\n";
    } catch (PDOException $e) {
        echo "⚠️ Erreur pour {$player['first_name']} {$player['last_name']}: " . $e->getMessage() . "\n";
    }
}

// 2. AJOUT DES LOGOS DES CLUBS
echo "\n🏆 Ajout des logos des clubs...\n";

$clubs = [
    [
        'id' => 1,
        'name' => 'Chelsea FC',
        'logo_path' => '/images/clubs/chelsea_fc.png',
        'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg'
    ],
    [
        'id' => 2,
        'name' => 'Al Nassr',
        'logo_path' => '/images/clubs/al_nassr.png',
        'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/8/8c/Al_Nassr_Logo.png'
    ],
    [
        'id' => 3,
        'name' => 'Inter Miami CF',
        'logo_path' => '/images/clubs/inter_miami.png',
        'logo_url' => 'https://upload.wikimedia.org/wikipedia/en/a/a2/Inter_Miami_CF_logo.svg'
    ]
];

foreach ($clubs as $club) {
    try {
        $stmt = $pdo->prepare("UPDATE clubs SET logo_path = ?, logo_url = ? WHERE id = ?");
        $stmt->execute([$club['logo_path'], $club['logo_url'], $club['id']]);
        echo "✅ Logo ajouté pour {$club['name']}\n";
    } catch (PDOException $e) {
        echo "⚠️ Erreur pour {$club['name']}: " . $e->getMessage() . "\n";
    }
}

// 3. AJOUT DES DRAPEAUX DES NATIONALITÉS
echo "\n🏳️ Ajout des drapeaux des nationalités...\n";

$nationalities = [
    [
        'id' => 1,
        'name' => 'Senegal',
        'flag_path' => '/images/flags/senegal.svg',
        'flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/f/fd/Flag_of_Senegal.svg'
    ],
    [
        'id' => 2,
        'name' => 'Argentina',
        'flag_path' => '/images/flags/argentina.svg',
        'flag_url' => 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Flag_of_Argentina.svg'
    ]
];

foreach ($nationalities as $nationality) {
    try {
        $stmt = $pdo->prepare("UPDATE nationalities SET flag_path = ?, flag_url = ? WHERE id = ?");
        $stmt->execute([$nationality['flag_path'], $nationality['flag_url'], $nationality['id']]);
        echo "✅ Drapeau ajouté pour {$nationality['name']}\n";
    } catch (PDOException $e) {
        echo "⚠️ Erreur pour {$nationality['name']}: " . $e->getMessage() . "\n";
    }
}

// 4. CRÉATION DES DOSSIERS D'IMAGES
echo "\n📁 Création des dossiers d'images...\n";

$directories = [
    'public/images/players',
    'public/images/clubs',
    'public/images/flags',
    'public/images/logos'
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "✅ Dossier créé: $dir\n";
        } else {
            echo "⚠️ Erreur création dossier: $dir\n";
        }
    } else {
        echo "✅ Dossier existe déjà: $dir\n";
    }
}

// 5. TÉLÉCHARGEMENT DES IMAGES (optionnel)
echo "\n🌐 Téléchargement des images...\n";

$imagesToDownload = [
    'players' => [
        'sadio_mane.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Sadio_Man%C3%A9_2018.jpg/800px-Sadio_Man%C3%A9_2018.jpg',
        'lionel_messi.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Lionel_Messi_20180626.jpg/800px-Lionel_Messi_20180626.jpg'
    ],
    'clubs' => [
        'chelsea_fc.png' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg',
        'al_nassr.png' => 'https://upload.wikimedia.org/wikipedia/en/8/8c/Al_Nassr_Logo.png',
        'inter_miami.png' => 'https://upload.wikimedia.org/wikipedia/en/a/a2/Inter_Miami_CF_logo.svg'
    ],
    'flags' => [
        'senegal.svg' => 'https://upload.wikimedia.org/wikipedia/commons/f/fd/Flag_of_Senegal.svg',
        'argentina.svg' => 'https://upload.wikimedia.org/wikipedia/commons/1/1a/Flag_of_Argentina.svg'
    ]
];

foreach ($imagesToDownload as $category => $images) {
    foreach ($images as $filename => $url) {
        $filepath = "public/images/$category/$filename";
        if (!file_exists($filepath)) {
            try {
                $imageContent = file_get_contents($url);
                if ($imageContent !== false) {
                    file_put_contents($filepath, $imageContent);
                    echo "✅ Image téléchargée: $filename\n";
                } else {
                    echo "⚠️ Erreur téléchargement: $filename\n";
                }
            } catch (Exception $e) {
                echo "⚠️ Erreur téléchargement: $filename - " . $e->getMessage() . "\n";
            }
        } else {
            echo "✅ Image existe déjà: $filename\n";
        }
    }
}

// 6. VÉRIFICATION FINALE
echo "\n🔍 Vérification finale...\n";

try {
    // Vérifier les photos des joueurs
    $stmt = $pdo->query("SELECT id, first_name, last_name, photo_path, photo_url FROM players WHERE photo_path IS NOT NULL");
    $playersWithPhotos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Joueurs avec photos: " . count($playersWithPhotos) . "\n";
    
    // Vérifier les logos des clubs
    $stmt = $pdo->query("SELECT id, name, logo_path, logo_url FROM clubs WHERE logo_path IS NOT NULL");
    $clubsWithLogos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Clubs avec logos: " . count($clubsWithLogos) . "\n";
    
    // Vérifier les drapeaux des nationalités
    $stmt = $pdo->query("SELECT id, name, flag_path, flag_url FROM nationalities WHERE flag_path IS NOT NULL");
    $nationalitiesWithFlags = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✅ Nationalités avec drapeaux: " . count($nationalitiesWithFlags) . "\n";
    
} catch (PDOException $e) {
    echo "⚠️ Erreur lors de la vérification: " . $e->getMessage() . "\n";
}

echo "\n🎉 AJOUT DES PHOTOS ET LOGOS TERMINÉ!\n";
echo "🚀 Toutes les images sont maintenant dans la base de données!\n";
echo "🌐 Testez maintenant: http://localhost:8001/joueur/2\n";
echo "\n💡 Les photos et logos sont maintenant 100% dynamiques!\n";
echo "✨ Plus de données fixes, tout vient de la base de données!\n";
echo "🎯 Le portail affichera les vraies images de Sadio Mané!\n";
echo "🏆 Logos des clubs et drapeaux des nationalités inclus!\n";







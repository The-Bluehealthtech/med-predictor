#!/bin/bash

echo "🎯 Création du Système de Licences avec Photos de Joueurs..."
echo "============================================================="

# Vérifier que le conteneur MySQL est en cours d'exécution
if ! docker ps | grep -q fit-mysql; then
    echo "❌ Conteneur MySQL non trouvé !"
    exit 1
fi

echo "✅ Conteneur MySQL trouvé"

# Créer le dossier licenses s'il n'existe pas
mkdir -p public/images/licenses

echo "📁 Dossier licenses créé/vérifié"

# Créer le système de licences
echo "🔄 Création du système de licences..."

docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;

-- Créer la table des licences
CREATE TABLE IF NOT EXISTS player_licenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id BIGINT NOT NULL,
    club_id BIGINT NOT NULL,
    association_id BIGINT NOT NULL,
    license_number VARCHAR(50) UNIQUE NOT NULL,
    season VARCHAR(9) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'expired') DEFAULT 'pending',
    photo_path VARCHAR(255),
    document_path VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_player_season (player_id, season),
    INDEX idx_club_season (club_id, season)
);

-- Créer la table des photos de licences
CREATE TABLE IF NOT EXISTS license_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    license_id INT NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    photo_type ENUM('player_photo', 'document_scan', 'signature') DEFAULT 'player_photo',
    uploaded_by INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (license_id) REFERENCES player_licenses(id) ON DELETE CASCADE
);

-- Insérer des licences d'exemple pour les 20 joueurs
INSERT IGNORE INTO player_licenses (player_id, club_id, association_id, license_number, season, status, photo_path) VALUES
(1, 1, 1, 'LIC-2024-001', '2024-2025', 'approved', 'images/licenses/player_1_license.jpg'),
(2, 2, 2, 'LIC-2024-002', '2024-2025', 'approved', 'images/licenses/player_2_license.jpg'),
(3, 3, 1, 'LIC-2024-003', '2024-2025', 'approved', 'images/licenses/player_3_license.jpg'),
(4, 4, 3, 'LIC-2024-004', '2024-2025', 'approved', 'images/licenses/player_4_license.jpg'),
(5, 5, 4, 'LIC-2024-005', '2024-2025', 'approved', 'images/licenses/player_5_license.jpg'),
(6, 6, 5, 'LIC-2024-006', '2024-2025', 'approved', 'images/licenses/player_6_license.jpg'),
(7, 7, 6, 'LIC-2024-007', '2024-2025', 'approved', 'images/licenses/player_7_license.jpg'),
(8, 8, 7, 'LIC-2024-008', '2024-2025', 'approved', 'images/licenses/player_8_license.jpg'),
(9, 9, 8, 'LIC-2024-009', '2024-2025', 'approved', 'images/licenses/player_9_license.jpg'),
(10, 10, 9, 'LIC-2024-010', '2024-2025', 'approved', 'images/licenses/player_10_license.jpg'),
(11, 1, 1, 'LIC-2024-011', '2024-2025', 'approved', 'images/licenses/player_11_license.jpg'),
(12, 2, 2, 'LIC-2024-012', '2024-2025', 'approved', 'images/licenses/player_12_license.jpg'),
(13, 3, 3, 'LIC-2024-013', '2024-2025', 'approved', 'images/licenses/player_13_license.jpg'),
(14, 4, 4, 'LIC-2024-014', '2024-2025', 'approved', 'images/licenses/player_14_license.jpg'),
(15, 5, 5, 'LIC-2024-015', '2024-2025', 'approved', 'images/licenses/player_15_license.jpg'),
(16, 6, 6, 'LIC-2024-016', '2024-2025', 'approved', 'images/licenses/player_16_license.jpg'),
(17, 7, 7, 'LIC-2024-017', '2024-2025', 'approved', 'images/licenses/player_17_license.jpg'),
(18, 8, 8, 'LIC-2024-018', '2024-2025', 'approved', 'images/licenses/player_18_license.jpg'),
(19, 9, 9, 'LIC-2024-019', '2024-2025', 'approved', 'images/licenses/player_19_license.jpg'),
(20, 10, 10, 'LIC-2024-020', '2024-2025', 'approved', 'images/licenses/player_20_license.jpg');

SELECT '✅ Système de licences créé avec 20 licences d\'exemple' as Status;
"

echo "✅ Système de licences créé !"
echo "🎯 Chaque joueur a maintenant sa licence avec photo !"



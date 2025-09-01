#!/bin/bash

echo "🎯 Création du Système de Logos de Clubs..."
echo "============================================="

# Vérifier que le conteneur MySQL est en cours d'exécution
if ! docker ps | grep -q fit-mysql; then
    echo "❌ Conteneur MySQL non trouvé !"
    exit 1
fi

echo "✅ Conteneur MySQL trouvé"

# Créer le dossier club_logos s'il n'existe pas
mkdir -p public/images/club_logos

echo "📁 Dossier club_logos créé/vérifié"

# Créer le système de logos de clubs
echo "🔄 Création du système de logos de clubs..."

docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;

-- Ajouter la colonne logo_path à la table clubs si elle n'existe pas
ALTER TABLE clubs ADD COLUMN logo_path VARCHAR(255) DEFAULT NULL;
ALTER TABLE clubs ADD COLUMN logo_uploaded_at TIMESTAMP NULL DEFAULT NULL;

-- Créer la table des logos de clubs
CREATE TABLE IF NOT EXISTS club_logos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    club_id INT NOT NULL,
    logo_path VARCHAR(255) NOT NULL,
    logo_type ENUM('main_logo', 'alternative_logo', 'historical_logo') DEFAULT 'main_logo',
    season VARCHAR(9),
    uploaded_by INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_club_season_type (club_id, season, logo_type)
);

-- Mettre à jour les clubs existants avec des logos
UPDATE clubs SET 
    logo_path = CASE id
        WHEN 1 THEN 'images/club_logos/psg_logo.png'
        WHEN 2 THEN 'images/club_logos/real_madrid_logo.png'
        WHEN 3 THEN 'images/club_logos/barcelona_logo.png'
        WHEN 4 THEN 'images/club_logos/manchester_city_logo.png'
        WHEN 5 THEN 'images/club_logos/liverpool_logo.png'
        WHEN 6 THEN 'images/club_logos/bayern_munich_logo.png'
        WHEN 7 THEN 'images/club_logos/chelsea_logo.png'
        WHEN 8 THEN 'images/club_logos/arsenal_logo.png'
        WHEN 9 THEN 'images/club_logos/atletico_madrid_logo.png'
        WHEN 10 THEN 'images/club_logos/paris_fc_logo.png'
    END,
    logo_uploaded_at = NOW()
WHERE id BETWEEN 1 AND 10;

-- Insérer les logos dans la table club_logos
INSERT IGNORE INTO club_logos (club_id, logo_path, logo_type, season) VALUES
(1, 'images/club_logos/psg_logo.png', 'main_logo', '2024-2025'),
(2, 'images/club_logos/real_madrid_logo.png', 'main_logo', '2024-2025'),
(3, 'images/club_logos/barcelona_logo.png', 'main_logo', '2024-2025'),
(4, 'images/club_logos/manchester_city_logo.png', 'main_logo', '2024-2025'),
(5, 'images/club_logos/liverpool_logo.png', 'main_logo', '2024-2025'),
(6, 'images/club_logos/bayern_munich_logo.png', 'main_logo', '2024-2025'),
(7, 'images/club_logos/chelsea_logo.png', 'main_logo', '2024-2025'),
(8, 'images/club_logos/arsenal_logo.png', 'main_logo', '2024-2025'),
(9, 'images/club_logos/atletico_madrid_logo.png', 'main_logo', '2024-2025'),
(10, 'images/club_logos/paris_fc_logo.png', 'main_logo', '2024-2025');

SELECT '✅ Système de logos de clubs créé avec 10 logos' as Status;
"

echo "✅ Système de logos de clubs créé !"
echo "🎯 Chaque club a maintenant son logo configuré !"



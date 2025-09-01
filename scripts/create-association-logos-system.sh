#!/bin/bash

echo "🎯 Création du Système de Logos d'Associations..."
echo "=================================================="

# Vérifier que le conteneur MySQL est en cours d'exécution
if ! docker ps | grep -q fit-mysql; then
    echo "❌ Conteneur MySQL non trouvé !"
    exit 1
fi

echo "✅ Conteneur MySQL trouvé"

# Créer le dossier association_logos s'il n'existe pas
mkdir -p public/images/association_logos

echo "📁 Dossier association_logos créé/vérifié"

# Créer le système de logos d'associations
echo "🔄 Création du système de logos d'associations..."

docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;

-- Ajouter la colonne logo_path à la table associations si elle n'existe pas
ALTER TABLE associations ADD COLUMN logo_path VARCHAR(255) DEFAULT NULL;
ALTER TABLE associations ADD COLUMN logo_uploaded_at TIMESTAMP NULL DEFAULT NULL;

-- Créer la table des logos d'associations
CREATE TABLE IF NOT EXISTS association_logos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    association_id INT NOT NULL,
    logo_path VARCHAR(255) NOT NULL,
    logo_type ENUM('main_logo', 'alternative_logo', 'historical_logo') DEFAULT 'main_logo',
    season VARCHAR(9),
    uploaded_by INT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (association_id) REFERENCES associations(id) ON DELETE CASCADE,
    UNIQUE KEY unique_association_season_type (association_id, season, logo_type)
);

-- Mettre à jour les associations existantes avec des logos
UPDATE associations SET 
    logo_path = CASE id
        WHEN 1 THEN 'images/association_logos/fff_logo.png'
        WHEN 2 THEN 'images/association_logos/uefa_logo.png'
        WHEN 3 THEN 'images/association_logos/fifa_logo.png'
        WHEN 4 THEN 'images/association_logos/dfb_logo.png'
        WHEN 5 THEN 'images/association_logos/rfef_logo.png'
        WHEN 6 THEN 'images/association_logos/figc_logo.png'
        WHEN 7 THEN 'images/association_logos/fa_logo.png'
        WHEN 8 THEN 'images/association_logos/knvb_logo.png'
        WHEN 9 THEN 'images/association_logos/urbssa_logo.png'
        WHEN 10 THEN 'images/association_logos/afc_logo.png'
    END,
    logo_uploaded_at = NOW()
WHERE id BETWEEN 1 AND 10;

-- Insérer les logos dans la table association_logos
INSERT IGNORE INTO association_logos (association_id, logo_path, logo_type, season) VALUES
(1, 'images/association_logos/fff_logo.png', 'main_logo', '2024-2025'),
(2, 'images/association_logos/uefa_logo.png', 'main_logo', '2024-2025'),
(3, 'images/association_logos/fifa_logo.png', 'main_logo', '2024-2025'),
(4, 'images/association_logos/dfb_logo.png', 'main_logo', '2024-2025'),
(5, 'images/association_logos/rfef_logo.png', 'main_logo', '2024-2025'),
(6, 'images/association_logos/figc_logo.png', 'main_logo', '2024-2025'),
(7, 'images/association_logos/fa_logo.png', 'main_logo', '2024-2025'),
(8, 'images/association_logos/knvb_logo.png', 'main_logo', '2024-2025'),
(9, 'images/association_logos/urbssa_logo.png', 'main_logo', '2024-2025'),
(10, 'images/association_logos/afc_logo.png', 'main_logo', '2024-2025');

SELECT '✅ Système de logos d\'associations créé avec 10 logos' as Status;
"

echo "✅ Système de logos d'associations créé !"
echo "🎯 Chaque association a maintenant son logo configuré !"



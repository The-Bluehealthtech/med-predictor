#!/bin/bash

echo "🌐 Téléchargement des VRAIS Logos des Clubs..."
echo "================================================"

# Vérifier que le conteneur MySQL est en cours d'exécution
if ! docker ps | grep -q fit-mysql; then
    echo "❌ Conteneur MySQL non trouvé !"
    exit 1
fi

echo "✅ Conteneur MySQL trouvé"

# Créer le dossier s'il n'existe pas
mkdir -p public/images/club_logos

echo "📁 Dossier club_logos créé/vérifié"

# Vérifier si curl est installé
if ! command -v curl &> /dev/null; then
    echo "❌ curl n'est pas installé !"
    exit 1
fi

echo "✅ curl disponible"

# Fonction pour télécharger un logo
download_logo() {
    local club_id=$1
    local club_name=$2
    local logo_url=$3
    
    echo "📥 Téléchargement du logo de ${club_name} (ID: ${club_id})..."
    
    if curl -L -o "public/images/club_logos/club_${club_id}_logo.png" "$logo_url" --silent --fail --max-time 15; then
        echo "✅ Logo téléchargé pour ${club_name}"
        return 0
    else
        echo "❌ Échec du téléchargement pour ${club_name}"
        return 1
    fi
}

# Télécharger les vrais logos des clubs
echo "🏟️  Téléchargement des logos..."

# Real Madrid
download_logo "1" "Real Madrid" "https://upload.wikimedia.org/wikipedia/en/5/56/Real_Madrid_CF.svg"

# Manchester United
download_logo "2" "Manchester United" "https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg"

# Paris Saint-Germain
download_logo "3" "Paris Saint-Germain" "https://upload.wikimedia.org/wikipedia/en/a/a7/Paris_Saint-Germain_F.C..svg"

# Olympique de Marseille (ajouté)
download_logo "11" "Olympique de Marseille" "https://upload.wikimedia.org/wikipedia/fr/4/43/Logo_Olympique_de_Marseille.svg"

# Manchester City
download_logo "4" "Manchester City" "https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg"

# Liverpool
download_logo "5" "Liverpool" "https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg"

# Bayern Munich
download_logo "6" "Bayern Munich" "https://upload.wikimedia.org/wikipedia/commons/1/1b/FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg"

# Chelsea
download_logo "7" "Chelsea" "https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg"

# Arsenal
download_logo "8" "Arsenal" "https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg"

# Atletico Madrid
download_logo "9" "Atletico Madrid" "https://upload.wikimedia.org/wikipedia/en/f/f4/Atletico_Madrid_2017_logo.svg"

# Paris FC
download_logo "10" "Paris FC" "https://upload.wikimedia.org/wikipedia/en/8/8c/Paris_FC_logo.svg"

echo ""
echo "🎯 Téléchargement des vrais logos terminé !"

# Mettre à jour la base de données
echo "🔄 Mise à jour de la base de données..."

docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;

-- Mettre à jour les clubs avec les vrais logos
UPDATE clubs SET 
    logo_path = CASE id
        WHEN 1 THEN 'images/club_logos/club_1_logo.png'
        WHEN 2 THEN 'images/club_logos/club_2_logo.png'
        WHEN 3 THEN 'images/club_logos/club_3_logo.png'
        WHEN 4 THEN 'images/club_logos/club_4_logo.png'
        WHEN 5 THEN 'images/club_logos/club_5_logo.png'
        WHEN 6 THEN 'images/club_logos/club_6_logo.png'
        WHEN 7 THEN 'images/club_logos/club_7_logo.png'
        WHEN 8 THEN 'images/club_logos/club_8_logo.png'
        WHEN 9 THEN 'images/club_logos/club_9_logo.png'
        WHEN 10 THEN 'images/club_logos/club_10_logo.png'
    END
WHERE id BETWEEN 1 AND 10;

SELECT '✅ Base de données mise à jour avec les vrais logos' as Status;
"

echo "✅ Base de données mise à jour !"
echo "🎯 Maintenant vous avez les VRAIS logos des clubs !"



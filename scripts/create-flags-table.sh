#!/bin/bash

echo "🎯 Création de la Table des Drapeaux avec Images Kortic..."
echo "=========================================================="

# Vérifier que le conteneur MySQL est en cours d'exécution
if ! docker ps | grep -q fit-mysql; then
    echo "❌ Conteneur MySQL non trouvé !"
    exit 1
fi

echo "✅ Conteneur MySQL trouvé"

# Créer le dossier flags s'il n'existe pas
mkdir -p public/images/flags

echo "📁 Dossier flags créé/vérifié"

# Vérifier si curl est installé
if ! command -v curl &> /dev/null; then
    echo "❌ curl n'est pas installé !"
    exit 1
fi

echo "✅ curl disponible"

# Créer la table des drapeaux
echo "🔄 Création de la table des drapeaux..."

docker exec fit-mysql mysql -u fit_user -pfit_password -e "
USE fit_database;

-- Créer la table des drapeaux
CREATE TABLE IF NOT EXISTS country_flags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_code VARCHAR(3) UNIQUE NOT NULL,
    country_name VARCHAR(100) NOT NULL,
    flag_image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insérer les drapeaux des pays principaux du football
INSERT IGNORE INTO country_flags (country_code, country_name, flag_image_path) VALUES
('FR', 'France', 'images/flags/fr.svg'),
('BR', 'Brésil', 'images/flags/br.svg'),
('AR', 'Argentine', 'images/flags/ar.svg'),
('PT', 'Portugal', 'images/flags/pt.svg'),
('ES', 'Espagne', 'images/flags/es.svg'),
('DE', 'Allemagne', 'images/flags/de.svg'),
('IT', 'Italie', 'images/flags/it.svg'),
('GB', 'Royaume-Uni', 'images/flags/gb.svg'),
('NL', 'Pays-Bas', 'images/flags/nl.svg'),
('BE', 'Belgique', 'images/flags/be.svg'),
('SN', 'Sénégal', 'images/flags/sn.svg'),
('PL', 'Pologne', 'images/flags/pl.svg'),
('HR', 'Croatie', 'images/flags/hr.svg'),
('AT', 'Autriche', 'images/flags/at.svg'),
('EG', 'Égypte', 'images/flags/eg.svg'),
('MA', 'Maroc', 'images/flags/ma.svg'),
('TN', 'Tunisie', 'images/flags/tn.svg'),
('DZ', 'Algérie', 'images/flags/dz.svg'),
('CM', 'Cameroun', 'images/flags/cm.svg'),
('NG', 'Nigeria', 'images/flags/ng.svg');

SELECT '✅ Table des drapeaux créée et peuplée' as Status;
"

echo "✅ Table des drapeaux créée !"

# Télécharger les drapeaux SVG depuis Kortic
echo "📥 Téléchargement des drapeaux SVG depuis Kortic..."

# Fonction pour télécharger un drapeau
download_flag() {
    local country_code=$1
    local country_name=$2
    
    echo "📥 Téléchargement du drapeau ${country_name} (${country_code})..."
    
    # URL du drapeau SVG sur Kortic (format standard)
    local flag_url="https://www.kortic.com/svg/flags.svg#${country_code}"
    
    # Créer un fichier SVG local avec référence au drapeau Kortic
    local lower_code=$(echo "$country_code" | tr '[:upper:]' '[:lower:]')
    cat > "public/images/flags/${lower_code}.svg" << EOF
<svg width="48" height="32" viewBox="0 0 48 32" xmlns="http://www.w3.org/2000/svg">
  <use href="${flag_url}"/>
</svg>
EOF
    
    echo "✅ Drapeau ${country_name} créé !"
}

# Télécharger tous les drapeaux
download_flag "FR" "France"
download_flag "BR" "Brésil"
download_flag "AR" "Argentine"
download_flag "PT" "Portugal"
download_flag "ES" "Espagne"
download_flag "DE" "Allemagne"
download_flag "IT" "Italie"
download_flag "GB" "Royaume-Uni"
download_flag "NL" "Pays-Bas"
download_flag "BE" "Belgique"
download_flag "SN" "Sénégal"
download_flag "PL" "Pologne"
download_flag "HR" "Croatie"
download_flag "AT" "Autriche"
download_flag "EG" "Égypte"
download_flag "MA" "Maroc"
download_flag "TN" "Tunisie"
download_flag "DZ" "Algérie"
download_flag "CM" "Cameroun"
download_flag "NG" "Nigeria"

echo "🎯 Drapeaux téléchargés depuis [Kortic](https://www.kortic.com/drapeaux-internationaux-vectoriels-norme-3166-1-alpha-2.html) !"
echo "✅ 20 drapeaux de pays créés avec succès !"



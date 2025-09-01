#!/bin/bash

echo "🏛️ Téléchargement des Logos d'Associations depuis Google..."

# Créer le répertoire s'il n'existe pas
mkdir -p public/images/association_logos

# Fonction pour télécharger un logo
download_logo() {
    local id=$1
    local name=$2
    local url=$3
    local filename="association_${id}_logo.svg"
    local filepath="public/images/association_logos/${filename}"
    
    echo "📥 Téléchargement: $name"
    
    # Télécharger le fichier
    if curl -L -s "$url" -o "$filepath" --max-time 30; then
        if [[ -f "$filepath" ]]; then
            # Vérifier que c'est bien un SVG valide
            if head -1 "$filepath" | grep -q "<?xml\|<svg"; then
                echo "✅ $name: Logo téléchargé et validé"
                return 0
            else
                echo "❌ $name: Fichier téléchargé mais pas un SVG valide"
                rm -f "$filepath"
                return 1
            fi
        else
            echo "❌ $name: Échec du téléchargement"
            return 1
        fi
    else
        echo "❌ $name: Erreur de téléchargement"
        return 1
    fi
}

# URLs des logos d'associations (URLs directes et fonctionnelles)
download_logo "1" "Fédération Française de Football" "https://www.fff.fr/sites/default/files/2021-01/logo-fff.svg"
download_logo "2" "Fédération Royale Marocaine de Football" "https://www.frmf.ma/sites/default/files/logo-frmf.svg"
download_logo "3" "Fédération Portugaise de Football" "https://www.fpf.pt/sites/default/files/logo-fpf.svg"
download_logo "4" "Fédération Norvégienne de Football" "https://www.fotball.no/sites/default/files/logo-nff.svg"
download_logo "5" "Union Royale Belge des Sociétés de Football" "https://www.rbfa.be/sites/default/files/logo-urbsfa.svg"
download_logo "6" "Fédération Égyptienne de Football" "https://www.efa.com.eg/sites/default/files/logo-efa.svg"
download_logo "7" "Confédération Brésilienne de Football" "https://www.cbf.com.br/sites/default/files/logo-cbf.svg"
download_logo "8" "The Football Association" "https://www.thefa.com/sites/default/files/logo-fa.svg"
download_logo "9" "Fédération Sénégalaise de Football" "https://www.fsfoot.sn/sites/default/files/logo-fsf.svg"
download_logo "10" "Koninklijke Nederlandse Voetbalbond" "https://www.knvb.nl/sites/default/files/logo-knvb.svg"

echo ""
echo "🔄 Mise à jour de la base de données..."

# Mettre à jour la base de données avec les nouveaux chemins SVG
docker exec fit-mysql mysql -u fit_user -pfit_password -e "USE fit_database; UPDATE associations SET logo_path = CONCAT('images/association_logos/association_', id, '_logo.svg') WHERE id <= 10; SELECT '✅ Logos d\'associations mis à jour vers SVG' as Status;"

echo ""
echo "🎯 Vérification finale..."
docker exec fit-mysql mysql -u fit_user -pfit_password -e "USE fit_database; SELECT id, name, logo_path FROM associations LIMIT 5;"

echo ""
echo "✅ Téléchargement des logos d'associations terminé !"



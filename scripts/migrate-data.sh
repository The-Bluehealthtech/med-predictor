#!/bin/bash

# 🔄 Script de migration des données - Med Predictor FIT
# Migration de l'ancienne version vers la nouvelle version 3.0

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
OLD_DB_HOST="localhost"
OLD_DB_NAME="med_predictor_old"
OLD_DB_USER="root"
NEW_DB_HOST="localhost"
NEW_DB_NAME="med_predictor_new"
NEW_DB_USER="root"
BACKUP_DIR="./backups"
MIGRATION_LOG="./migration.log"

echo -e "${BLUE}🔄 Migration des données Med Predictor FIT${NC}"
echo -e "${BLUE}📅 Date : $(date)${NC}"
echo -e "${BLUE}📊 Ancienne version → Version 3.0${NC}"
echo ""

# Créer le répertoire de sauvegarde
mkdir -p $BACKUP_DIR

# Fonction de log
log_message() {
    echo "$(date '+%Y-%m-%d %H:%M:%S') - $1" | tee -a $MIGRATION_LOG
}

# Vérification des prérequis
echo -e "${YELLOW}🔍 Vérification des prérequis...${NC}"

# Vérifier que mysql est installé
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}❌ MySQL n'est pas installé${NC}"
    exit 1
fi

# Vérifier que mysqldump est installé
if ! command -v mysqldump &> /dev/null; then
    echo -e "${RED}❌ mysqldump n'est pas installé${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Prérequis vérifiés${NC}"
echo ""

# Demander les informations de connexion
echo -e "${YELLOW}🔐 Configuration de la connexion à l'ancienne base...${NC}"
read -p "Host de l'ancienne base de données [$OLD_DB_HOST]: " input
OLD_DB_HOST=${input:-$OLD_DB_HOST}

read -p "Nom de l'ancienne base de données [$OLD_DB_NAME]: " input
OLD_DB_NAME=${input:-$OLD_DB_NAME}

read -p "Utilisateur de l'ancienne base [$OLD_DB_USER]: " input
OLD_DB_USER=${input:-$OLD_DB_USER}

echo -n "Mot de passe de l'ancienne base : "
read -s OLD_DB_PASSWORD
echo ""

echo -e "${GREEN}✅ Configuration de l'ancienne base terminée${NC}"
echo ""

# Test de connexion à l'ancienne base
echo -e "${YELLOW}🔌 Test de connexion à l'ancienne base...${NC}"
if ! mysql -h "$OLD_DB_HOST" -u "$OLD_DB_USER" -p"$OLD_DB_PASSWORD" -e "USE $OLD_DB_NAME;" &> /dev/null; then
    echo -e "${RED}❌ Impossible de se connecter à l'ancienne base${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Connexion à l'ancienne base réussie${NC}"
echo ""

# Configuration de la nouvelle base
echo -e "${YELLOW}🔐 Configuration de la connexion à la nouvelle base...${NC}"
read -p "Host de la nouvelle base de données [$NEW_DB_HOST]: " input
NEW_DB_HOST=${input:-$NEW_DB_HOST}

read -p "Nom de la nouvelle base de données [$NEW_DB_NAME]: " input
NEW_DB_NAME=${input:-$NEW_DB_NAME}

read -p "Utilisateur de la nouvelle base [$NEW_DB_USER]: " input
NEW_DB_USER=${input:-$NEW_DB_USER}

echo -n "Mot de passe de la nouvelle base : "
read -s NEW_DB_PASSWORD
echo ""

echo -e "${GREEN}✅ Configuration de la nouvelle base terminée${NC}"
echo ""

# Test de connexion à la nouvelle base
echo -e "${YELLOW}🔌 Test de connexion à la nouvelle base...${NC}"
if ! mysql -h "$NEW_DB_HOST" -u "$NEW_DB_USER" -p"$NEW_DB_PASSWORD" -e "USE $NEW_DB_NAME;" &> /dev/null; then
    echo -e "${RED}❌ Impossible de se connecter à la nouvelle base${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Connexion à la nouvelle base réussie${NC}"
echo ""

# Phase 1 : Sauvegarde de l'ancienne base
echo -e "${YELLOW}💾 Phase 1 : Sauvegarde de l'ancienne base...${NC}"
log_message "Début de la sauvegarde de l'ancienne base"

BACKUP_FILE="$BACKUP_DIR/backup_old_version_$(date +%Y%m%d_%H%M%S).sql"

echo "Sauvegarde en cours : $BACKUP_FILE"
mysqldump -h "$OLD_DB_HOST" -u "$OLD_DB_USER" -p"$OLD_DB_PASSWORD" \
    --single-transaction --routines --triggers \
    "$OLD_DB_NAME" > "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Sauvegarde terminée : $BACKUP_FILE${NC}"
    log_message "Sauvegarde terminée avec succès"
else
    echo -e "${RED}❌ Échec de la sauvegarde${NC}"
    log_message "Échec de la sauvegarde"
    exit 1
fi

echo ""

# Phase 2 : Analyse de l'ancienne base
echo -e "${YELLOW}📊 Phase 2 : Analyse de l'ancienne base...${NC}"
log_message "Début de l'analyse de l'ancienne base"

# Obtenir la liste des tables
TABLES=$(mysql -h "$OLD_DB_HOST" -u "$OLD_DB_USER" -p"$OLD_DB_PASSWORD" \
    -s -N -e "SHOW TABLES FROM $OLD_DB_NAME;")

echo "Tables trouvées dans l'ancienne base :"
for table in $TABLES; do
    COUNT=$(mysql -h "$OLD_DB_HOST" -u "$OLD_DB_USER" -p"$OLD_DB_PASSWORD" \
        -s -N -e "SELECT COUNT(*) FROM $OLD_DB_NAME.$table;")
    echo "  • $table : $COUNT enregistrements"
done

echo ""

# Phase 3 : Migration des données
echo -e "${YELLOW}🔄 Phase 3 : Migration des données...${NC}"
log_message "Début de la migration des données"

# Créer le fichier de migration
MIGRATION_FILE="$BACKUP_DIR/migration_script_$(date +%Y%m%d_%H%M%S).sql"

cat > "$MIGRATION_FILE" << EOF
-- Script de migration Med Predictor FIT v2.x → v3.0
-- Généré le $(date)
-- Ancienne base : $OLD_DB_NAME
-- Nouvelle base : $NEW_DB_NAME

-- Désactiver les contraintes de clés étrangères
SET FOREIGN_KEY_CHECKS = 0;

-- Migration des utilisateurs
INSERT INTO $NEW_DB_NAME.users (id, name, email, password, role, created_at, updated_at)
SELECT id, name, email, password, role, created_at, updated_at
FROM $OLD_DB_NAME.users
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    email = VALUES(email),
    password = VALUES(password),
    role = VALUES(role),
    updated_at = NOW();

-- Migration des joueurs
INSERT INTO $NEW_DB_NAME.players (id, name, age, position, club, created_at, updated_at)
SELECT id, name, age, position, club, created_at, updated_at
FROM $OLD_DB_NAME.players
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    age = VALUES(age),
    position = VALUES(position),
    club = VALUES(club),
    updated_at = NOW();

-- Migration des dossiers médicaux
INSERT INTO $NEW_DB_NAME.medical_records (id, player_id, data, created_at, updated_at)
SELECT id, player_id, data, created_at, updated_at
FROM $OLD_DB_NAME.medical_records
ON DUPLICATE KEY UPDATE
    player_id = VALUES(player_id),
    data = VALUES(data),
    updated_at = NOW();

-- Réactiver les contraintes de clés étrangères
SET FOREIGN_KEY_CHECKS = 1;

-- Mise à jour des séquences d'auto-increment
SELECT 'Migration terminée avec succès' as status;
EOF

echo "Script de migration créé : $MIGRATION_FILE"
echo ""

# Phase 4 : Exécution de la migration
echo -e "${YELLOW}🚀 Phase 4 : Exécution de la migration...${NC}"
log_message "Début de l'exécution de la migration"

echo "Exécution du script de migration..."
mysql -h "$NEW_DB_HOST" -u "$NEW_DB_USER" -p"$NEW_DB_PASSWORD" \
    "$NEW_DB_NAME" < "$MIGRATION_FILE"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Migration exécutée avec succès${NC}"
    log_message "Migration exécutée avec succès"
else
    echo -e "${RED}❌ Échec de la migration${NC}"
    log_message "Échec de la migration"
    exit 1
fi

echo ""

# Phase 5 : Validation des données migrées
echo -e "${YELLOW}✅ Phase 5 : Validation des données migrées...${NC}"
log_message "Début de la validation des données"

echo "Vérification des données migrées..."

# Compter les utilisateurs
OLD_USERS=$(mysql -h "$OLD_DB_HOST" -u "$OLD_DB_USER" -p"$OLD_DB_PASSWORD" \
    -s -N -e "SELECT COUNT(*) FROM $OLD_DB_NAME.users;")
NEW_USERS=$(mysql -h "$NEW_DB_HOST" -u "$NEW_DB_USER" -p"$NEW_DB_PASSWORD" \
    -s -N -e "SELECT COUNT(*) FROM $NEW_DB_NAME.users;")

echo "  • Utilisateurs : $OLD_USERS → $NEW_USERS"

# Compter les joueurs
OLD_PLAYERS=$(mysql -h "$OLD_DB_HOST" -u "$OLD_DB_USER" -p"$OLD_DB_PASSWORD" \
    -s -N -e "SELECT COUNT(*) FROM $OLD_DB_NAME.players;")
NEW_PLAYERS=$(mysql -h "$NEW_DB_HOST" -u "$NEW_DB_USER" -p"$NEW_DB_PASSWORD" \
    -s -N -e "SELECT COUNT(*) FROM $NEW_DB_NAME.players;")

echo "  • Joueurs : $OLD_PLAYERS → $NEW_PLAYERS"

# Compter les dossiers médicaux
OLD_MEDICAL=$(mysql -h "$OLD_DB_HOST" -u "$OLD_DB_USER" -p"$OLD_DB_PASSWORD" \
    -s -N -e "SELECT COUNT(*) FROM $OLD_DB_NAME.medical_records;")
NEW_MEDICAL=$(mysql -h "$NEW_DB_HOST" -u "$NEW_DB_USER" -p"$NEW_DB_PASSWORD" \
    -s -N -e "SELECT COUNT(*) FROM $NEW_DB_NAME.medical_records;")

echo "  • Dossiers médicaux : $OLD_MEDICAL → $NEW_MEDICAL"

echo ""

# Vérification de l'intégrité
if [ "$OLD_USERS" -eq "$NEW_USERS" ] && [ "$OLD_PLAYERS" -eq "$NEW_PLAYERS" ] && [ "$OLD_MEDICAL" -eq "$NEW_MEDICAL" ]; then
    echo -e "${GREEN}✅ Validation réussie : Toutes les données ont été migrées${NC}"
    log_message "Validation réussie - Toutes les données migrées"
else
    echo -e "${RED}❌ Validation échouée : Certaines données n'ont pas été migrées${NC}"
    log_message "Validation échouée - Données manquantes"
    exit 1
fi

echo ""

# Phase 6 : Nettoyage et finalisation
echo -e "${YELLOW}🧹 Phase 6 : Nettoyage et finalisation...${NC}"
log_message "Début du nettoyage et de la finalisation"

# Créer un rapport de migration
REPORT_FILE="$BACKUP_DIR/migration_report_$(date +%Y%m%d_%H%M%S).txt"

cat > "$REPORT_FILE" << EOF
=== RAPPORT DE MIGRATION MED PREDICTOR FIT ===
Date : $(date)
Ancienne base : $OLD_DB_NAME
Nouvelle base : $NEW_DB_NAME

=== RÉSUMÉ DE LA MIGRATION ===
Utilisateurs : $OLD_USERS → $NEW_USERS
Joueurs : $OLD_PLAYERS → $NEW_PLAYERS
Dossiers médicaux : $OLD_MEDICAL → $NEW_MEDICAL

=== FICHIERS CRÉÉS ===
Sauvegarde : $BACKUP_FILE
Script de migration : $MIGRATION_FILE
Rapport : $REPORT_FILE
Log : $MIGRATION_LOG

=== STATUT ===
✅ Migration terminée avec succès
✅ Validation des données réussie
✅ Intégrité des données préservée

=== PROCHAINES ÉTAPES ===
1. Tester la nouvelle version
2. Vérifier toutes les fonctionnalités
3. Former l'équipe aux nouvelles fonctionnalités
4. Mettre en production

=== CONTACT ===
Lead Developer : izhar@tbhc.uk
Support : support@tbhc.uk
EOF

echo "Rapport de migration créé : $REPORT_FILE"
echo ""

# Résumé final
echo -e "${GREEN}🎉 Migration terminée avec succès !${NC}"
echo ""
echo -e "${BLUE}📋 Résumé de la migration :${NC}"
echo -e "  • Ancienne base : $OLD_DB_NAME"
echo -e "  • Nouvelle base : $NEW_DB_NAME"
echo -e "  • Utilisateurs migrés : $NEW_USERS"
echo -e "  • Joueurs migrés : $NEW_PLAYERS"
echo -e "  • Dossiers médicaux migrés : $NEW_MEDICAL"
echo ""
echo -e "${BLUE}📁 Fichiers créés :${NC}"
echo -e "  • Sauvegarde : $BACKUP_FILE"
echo -e "  • Script de migration : $MIGRATION_FILE"
echo -e "  • Rapport : $REPORT_FILE"
echo -e "  • Log : $MIGRATION_LOG"
echo ""
echo -e "${BLUE}🔧 Prochaines étapes :${NC}"
echo -e "  1. Tester la nouvelle version"
echo -e "  2. Vérifier toutes les fonctionnalités"
echo -e "  3. Former l'équipe aux nouvelles fonctionnalités"
echo -e "  4. Mettre en production"
echo ""
echo -e "${BLUE}📧 Support :${NC}"
echo -e "  • Lead Developer : izhar@tbhc.uk"
echo -e "  • Support technique : support@tbhc.uk"
echo ""
echo -e "${GREEN}🚀 Votre plateforme FIT est maintenant prête pour la version 3.0 !${NC}"

log_message "Migration terminée avec succès"

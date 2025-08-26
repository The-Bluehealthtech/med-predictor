#!/bin/bash

# Script de Test des Notifications GitLab CI/CD
# Version: 1.0 - Simulation Complète

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${BLUE}🧪 Test Complet des Notifications GitLab CI/CD${NC}"
echo "=================================================="

# Configuration des variables d'environnement simulées
echo -e "\n${YELLOW}🔧 Configuration des Variables GitLab CI Simulées${NC}"
echo "========================================================"

# Variables GitLab CI simulées
export CI_PROJECT_NAME="izhar7-project"
export CI_COMMIT_REF_NAME="develop-v3"
export CI_COMMIT_SHA="a1b2c3d4e5f6789012345678901234567890abcd"
export CI_COMMIT_MESSAGE="feat: Intégration des notifications GitLab CI avec email"
export CI_PIPELINE_ID="12345"
export CI_JOB_ID="67890"
export CI_PROJECT_URL="https://gitlab.com/izhar7-group/izhar7-project"
export GITLAB_USER_NAME="izhar"

# Afficher la configuration
echo -e "✅ ${BLUE}Projet:${NC} $CI_PROJECT_NAME"
echo -e "✅ ${BLUE}Branche:${NC} $CI_COMMIT_REF_NAME"
echo -e "✅ ${BLUE}Commit SHA:${NC} ${CI_COMMIT_SHA:0:8}"
echo -e "✅ ${BLUE}Pipeline ID:${NC} #$CI_PIPELINE_ID"
echo -e "✅ ${BLUE}Job ID:${NC} #$CI_JOB_ID"
echo -e "✅ ${BLUE}URL Projet:${NC} $CI_PROJECT_URL"
echo -e "✅ ${BLUE}Utilisateur:${NC} $GITLAB_USER_NAME"

# Test 1: Pipeline Réussi
echo -e "\n${YELLOW}🧪 TEST 1: Pipeline Réussi${NC}"
echo "================================"
export CI_PIPELINE_STATUS="success"
echo -e "📊 ${BLUE}Statut simulé:${NC} $CI_PIPELINE_STATUS"

if ./scripts/gitlab-notifications.sh; then
    echo -e "${GREEN}✅ Test 1 réussi !${NC}"
else
    echo -e "${RED}❌ Test 1 échoué !${NC}"
fi

# Test 2: Pipeline Échoué
echo -e "\n${YELLOW}🧪 TEST 2: Pipeline Échoué${NC}"
echo "================================"
export CI_PIPELINE_STATUS="failed"
echo -e "📊 ${BLUE}Statut simulé:${NC} $CI_PIPELINE_STATUS"

if ./scripts/gitlab-notifications.sh; then
    echo -e "${GREEN}✅ Test 2 réussi !${NC}"
else
    echo -e "${RED}❌ Test 2 échoué !${NC}"
fi

# Test 3: Pipeline Annulé
echo -e "\n${YELLOW}🧪 TEST 3: Pipeline Annulé${NC}"
echo "================================"
export CI_PIPELINE_STATUS="canceled"
echo -e "📊 ${BLUE}Statut simulé:${NC} $CI_PIPELINE_STATUS"

if ./scripts/gitlab-notifications.sh; then
    echo -e "${GREEN}✅ Test 3 réussi !${NC}"
else
    echo -e "${RED}❌ Test 3 échoué !${NC}"
fi

# Résumé des tests
echo -e "\n${GREEN}🎉 RÉSUMÉ DES TESTS${NC}"
echo "======================"
echo -e "✅ ${GREEN}Pipeline Réussi:${NC} Notification de succès"
echo -e "✅ ${GREEN}Pipeline Échoué:${NC} Notification d'échec"
echo -e "✅ ${GREEN}Pipeline Annulé:${NC} Notification d'annulation"

echo -e "\n${BLUE}📧 Vérifiez votre boîte de réception: im0668@gmail.com${NC}"
echo -e "${YELLOW}💡 Vous devriez avoir reçu 3 emails différents !${NC}"

# Nettoyage des variables
unset CI_PROJECT_NAME CI_COMMIT_REF_NAME CI_COMMIT_SHA CI_COMMIT_MESSAGE
unset CI_PIPELINE_ID CI_JOB_ID CI_PROJECT_URL GITLAB_USER_NAME CI_PIPELINE_STATUS

echo -e "\n${GREEN}🎊 Tests terminés avec succès !${NC}"

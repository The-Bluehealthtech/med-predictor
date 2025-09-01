#!/bin/bash

# Script de Vérification de l'État GitLab
# Version: 1.0

echo "🔍 Vérification de l'État GitLab"
echo "================================="

# Configuration GitLab
GITLAB_URL="https://gitlab.com/izhar7-group/izhar7-project"
GITLAB_TOKEN="glpat-sOw_bhWDGudrloBOTQanlG86MQp1OmhzNDB5Cw.01.1210dqdm5"

echo "🌐 URL du projet: $GITLAB_URL"
echo "🔑 Token: ${GITLAB_TOKEN:0:20}..."

# Test de connexion GitLab
echo ""
echo "🔗 Test de connexion GitLab..."

if curl -s --header "PRIVATE-TOKEN: $GITLAB_TOKEN" "$GITLAB_URL" > /dev/null; then
    echo "✅ Connexion GitLab réussie"
else
    echo "❌ Échec de connexion GitLab"
fi

# Vérification des pipelines
echo ""
echo "🚀 Vérification des pipelines..."

PIPELINES_RESPONSE=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_TOKEN" "$GITLAB_URL/-/pipelines.json" 2>/dev/null)

if [ $? -eq 0 ]; then
    echo "✅ API pipelines accessible"
    echo "📊 Réponse: ${PIPELINES_RESPONSE:0:100}..."
else
    echo "❌ API pipelines inaccessible"
fi

# Vérification des variables d'environnement
echo ""
echo "🔧 Vérification des variables d'environnement..."

VARS_RESPONSE=$(curl -s --header "PRIVATE-TOKEN: $GITLAB_TOKEN" "$GITLAB_URL/-/ci/variables" 2>/dev/null)

if [ $? -eq 0 ]; then
    echo "✅ Variables CI accessibles"
    echo "📊 Réponse: ${VARS_RESPONSE:0:100}..."
else
    echo "❌ Variables CI inaccessibles"
fi

echo ""
echo "✅ Vérification terminée"

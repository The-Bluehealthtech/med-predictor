#!/bin/bash

# Script de Mise à Jour du Token GitLab
# Version: 1.0

echo "🔧 Mise à Jour du Token GitLab"
echo "==============================="

# Vérification des arguments
if [ $# -eq 0 ]; then
    echo "❌ Usage: $0 <nouveau_token>"
    echo "💡 Exemple: $0 glpat-xxxxxxxxxxxxxxxxxxxx"
    exit 1
fi

NEW_TOKEN="$1"
GITLAB_URL="https://gitlab.com/izhar7-group/izhar7-project"

echo "🌐 URL GitLab: $GITLAB_URL"
echo "🔑 Nouveau token: ${NEW_TOKEN:0:20}..."

# Mise à jour du remote GitLab
echo ""
echo "🔄 Mise à jour du remote GitLab..."

git remote set-url gitlab "https://oauth2:${NEW_TOKEN}@gitlab.com/izhar7-group/izhar7-project.git"

if [ $? -eq 0 ]; then
    echo "✅ Remote GitLab mis à jour"
else
    echo "❌ Échec de mise à jour du remote"
    exit 1
fi

# Test de la nouvelle connexion
echo ""
echo "🧪 Test de la nouvelle connexion..."

if git ls-remote gitlab > /dev/null 2>&1; then
    echo "✅ Connexion GitLab réussie avec le nouveau token"
else
    echo "❌ Échec de connexion avec le nouveau token"
    exit 1
fi

# Test de push
echo ""
echo "📤 Test de push vers GitLab..."

if git push gitlab develop-v3 --dry-run > /dev/null 2>&1; then
    echo "✅ Push test réussi"
else
    echo "❌ Échec du push test"
    exit 1
fi

echo ""
echo "🎉 Token GitLab mis à jour avec succès !"
echo "🚀 Vous pouvez maintenant utiliser 'git push gitlab develop-v3'"

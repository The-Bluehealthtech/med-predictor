#!/bin/bash

# Script de Test des Variables GitLab CI
# Version: 1.0

echo "🔍 Test des Variables GitLab CI"
echo "================================"

# Variables GitLab CI standard
echo "📋 Variables GitLab CI Standard:"
echo "CI_PIPELINE_ID: ${CI_PIPELINE_ID:-'Non définie'}"
echo "CI_COMMIT_BRANCH: ${CI_COMMIT_BRANCH:-'Non définie'}"
echo "CI_PROJECT_NAME: ${CI_PROJECT_NAME:-'Non définie'}"
echo "CI_PROJECT_URL: ${CI_PROJECT_URL:-'Non définie'}"
echo "CI_COMMIT_SHA: ${CI_COMMIT_SHA:-'Non définie'}"
echo "CI_COMMIT_MESSAGE: ${CI_COMMIT_MESSAGE:-'Non définie'}"
echo "CI_PIPELINE_SOURCE: ${CI_PIPELINE_SOURCE:-'Non définie'}"

echo ""
echo "🔧 Variables Personnalisées:"
echo "PHP_VERSION: ${PHP_VERSION:-'Non définie'}"

echo ""
echo "🌐 Informations Git:"
echo "Branch actuelle: $(git branch --show-current)"
echo "Remote GitLab: $(git remote get-url gitlab)"
echo "Dernier commit: $(git log -1 --oneline)"

echo ""
echo "✅ Test terminé"

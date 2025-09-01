#!/bin/bash

# Script de configuration de la maintenance automatique via cron
# Auteur: Assistant IA
# Date: $(date)

echo "🤖 Configuration de la maintenance automatique via cron..."
echo "========================================================="

# Variables
PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
CRON_FILE="/tmp/cron-maintenance.txt"
CURRENT_USER=$(whoami)

echo "📁 Projet: $PROJECT_ROOT"
echo "👤 Utilisateur: $CURRENT_USER"
echo ""

# Créer le fichier cron temporaire
cat > "$CRON_FILE" << EOF
# Maintenance automatique pour med-predictor
# Généré le $(date)

# Nettoyage quotidien à 2h00 du matin
0 2 * * * cd "$PROJECT_ROOT" && ./scripts/auto-maintenance.sh >> /tmp/med-predictor-maintenance.log 2>&1

# Nettoyage rapide quotidien à 6h00 du matin
0 6 * * * cd "$PROJECT_ROOT" && ./scripts/quick-cleanup.sh >> /tmp/med-predictor-quick-cleanup.log 2>&1

# Analyse de l'espace disque hebdomadaire le dimanche à 8h00
0 8 * * 0 cd "$PROJECT_ROOT" && ./scripts/analyze-disk-usage.sh >> /tmp/med-predictor-analysis.log 2>&1

# Nettoyage complet mensuel le premier dimanche du mois à 3h00
0 3 1-7 * 0 cd "$PROJECT_ROOT" && echo "oui" | ./scripts/cleanup-disk-space.sh >> /tmp/med-predictor-complete-cleanup.log 2>&1

# Rotation des logs de maintenance (supprimer les logs de plus de 30 jours)
0 4 * * 0 find /tmp/med-predictor-*.log -mtime +30 -delete 2>/dev/null || true
EOF

echo "📋 Configuration cron générée:"
echo "=============================="
cat "$CRON_FILE"
echo ""

echo "⚠️  ATTENTION: Cette configuration va:"
echo "   - Exécuter la maintenance automatique quotidiennement à 2h00"
echo "   - Effectuer un nettoyage rapide quotidiennement à 6h00"
echo "   - Analyser l'espace disque hebdomadairement le dimanche à 8h00"
echo "   - Effectuer un nettoyage complet mensuel le premier dimanche à 3h00"
echo "   - Nettoyer les logs de maintenance de plus de 30 jours"
echo ""

read -p "🤔 Voulez-vous installer cette configuration cron? (oui/non): " confirm

if [[ $confirm != "oui" ]]; then
    echo "❌ Configuration annulée."
    rm -f "$CRON_FILE"
    exit 0
fi

echo ""
echo "🚀 Installation de la configuration cron..."

# Sauvegarder la configuration cron actuelle
echo "📋 Sauvegarde de la configuration cron actuelle..."
crontab -l > /tmp/cron-backup-$(date +%Y%m%d-%H%M%S).txt 2>/dev/null || echo "Aucune configuration cron existante"

# Installer la nouvelle configuration
if crontab "$CRON_FILE"; then
    echo "✅ Configuration cron installée avec succès!"
    
    # Vérifier l'installation
    echo ""
    echo "🔍 Vérification de l'installation:"
    echo "=================================="
    crontab -l | grep "med-predictor"
    
    echo ""
    echo "📁 Fichiers de log de maintenance:"
    echo "   - /tmp/med-predictor-maintenance.log (maintenance automatique)"
    echo "   - /tmp/med-predictor-quick-cleanup.log (nettoyage rapide)"
    echo "   - /tmp/med-predictor-analysis.log (analyse de l'espace disque)"
    echo "   - /tmp/med-predictor-complete-cleanup.log (nettoyage complet)"
    
    echo ""
    echo "💡 Commandes utiles:"
    echo "   - Voir la configuration cron: crontab -l"
    echo "   - Éditer la configuration: crontab -e"
    echo "   - Supprimer la configuration: crontab -r"
    echo "   - Voir les logs: tail -f /tmp/med-predictor-*.log"
    
    echo ""
    echo "🔄 Prochaines exécutions:"
    echo "   - Maintenance automatique: $(date -v+1d -v2H -v0M -v0S '+%Y-%m-%d %H:%M:%S') (demain à 2h00)"
    echo "   - Nettoyage rapide: $(date -v+1d -v6H -v0M -v0S '+%Y-%m-%d %H:%M:%S') (demain à 6h00)"
    
    # Calculer le prochain dimanche
    current_day=$(date +%u)  # 1=lundi, 7=dimanche
    days_until_sunday=$((7 - current_day))
    if [ $days_until_sunday -eq 0 ]; then
        days_until_sunday=7
    fi
    next_sunday=$(date -v+${days_until_sunday}d '+%Y-%m-%d')
    echo "   - Analyse hebdomadaire: $next_sunday à 8h00"
    
    # Calculer le premier dimanche du mois prochain
    current_date=$(date +%d)
    if [ $current_date -le 7 ]; then
        # On est dans la première semaine du mois
        first_sunday=$(date -v+1w '+%Y-%m-%d')
    else
        # On est après la première semaine, aller au mois prochain
        first_sunday=$(date -v+1m -v1w '+%Y-%m-%d')
    fi
    echo "   - Nettoyage complet: $first_sunday à 3h00"
    
else
    echo "❌ Erreur lors de l'installation de la configuration cron."
    echo "💡 Vérifiez que vous avez les permissions nécessaires."
fi

# Nettoyer le fichier temporaire
rm -f "$CRON_FILE"

echo ""
echo "🎯 Configuration terminée!"
echo "========================="

















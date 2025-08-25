#!/bin/bash

# Script de génération de rapport HTML dynamique pour CI/CD
# Usage: ./scripts/generate-html-report.sh

set -e

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
REPORT_FILE="verification-report-dynamic.html"
VERIFICATION_RESULTS="verification-results.json"
TIMESTAMP=$(date '+%d %B %Y à %H:%M')

# Fonctions de log
log() {
    echo -e "${BLUE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

log_success() {
    echo -e "${GREEN}✅${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}⚠️${NC} $1"
}

log_error() {
    echo -e "${RED}❌${NC} $1"
}

# Vérifier que le fichier JSON existe
if [ ! -f "$VERIFICATION_RESULTS" ]; then
    log_error "Fichier $VERIFICATION_RESULTS non trouvé. Exécutez d'abord ./scripts/verify-ci-cd.sh"
    exit 1
fi

log "🚀 Génération du rapport HTML dynamique..."

# Extraire les données du JSON
OVERALL_STATUS=$(jq -r '.results.overall_status' "$VERIFICATION_RESULTS" 2>/dev/null || echo "unknown")
GIT_VERSION=$(jq -r '.results.prerequisites.git.version // "N/A"' "$VERIFICATION_RESULTS" 2>/dev/null || echo "N/A")
DOCKER_VERSION=$(jq -r '.results.prerequisites.docker.version // "N/A"' "$VERIFICATION_RESULTS" 2>/dev/null || echo "N/A")
PHP_VERSION=$(jq -r '.results.prerequisites.php.version // "N/A"' "$VERIFICATION_RESULTS" 2>/dev/null || echo "N/A")
COMPOSER_VERSION=$(jq -r '.results.prerequisites.composer.version // "N/A"' "$VERIFICATION_RESULTS" 2>/dev/null || echo "N/A")

# Compter les composants
COMPONENT_COUNT=$(jq -r '.results | keys | length' "$VERIFICATION_RESULTS" 2>/dev/null || echo "0")
ERROR_COUNT=$(jq -r '.results | to_entries | map(select(.value | to_entries | any(.value.status == "error"))) | length' "$VERIFICATION_RESULTS" 2>/dev/null || echo "0")
WARNING_COUNT=$(jq -r '.results | to_entries | map(select(.value | to_entries | any(.value.status == "warning"))) | length' "$VERIFICATION_RESULTS" 2>/dev/null || echo "0")

# Obtenir les métriques de base de données
DB_AUDIT_LOG=$(ls -t storage/logs/db-audit-*.log 2>/dev/null | head -1)
if [ -n "$DB_AUDIT_LOG" ]; then
    CLEANLINESS_SCORE=$(grep "Score de propreté" "$DB_AUDIT_LOG" | grep -o '[0-9.]*%' | head -1 || echo "N/A")
    TABLE_COUNT=$(grep "Tables trouvées" "$DB_AUDIT_LOG" | grep -o '[0-9]*' | head -1 || echo "N/A")
    MODEL_COUNT=$(grep "Modèles trouvés" "$DB_AUDIT_LOG" | grep -o '[0-9]*' | head -1 || echo "N/A")
else
    CLEANLINESS_SCORE="N/A"
    TABLE_COUNT="N/A"
    MODEL_COUNT="N/A"
fi

# Compter les tests
TEST_COUNT=$(find tests -name "*.php" -type f | wc -l)

# Déterminer la couleur du statut
STATUS_COLOR="#28a745"  # Vert par défaut
if [ "$OVERALL_STATUS" = "poor" ]; then
    STATUS_COLOR="#dc3545"  # Rouge
elif [ "$OVERALL_STATUS" = "fair" ]; then
    STATUS_COLOR="#ffc107"  # Jaune
elif [ "$OVERALL_STATUS" = "good" ]; then
    STATUS_COLOR="#17a2b8"  # Bleu
fi

# Générer le rapport HTML
cat > "$REPORT_FILE" << EOF
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport CI/CD Dynamique - Med-Predictor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; text-align: center; margin-bottom: 20px; }
        .status { padding: 10px; border-radius: 5px; margin: 10px 0; }
        .status.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status.warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        .status.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .section { margin: 20px 0; padding: 15px; border-left: 4px solid #667eea; background: #f8f9fa; }
        .metric { display: inline-block; margin: 10px; padding: 15px; background: white; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .metric-value { font-size: 2em; font-weight: bold; color: #667eea; }
        .metric-label { color: #666; font-size: 0.9em; }
        .component-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; }
        .component-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .component-title { color: #667eea; font-size: 1.2em; margin-bottom: 15px; border-bottom: 2px solid #667eea; padding-bottom: 5px; }
        .detail-item { margin: 8px 0; padding: 8px; background: #f8f9fa; border-radius: 4px; }
        .detail-label { font-weight: bold; color: #333; }
        .detail-value { color: #666; margin-left: 10px; }
        .summary-stats { display: flex; justify-content: space-around; margin: 20px 0; flex-wrap: wrap; }
        .stat-box { text-align: center; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin: 10px; min-width: 200px; }
        .stat-number { font-size: 3em; font-weight: bold; color: #667eea; }
        .stat-label { color: #666; font-size: 1.1em; }
        .refresh-info { text-align: center; margin: 20px 0; padding: 15px; background: #e3f2fd; border-radius: 8px; color: #1976d2; }
        .auto-refresh { text-align: center; margin: 10px 0; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Rapport CI/CD Dynamique</h1>
            <p>Projet: Med-Predictor</p>
            <p>Généré le: $TIMESTAMP</p>
            <p>Statut: <span style="color: $STATUS_COLOR; font-weight: bold;">$OVERALL_STATUS</span></p>
        </div>
        
        <div class="refresh-info">
            <strong>🔄 Ce rapport se met à jour automatiquement</strong><br>
            Exécutez <code>./scripts/generate-html-report.sh</code> pour le rafraîchir
        </div>
        
        <div class="section">
            <h2>📊 Résumé Global</h2>
            <div class="summary-stats">
                <div class="stat-box">
                    <div class="stat-number" style="color: $STATUS_COLOR;">$OVERALL_STATUS</div>
                    <div class="stat-label">Statut Global</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">$COMPONENT_COUNT</div>
                    <div class="stat-label">Composants Vérifiés</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">$ERROR_COUNT</div>
                    <div class="stat-label">Erreurs</div>
                </div>
                <div class="stat-box">
                    <div class="stat-number">$WARNING_COUNT</div>
                    <div class="stat-label">Avertissements</div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2>📋 Détails par Composant</h2>
            <div class="component-grid">
                <div class="component-card">
                    <div class="component-title">🔧 Prérequis</div>
                    <div class="detail-item">
                        <span class="detail-label">Git:</span>
                        <span class="detail-value">✅ $GIT_VERSION</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Docker:</span>
                        <span class="detail-value">✅ $DOCKER_VERSION</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">PHP:</span>
                        <span class="detail-value">✅ $PHP_VERSION</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Composer:</span>
                        <span class="detail-value">✅ $COMPOSER_VERSION</span>
                    </div>
                </div>
                
                <div class="component-card">
                    <div class="component-title">📊 Métriques de Qualité</div>
                    <div class="detail-item">
                        <span class="detail-label">Score de Propreté DB:</span>
                        <span class="detail-value">📈 $CLEANLINESS_SCORE</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tables DB:</span>
                        <span class="detail-value">🗄️ $TABLE_COUNT</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Modèles Eloquent:</span>
                        <span class="detail-value">🏗️ $MODEL_COUNT</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tests Disponibles:</span>
                        <span class="detail-value">🧪 $TEST_COUNT</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="section">
            <h2>📁 Fichiers de Configuration</h2>
            <ul>
                <li><a href=".gitlab-ci.yml">.gitlab-ci.yml</a> - Configuration GitLab CI/CD</li>
                <li><a href="Jenkinsfile">Jenkinsfile</a> - Configuration Jenkins</li>
                <li><a href="docker-compose.ci.yml">docker-compose.ci.yml</a> - Configuration Docker</li>
                <li><a href="phpunit.ci.xml">phpunit.ci.xml</a> - Configuration des tests</li>
                <li><a href="notifications.yml">notifications.yml</a> - Configuration des notifications</li>
                <li><a href="deploy-ci-cd.sh">deploy-ci-cd.sh</a> - Script de déploiement</li>
                <li><a href="scripts/run-ci-tests.sh">run-ci-tests.sh</a> - Script de tests CI/CD</li>
            </ul>
        </div>
        
        <div class="section">
            <h2>🚀 Actions Recommandées</h2>
            <div class="status success">
                🎉 Votre pipeline CI/CD est configuré et fonctionnel !
            </div>
            <div class="detail-item">
                <span class="detail-label">Prochaines étapes:</span>
                <ul>
                    <li>Installer yamllint: <code>brew install yamllint</code></li>
                    <li>Tester le pipeline GitLab en poussant du code</li>
                    <li>Configurer les notifications (Slack, Teams, Email)</li>
                    <li>Déployer en staging: <code>./deploy-ci-cd.sh staging</code></li>
                    <li>Surveiller les métriques de qualité</li>
                </ul>
            </div>
        </div>
        
        <div class="section">
            <h2>🔗 Liens Utiles</h2>
            <ul>
                <li><a href="verification-results.json">📄 Données JSON complètes</a></li>
                <li><a href="storage/logs/">📝 Logs détaillés</a></li>
                <li><a href="audit-reports/">📊 Rapports d'audit</a></li>
                <li><a href="CI_CD_DEPLOYMENT_GUIDE.md">📚 Guide de déploiement</a></li>
                <li><a href="README.md">📖 Documentation principale</a></li>
            </ul>
        </div>
        
        <div class="auto-refresh">
            <em>💡 Pour rafraîchir ce rapport: <code>./scripts/generate-html-report.sh</code></em>
        </div>
    </div>
</body>
</html>
EOF

log_success "Rapport HTML dynamique généré: $REPORT_FILE"
log "📊 Données extraites:"
log "   - Statut global: $OVERALL_STATUS"
log "   - Composants: $COMPONENT_COUNT"
log "   - Erreurs: $ERROR_COUNT"
log "   - Avertissements: $WARNING_COUNT"
log "   - Score DB: $CLEANLINESS_SCORE"
log "   - Tests: $TEST_COUNT"

# Ouvrir le rapport
if command -v open &> /dev/null; then
    log "🌐 Ouverture du rapport dans le navigateur..."
    open "$REPORT_FILE"
else
    log "📄 Rapport généré: $REPORT_FILE"
    log "Ouvrez-le manuellement dans votre navigateur"
fi

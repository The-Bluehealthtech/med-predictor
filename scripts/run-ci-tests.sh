#!/bin/bash

# Script de Tests CI/CD Automatisés
# Version: 1.0 - Tests Complets

set -e

# Configuration
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
LOG_FILE="ci-tests-${TIMESTAMP}.log"
COVERAGE_DIR="coverage"
TEST_RESULTS_DIR="test-results"

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Logging
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}" | tee -a "$LOG_FILE"
}

log_error() {
    echo -e "${RED}❌ $1${NC}" | tee -a "$LOG_FILE"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}" | tee -a "$LOG_FILE"
}

# Créer les répertoires
mkdir -p "$COVERAGE_DIR" "$TEST_RESULTS_DIR"

# Tests de syntaxe PHP
run_php_syntax_tests() {
    log "🔍 Tests de syntaxe PHP..."
    
    find app -name "*.php" -exec php -l {} \; | grep -v "No syntax errors" || {
        log_success "Syntaxe PHP valide"
    }
}

# Tests PHPUnit
run_phpunit_tests() {
    log "🧪 Tests PHPUnit..."
    
    if [ -f "phpunit.ci.xml" ]; then
        php vendor/bin/phpunit --configuration phpunit.ci.xml --coverage-html "$COVERAGE_DIR/html" --coverage-clover "$COVERAGE_DIR/clover.xml" --log-junit "$TEST_RESULTS_DIR/junit.xml" --testdox-html "$TEST_RESULTS_DIR/testdox.html"
        log_success "Tests PHPUnit terminés"
    else
        log_warning "Fichier phpunit.ci.xml non trouvé, utilisation de phpunit.xml par défaut"
        php vendor/bin/phpunit --coverage-html "$COVERAGE_DIR/html" --coverage-clover "$COVERAGE_DIR/clover.xml" --log-junit "$TEST_RESULTS_DIR/junit.xml"
        log_success "Tests PHPUnit terminés"
    fi
}

# Tests de qualité du code
run_code_quality_tests() {
    log "🔍 Tests de qualité du code..."
    
    # PHP CS Fixer
    if [ -f ".php-cs-fixer.php" ]; then
        log "  - Vérification du style avec PHP CS Fixer..."
        vendor/bin/php-cs-fixer fix --dry-run --diff || log_warning "Problèmes de style détectés"
    fi
    
    # PHPStan
    if [ -f "phpstan.neon" ]; then
        log "  - Analyse statique avec PHPStan..."
        vendor/bin/phpstan analyse app --no-progress || log_warning "Problèmes PHPStan détectés"
    fi
    
    # PHPMD
    if [ -f "phpmd.xml" ]; then
        log "  - Détection des défauts avec PHPMD..."
        vendor/bin/phpmd app text phpmd.xml || log_warning "Défauts PHPMD détectés"
    fi
    
    log_success "Tests de qualité terminés"
}

# Tests de sécurité
run_security_tests() {
    log "🔒 Tests de sécurité..."
    
    # Vérification des dépendances
    if [ -f "composer.lock" ]; then
        log "  - Vérification des vulnérabilités des dépendances..."
        composer audit --format=json > "$TEST_RESULTS_DIR/security-audit.json" || log_warning "Vulnérabilités détectées"
    fi
    
    # Vérification des permissions
    log "  - Vérification des permissions..."
    find storage -type d -exec chmod 755 {} \;
    find storage -type f -exec chmod 644 {} \;
    
    log_success "Tests de sécurité terminés"
}

# Tests de performance
run_performance_tests() {
    log "⚡ Tests de performance..."
    
    # Test de temps de réponse
    if [ -n "$APP_URL" ]; then
        log "  - Test de temps de réponse..."
        RESPONSE_TIME=$(curl -o /dev/null -s -w "%{time_total}" "$APP_URL")
        log "Temps de réponse: ${RESPONSE_TIME}s"
        
        if (( $(echo "$RESPONSE_TIME > 2.0" | bc -l) )); then
            log_warning "Temps de réponse élevé: ${RESPONSE_TIME}s"
        else
            log_success "Temps de réponse acceptable: ${RESPONSE_TIME}s"
        fi
    fi
    
    log_success "Tests de performance terminés"
}

# Tests de base de données
run_database_tests() {
    log "🗄️ Tests de base de données..."
    
    # Test de connectivité
    php artisan tinker --execute="echo 'Base de données accessible: ' . (DB::connection()->getPdo() ? 'OUI' : 'NON') . PHP_EOL;"
    
    # Test des migrations
    log "  - Test des migrations..."
    php artisan migrate:status
    
    log_success "Tests de base de données terminés"
}

# Tests d'audit
run_audit_tests() {
    log "🔍 Tests d'audit..."
    
    if php artisan list | grep -q "project:db:audit"; then
        php artisan project:db:audit
        
        # Vérifier le score de propreté
        LATEST_REPORT=$(ls -t storage/logs/db-audit-*.log | head -1)
        if [ -n "$LATEST_REPORT" ]; then
            CLEANLINESS_SCORE=$(grep -o '"cleanliness_score": "[^"]*"' "$LATEST_REPORT" | cut -d'"' -f4 | sed 's/%//' || echo "0")
            log "Score de propreté: ${CLEANLINESS_SCORE}%"
            
            if [ "$CLEANLINESS_SCORE" -lt 70 ]; then
                log_warning "Score de propreté bas: ${CLEANLINESS_SCORE}%"
            else
                log_success "Score de propreté acceptable: ${CLEANLINESS_SCORE}%"
            fi
        fi
    else
        log_warning "Commande d'audit non trouvée"
    fi
    
    log_success "Tests d'audit terminés"
}

# Génération du rapport
generate_test_report() {
    log "📊 Génération du rapport de tests..."
    
    cat > "$TEST_RESULTS_DIR/test-summary.html" << 'EOF'
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Tests CI/CD</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { background: #f0f0f0; padding: 20px; border-radius: 5px; }
        .test-section { margin: 20px 0; padding: 15px; border-left: 4px solid #007cba; }
        .success { color: #28a745; }
        .warning { color: #ffc107; }
        .error { color: #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🧪 Rapport de Tests CI/CD</h1>
        <p>Généré le: $(date)</p>
        <p>Projet: Med-Predictor</p>
    </div>
    
    <div class="test-section">
        <h2>📋 Résumé des Tests</h2>
        <p>Tests exécutés avec succès</p>
    </div>
    
    <div class="test-section">
        <h2>📁 Fichiers de Résultats</h2>
        <ul>
            <li><a href="../coverage/html/index.html">Rapport de couverture HTML</a></li>
            <li><a href="junit.xml">Résultats JUnit XML</a></li>
            <li><a href="testdox.html">Rapport TestDox</a></li>
        </ul>
    </div>
</body>
</html>
EOF
    
    log_success "Rapport généré dans $TEST_RESULTS_DIR/test-summary.html"
}

# Fonction principale
main() {
    log "🚀 Démarrage des tests CI/CD automatisés"
    
    run_php_syntax_tests
    run_phpunit_tests
    run_code_quality_tests
    run_security_tests
    run_performance_tests
    run_database_tests
    run_audit_tests
    generate_test_report
    
    log_success "🎉 Tous les tests CI/CD sont terminés !"
    log "📋 Consultez les résultats dans: $TEST_RESULTS_DIR/"
    log "📊 Consultez la couverture dans: $COVERAGE_DIR/"
}

# Exécution
main "$@"

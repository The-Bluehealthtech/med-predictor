#!/bin/bash

# Script de Vérification CI/CD
# Version: 1.0 - Validation Complète

set -e

# Configuration
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
LOG_FILE="ci-cd-verification-${TIMESTAMP}.log"
VERIFICATION_RESULTS="verification-results.json"

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

# Initialisation du rapport JSON
init_report() {
    cat > "$VERIFICATION_RESULTS" << EOF
{
  "verification_timestamp": "$(date -u +%Y-%m-%dT%H:%M:%SZ)",
  "project": "med-predictor",
  "results": {
    "prerequisites": {},
    "gitlab_ci": {},
    "jenkins": {},
    "github_actions": {},
    "docker": {},
    "artisan_commands": {},
    "tests": {},
    "overall_status": "unknown"
  }
}
EOF
}

# Vérification des prérequis
check_prerequisites() {
    log "🔍 Vérification des prérequis..."
    
    local results=()
    
    # Git
    if command -v git &> /dev/null; then
        GIT_VERSION=$(git --version | cut -d' ' -f3)
        log_success "Git installé: $GIT_VERSION"
        results+=("{\"git\": {\"status\": \"success\", \"version\": \"$GIT_VERSION\"}}")
    else
        log_error "Git non installé"
        results+=("{\"git\": {\"status\": \"error\", \"message\": \"Non installé\"}}")
    fi
    
    # Docker
    if command -v docker &> /dev/null; then
        DOCKER_VERSION=$(docker --version | cut -d' ' -f3 | sed 's/,//')
        log_success "Docker installé: $DOCKER_VERSION"
        results+=("{\"docker\": {\"status\": \"success\", \"version\": \"$DOCKER_VERSION\"}}")
    else
        log_error "Docker non installé"
        results+=("{\"docker\": {\"status\": \"error\", \"message\": \"Non installé\"}}")
    fi
    
    # Docker Compose
    if command -v docker-compose &> /dev/null; then
        COMPOSE_VERSION=$(docker-compose --version | cut -d' ' -f3 | sed 's/,//')
        log_success "Docker Compose installé: $COMPOSE_VERSION"
        results+=("{\"docker_compose\": {\"status\": \"success\", \"version\": \"$COMPOSE_VERSION\"}}")
    else
        log_error "Docker Compose non installé"
        results+=("{\"docker_compose\": {\"status\": \"error\", \"message\": \"Non installé\"}}")
    fi
    
    # PHP
    if command -v php &> /dev/null; then
        PHP_VERSION=$(php --version | head -1 | cut -d' ' -f2)
        log_success "PHP installé: $PHP_VERSION"
        results+=("{\"php\": {\"status\": \"success\", \"version\": \"$PHP_VERSION\"}}")
    else
        log_error "PHP non installé"
        results+=("{\"php\": {\"status\": \"error\", \"message\": \"Non installé\"}}")
    fi
    
    # Composer
    if command -v composer &> /dev/null; then
        COMPOSER_VERSION=$(composer --version | head -1 | cut -d' ' -f3)
        log_success "Composer installé: $COMPOSER_VERSION"
        results+=("{\"composer\": {\"status\": \"success\", \"version\": \"$COMPOSER_VERSION\"}}")
    else
        log_error "Composer non installé"
        results+=("{\"composer\": {\"status\": \"error\", \"message\": \"Non installé\"}}")
    fi
    
    # Mettre à jour le rapport
    local prereq_json=$(echo "${results[@]}" | jq -s 'add')
    jq ".results.prerequisites = $prereq_json" "$VERIFICATION_RESULTS" > temp.json && mv temp.json "$VERIFICATION_RESULTS"
}

# Vérification GitLab CI/CD
check_gitlab_ci() {
    log "🔍 Vérification GitLab CI/CD..."
    
    local results=()
    
    # Fichier .gitlab-ci.yml
    if [ -f ".gitlab-ci.yml" ]; then
        log_success "Fichier .gitlab-ci.yml trouvé"
        results+=("{\"config_file\": {\"status\": \"success\", \"message\": \"Fichier présent\"}}")
        
        # Vérifier la syntaxe
        if command -v yamllint &> /dev/null; then
            if yamllint .gitlab-ci.yml > /dev/null 2>&1; then
                log_success "Syntaxe YAML valide"
                results+=("{\"syntax\": {\"status\": \"success\", \"message\": \"Syntaxe valide\"}}")
            else
                log_warning "Problèmes de syntaxe YAML détectés"
                results+=("{\"syntax\": {\"status\": \"warning\", \"message\": \"Problèmes détectés\"}}")
            fi
        else
            log_warning "yamllint non installé, vérification de syntaxe ignorée"
            results+=("{\"syntax\": {\"status\": \"warning\", \"message\": \"yamllint non installé\"}}")
        fi
    else
        log_error "Fichier .gitlab-ci.yml manquant"
        results+=("{\"config_file\": {\"status\": \"error\", \"message\": \"Fichier manquant\"}}")
    fi
    
    # Mettre à jour le rapport
    local gitlab_json=$(echo "${results[@]}" | jq -s 'add')
    jq ".results.gitlab_ci = $gitlab_json" "$VERIFICATION_RESULTS" > temp.json && mv temp.json "$VERIFICATION_RESULTS"
}

# Vérification Jenkins
check_jenkins() {
    log "🔍 Vérification Jenkins..."
    
    local results=()
    
    # Fichier Jenkinsfile
    if [ -f "Jenkinsfile" ]; then
        log_success "Fichier Jenkinsfile trouvé"
        results+=("{\"jenkinsfile\": {\"status\": \"success\", \"message\": \"Fichier présent\"}}")
        
        # Vérifier la syntaxe Groovy (basique)
        if grep -q "pipeline" Jenkinsfile; then
            log_success "Structure de pipeline détectée"
            results+=("{\"structure\": {\"status\": \"success\", \"message\": \"Pipeline valide\"}}")
        else
            log_warning "Structure de pipeline non détectée"
            results+=("{\"structure\": {\"status\": \"warning\", \"message\": \"Structure non détectée\"}}")
        fi
    else
        log_warning "Fichier Jenkinsfile non trouvé"
        results+=("{\"jenkinsfile\": {\"status\": \"warning\", \"message\": \"Fichier manquant\"}}")
    fi
    
    # Mettre à jour le rapport
    local jenkins_json=$(echo "${results[@]}" | jq -s 'add')
    jq ".results.jenkins = $jenkins_json" "$VERIFICATION_RESULTS" > temp.json && mv temp.json "$VERIFICATION_RESULTS"
}

# Vérification GitHub Actions
check_github_actions() {
    log "🔍 Vérification GitHub Actions..."
    
    local results=()
    
    # Répertoire .github/workflows
    if [ -d ".github/workflows" ]; then
        log_success "Répertoire .github/workflows trouvé"
        results+=("{\"directory\": {\"status\": \"success\", \"message\": \"Répertoire présent\"}}")
        
        # Compter les workflows
        local workflow_count=$(find .github/workflows -name "*.yml" | wc -l)
        log_success "Nombre de workflows: $workflow_count"
        results+=("{\"workflow_count\": {\"status\": \"success\", \"count\": $workflow_count}}")
        
        # Lister les workflows
        local workflows=$(find .github/workflows -name "*.yml" -exec basename {} \; | jq -R -s 'split("\n")[:-1]')
        results+=("{\"workflows\": {\"status\": \"success\", \"files\": $workflows}}")
    else
        log_warning "Répertoire .github/workflows non trouvé"
        results+=("{\"directory\": {\"status\": \"warning\", \"message\": \"Répertoire manquant\"}}")
    fi
    
    # Mettre à jour le rapport
    local github_json=$(echo "${results[@]}" | jq -s 'add')
    jq ".results.github_actions = $github_json" "$VERIFICATION_RESULTS" > temp.json && mv temp.json "$VERIFICATION_RESULTS"
}

# Vérification Docker
check_docker() {
    log "🔍 Vérification Docker..."
    
    local results=()
    
    # Dockerfile.ci
    if [ -f "Dockerfile.ci" ]; then
        log_success "Dockerfile.ci trouvé"
        results+=("{\"dockerfile_ci\": {\"status\": \"success\", \"message\": \"Fichier présent\"}}")
        
        # Vérifier la syntaxe
        if docker build -f Dockerfile.ci --dry-run . > /dev/null 2>&1; then
            log_success "Dockerfile.ci syntaxe valide"
            results+=("{\"syntax\": {\"status\": \"success\", \"message\": \"Syntaxe valide\"}}")
        else
            log_warning "Problèmes de syntaxe dans Dockerfile.ci"
            results+=("{\"syntax\": {\"status\": \"warning\", \"message\": \"Problèmes détectés\"}}")
        fi
    else
        log_warning "Dockerfile.ci non trouvé"
        results+=("{\"dockerfile_ci\": {\"status\": \"warning\", \"message\": \"Fichier manquant\"}}")
    fi
    
    # docker-compose.ci.yml
    if [ -f "docker-compose.ci.yml" ]; then
        log_success "docker-compose.ci.yml trouvé"
        results+=("{\"compose_file\": {\"status\": \"success\", \"message\": \"Fichier présent\"}}")
        
        # Vérifier la configuration
        if docker-compose -f docker-compose.ci.yml config > /dev/null 2>&1; then
            log_success "Configuration docker-compose valide"
            results+=("{\"config\": {\"status\": \"success\", \"message\": \"Configuration valide\"}}")
        else
            log_warning "Problèmes de configuration docker-compose"
            results+=("{\"config\": {\"status\": \"warning\", \"message\": \"Problèmes détectés\"}}")
        fi
    else
        log_warning "docker-compose.ci.yml non trouvé"
        results+=("{\"compose_file\": {\"status\": \"warning\", \"message\": \"Fichier manquant\"}}")
    fi
    
    # Mettre à jour le rapport
    local docker_json=$(echo "${results[@]}" | jq -s 'add')
    jq ".results.docker = $docker_json" "$VERIFICATION_RESULTS" > temp.json && mv temp.json "$VERIFICATION_RESULTS"
}

# Vérification des commandes Artisan
check_artisan_commands() {
    log "🔍 Vérification des commandes Artisan..."
    
    local results=()
    
    # Vérifier si Laravel est installé
    if [ -f "artisan" ]; then
        log_success "Laravel Artisan trouvé"
        results+=("{\"artisan\": {\"status\": \"success\", \"message\": \"Laravel installé\"}}")
        
        # Vérifier la commande d'audit
        if php artisan list | grep -q "project:db:audit"; then
            log_success "Commande project:db:audit disponible"
            results+=("{\"audit_command\": {\"status\": \"success\", \"message\": \"Commande disponible\"}}")
        else
            log_error "Commande project:db:audit non trouvée"
            results+=("{\"audit_command\": {\"status\": \"error\", \"message\": \"Commande manquante\"}}")
        fi
        
        # Vérifier les autres commandes utiles
        local commands=("migrate:status" "config:clear" "cache:clear" "route:list")
        local available_commands=()
        
        for cmd in "${commands[@]}"; do
            if php artisan list | grep -q "$cmd"; then
                available_commands+=("$cmd")
            fi
        done
        
        local cmd_count=${#available_commands[@]}
        log_success "Commandes disponibles: $cmd_count/${#commands[@]}"
        results+=("{\"available_commands\": {\"status\": \"success\", \"count\": $cmd_count, \"commands\": $(echo "${available_commands[@]}" | jq -R -s 'split(" ")')}}")
    else
        log_error "Laravel Artisan non trouvé"
        results+=("{\"artisan\": {\"status\": \"error\", \"message\": \"Laravel non installé\"}}")
    fi
    
    # Mettre à jour le rapport
    local artisan_json=$(echo "${results[@]}" | jq -s 'add')
    jq ".results.artisan_commands = $artisan_json" "$VERIFICATION_RESULTS" > temp.json && mv temp.json "$VERIFICATION_RESULTS"
}

# Vérification des tests
check_tests() {
    log "🔍 Vérification des tests..."
    
    local results=()
    
    # PHPUnit
    if [ -f "phpunit.xml" ] || [ -f "phpunit.ci.xml" ]; then
        log_success "Configuration PHPUnit trouvée"
        results+=("{\"phpunit_config\": {\"status\": \"success\", \"message\": \"Configuration présente\"}}")
        
        # Vérifier si PHPUnit est installé
        if [ -f "vendor/bin/phpunit" ]; then
            log_success "PHPUnit installé"
            results+=("{\"phpunit_installed\": {\"status\": \"success\", \"message\": \"PHPUnit disponible\"}}")
            
            # Compter les tests
            local test_count=$(find tests -name "*Test.php" 2>/dev/null | wc -l)
            log_success "Nombre de tests: $test_count"
            results+=("{\"test_count\": {\"status\": \"success\", \"count\": $test_count}}")
        else
            log_warning "PHPUnit non installé"
            results+=("{\"phpunit_installed\": {\"status\": \"warning\", \"message\": \"PHPUnit non installé\"}}")
        fi
    else
        log_warning "Configuration PHPUnit non trouvée"
        results+=("{\"phpunit_config\": {\"status\": \"warning\", \"message\": \"Configuration manquante\"}}")
    fi
    
    # Scripts de test
    if [ -f "scripts/run-ci-tests.sh" ]; then
        log_success "Script de tests CI/CD trouvé"
        results+=("{\"test_script\": {\"status\": \"success\", \"message\": \"Script présent\"}}")
        
        # Vérifier les permissions
        if [ -x "scripts/run-ci-tests.sh" ]; then
            log_success "Script de tests exécutable"
            results+=("{\"script_permissions\": {\"status\": \"success\", \"message\": \"Permissions correctes\"}}")
        else
            log_warning "Script de tests non exécutable"
            results+=("{\"script_permissions\": {\"status\": \"warning\", \"message\": \"Permissions incorrectes\"}}")
        fi
    else
        log_warning "Script de tests CI/CD non trouvé"
        results+=("{\"test_script\": {\"status\": \"warning\", \"message\": \"Script manquant\"}}")
    fi
    
    # Mettre à jour le rapport
    local tests_json=$(echo "${results[@]}" | jq -s 'add')
    jq ".results.tests = $tests_json" "$VERIFICATION_RESULTS" > temp.json && mv temp.json "$VERIFICATION_RESULTS"
}

# Calcul du statut global
calculate_overall_status() {
    log "📊 Calcul du statut global..."
    
    # Compter les erreurs, warnings et succès
    local error_count=$(jq -r '.results | to_entries | map(select(.value | to_entries | any(.value.status == "error"))) | length' "$VERIFICATION_RESULTS" 2>/dev/null || echo "0")
    local warning_count=$(jq -r '.results | to_entries | map(select(.value | to_entries | any(.value.status == "warning"))) | length' "$VERIFICATION_RESULTS" 2>/dev/null || echo "0")
    local success_count=$(jq -r '.results | to_entries | map(select(.value | to_entries | all(.value.status == "success"))) | length' "$VERIFICATION_RESULTS" 2>/dev/null || echo "0")
    
    # Convertir en entiers avec valeur par défaut
    error_count=${error_count:-0}
    warning_count=${warning_count:-0}
    success_count=${success_count:-0}
    
    local overall_status="unknown"
    
    if [ "$error_count" -eq 0 ] && [ "$warning_count" -eq 0 ]; then
        overall_status="excellent"
        log_success "Statut global: EXCELLENT - Tous les composants sont configurés correctement"
    elif [ "$error_count" -eq 0 ]; then
        overall_status="good"
        log_success "Statut global: BON - Quelques avertissements mais pas d'erreurs critiques"
    elif [ "$error_count" -le 2 ]; then
        overall_status="fair"
        log_warning "Statut global: MOYEN - Quelques erreurs à corriger"
    else
        overall_status="poor"
        log_error "Statut global: MAUVAIS - Nombreuses erreurs à corriger"
    fi
    
    # Mettre à jour le rapport
    jq ".results.overall_status = \"$overall_status\"" "$VERIFICATION_RESULTS" > temp.json && mv temp.json "$VERIFICATION_RESULTS"
    
    # Afficher le résumé
    echo ""
    log "📋 Résumé de la vérification:"
    log "  - Succès: $success_count"
    log "  - Avertissements: $warning_count"
    log "  - Erreurs: $error_count"
    log "  - Statut global: $overall_status"
}

# Génération du rapport final
generate_final_report() {
    log "📄 Génération du rapport final..."
    
    # Créer un rapport HTML
    cat > "verification-report.html" << 'EOF'
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Vérification CI/CD - Med-Predictor</title>
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Rapport de Vérification CI/CD</h1>
            <p>Projet: Med-Predictor</p>
            <p>Généré le: $(date)</p>
        </div>
        
        <div class="section">
            <h2>📊 Résumé Global</h2>
            <div class="metric">
                <div class="metric-value" id="overall-status">Chargement...</div>
                <div class="metric-label">Statut Global</div>
            </div>
        </div>
        
        <div class="section">
            <h2>📋 Détails par Composant</h2>
            <div id="component-details">Chargement...</div>
        </div>
        
        <div class="section">
            <h2>📁 Fichiers de Configuration</h2>
            <ul>
                <li><a href=".gitlab-ci.yml">.gitlab-ci.yml</a> - Configuration GitLab CI/CD</li>
                <li><a href="Jenkinsfile">Jenkinsfile</a> - Configuration Jenkins</li>
                <li><a href="docker-compose.ci.yml">docker-compose.ci.yml</a> - Configuration Docker</li>
                <li><a href="phpunit.ci.xml">phpunit.ci.xml</a> - Configuration des tests</li>
                <li><a href="notifications.yml">notifications.yml</a> - Configuration des notifications</li>
            </ul>
        </div>
        
        <div class="section">
            <h2>🚀 Actions Recommandées</h2>
            <div id="recommendations">Chargement...</div>
        </div>
    </div>
    
    <script>
        // Charger les données JSON
        fetch('verification-results.json')
            .then(response => response.json())
            .then(data => {
                // Afficher le statut global
                document.getElementById('overall-status').textContent = data.results.overall_status.toUpperCase();
                
                // Afficher les détails des composants
                let componentHtml = '';
                for (const [component, details] of Object.entries(data.results)) {
                    if (component !== 'overall_status') {
                        componentHtml += `<h3>${component.replace(/_/g, ' ').toUpperCase()}</h3>`;
                        for (const [key, value] of Object.entries(details)) {
                            const statusClass = value.status;
                            componentHtml += `<div class="status ${statusClass}">${key}: ${value.message || value.status}</div>`;
                        }
                    }
                }
                document.getElementById('component-details').innerHTML = componentHtml;
                
                // Générer les recommandations
                let recommendations = '';
                if (data.results.overall_status === 'excellent') {
                    recommendations = '<div class="status success">🎉 Tous les composants sont configurés correctement. Votre pipeline CI/CD est prêt !</div>';
                } else if (data.results.overall_status === 'good') {
                    recommendations = '<div class="status warning">⚠️ Quelques avertissements à corriger pour optimiser votre pipeline.</div>';
                } else {
                    recommendations = '<div class="status error">❌ Des erreurs critiques doivent être corrigées avant d\'utiliser le pipeline.</div>';
                }
                document.getElementById('recommendations').innerHTML = recommendations;
            })
            .catch(error => {
                console.error('Erreur lors du chargement des données:', error);
            });
    </script>
</body>
</html>
EOF
    
    log_success "Rapport HTML généré: verification-report.html"
}

# Fonction principale
main() {
    log "🚀 Démarrage de la vérification CI/CD complète"
    
    # Initialiser le rapport
    init_report
    
    # Exécuter toutes les vérifications
    check_prerequisites
    check_gitlab_ci
    check_jenkins
    check_github_actions
    check_docker
    check_artisan_commands
    check_tests
    
    # Calculer le statut global
    calculate_overall_status
    
    # Générer le rapport final
    generate_final_report
    
    log_success "🎉 Vérification CI/CD terminée !"
    log "📋 Consultez les résultats dans: $VERIFICATION_RESULTS"
    log "📄 Rapport HTML: verification-report.html"
    log "📝 Logs détaillés: $LOG_FILE"
    
    # Afficher le statut final
    local final_status=$(jq -r '.results.overall_status' "$VERIFICATION_RESULTS")
    echo ""
    case $final_status in
        "excellent")
            log_success "🎉 Votre pipeline CI/CD est parfaitement configuré !"
            ;;
        "good")
            log_success "✅ Votre pipeline CI/CD est bien configuré avec quelques améliorations possibles."
            ;;
        "fair")
            log_warning "⚠️ Votre pipeline CI/CD nécessite des corrections avant utilisation."
            ;;
        "poor")
            log_error "❌ Votre pipeline CI/CD nécessite une attention immédiate."
            ;;
    esac
}

# Exécution
main "$@"

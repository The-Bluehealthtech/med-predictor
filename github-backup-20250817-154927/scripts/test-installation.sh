#!/bin/bash

# =============================================================================
# SCRIPT DE TEST D'INSTALLATION - MICROSERVICE FIT
# =============================================================================

set -e

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
print_message() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Variables de test
TESTS_PASSED=0
TESTS_FAILED=0
TOTAL_TESTS=0

# Fonction pour incrémenter les compteurs de test
test_passed() {
    TESTS_PASSED=$((TESTS_PASSED + 1))
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
}

test_failed() {
    TESTS_FAILED=$((TESTS_FAILED + 1))
    TOTAL_TESTS=$((TOTAL_TESTS + 1))
}

# Test 1: Vérification des prérequis
test_prerequisites() {
    print_message "Test 1: Vérification des prérequis..."
    
    # Vérifier Node.js
    if command -v node &> /dev/null; then
        NODE_VERSION=$(node --version)
        print_success "Node.js détecté: $NODE_VERSION"
        test_passed
    else
        print_error "Node.js non détecté"
        test_failed
    fi
    
    # Vérifier npm
    if command -v npm &> /dev/null; then
        NPM_VERSION=$(npm --version)
        print_success "npm détecté: $NPM_VERSION"
        test_passed
    else
        print_error "npm non détecté"
        test_failed
    fi
    
    # Vérifier Git
    if command -v git &> /dev/null; then
        GIT_VERSION=$(git --version)
        print_success "Git détecté: $GIT_VERSION"
        test_passed
    else
        print_error "Git non détecté"
        test_failed
    fi
}

# Test 2: Vérification des fichiers de configuration
test_configuration_files() {
    print_message "Test 2: Vérification des fichiers de configuration..."
    
    # Vérifier package.json
    if [ -f "package.json" ]; then
        print_success "package.json trouvé"
        test_passed
    else
        print_error "package.json manquant"
        test_failed
    fi
    
    # Vérifier .env
    if [ -f ".env" ]; then
        print_success ".env trouvé"
        test_passed
    else
        print_error ".env manquant"
        test_failed
    fi
    
    # Vérifier env.example
    if [ -f "env.example" ]; then
        print_success "env.example trouvé"
        test_passed
    else
        print_error "env.example manquant"
        test_failed
    fi
}

# Test 3: Vérification des dépendances
test_dependencies() {
    print_message "Test 3: Vérification des dépendances..."
    
    # Vérifier node_modules
    if [ -d "node_modules" ]; then
        print_success "node_modules trouvé"
        test_passed
    else
        print_warning "node_modules manquant - exécuter 'npm install'"
        test_failed
    fi
    
    # Vérifier les dépendances critiques
    if [ -d "node_modules/express" ]; then
        print_success "Express.js installé"
        test_passed
    else
        print_error "Express.js manquant"
        test_failed
    fi
    
    if [ -d "node_modules/mongoose" ]; then
        print_success "Mongoose installé"
        test_passed
    else
        print_error "Mongoose manquant"
        test_failed
    fi
    
    if [ -d "node_modules/sequelize" ]; then
        print_success "Sequelize installé"
        test_passed
    else
        print_error "Sequelize manquant"
        test_failed
    fi
}

# Test 4: Vérification des bases de données
test_databases() {
    print_message "Test 4: Vérification des bases de données..."
    
    # Vérifier MongoDB
    if command -v mongosh &> /dev/null; then
        if mongosh --quiet --eval "db.runCommand('ping')" > /dev/null 2>&1; then
            print_success "MongoDB connecté"
            test_passed
        else
            print_warning "MongoDB non accessible"
            test_failed
        fi
    else
        print_warning "MongoDB non installé"
        test_failed
    fi
    
    # Vérifier PostgreSQL
    if command -v psql &> /dev/null; then
        if PGPASSWORD=fit_password psql -h localhost -U fit_user -d fit_database -c "SELECT 1;" > /dev/null 2>&1; then
            print_success "PostgreSQL connecté"
            test_passed
        else
            print_warning "PostgreSQL non accessible"
            test_failed
        fi
    else
        print_warning "PostgreSQL non installé"
        test_failed
    fi
    
    # Vérifier Redis
    if command -v redis-cli &> /dev/null; then
        if redis-cli ping > /dev/null 2>&1; then
            print_success "Redis connecté"
            test_passed
        else
            print_warning "Redis non accessible"
            test_failed
        fi
    else
        print_warning "Redis non installé"
        test_failed
    fi
}

# Test 5: Vérification des scripts
test_scripts() {
    print_message "Test 5: Vérification des scripts..."
    
    # Vérifier le script d'installation
    if [ -f "scripts/setup.sh" ]; then
        if [ -x "scripts/setup.sh" ]; then
            print_success "Script setup.sh exécutable"
            test_passed
        else
            print_warning "Script setup.sh non exécutable"
            test_failed
        fi
    else
        print_error "Script setup.sh manquant"
        test_failed
    fi
    
    # Vérifier le script de déploiement
    if [ -f "scripts/deploy-production.sh" ]; then
        if [ -x "scripts/deploy-production.sh" ]; then
            print_success "Script deploy-production.sh exécutable"
            test_passed
        else
            print_warning "Script deploy-production.sh non exécutable"
            test_failed
        fi
    else
        print_error "Script deploy-production.sh manquant"
        test_failed
    fi
    
    # Vérifier le script de monitoring
    if [ -f "scripts/monitoring.sh" ]; then
        if [ -x "scripts/monitoring.sh" ]; then
            print_success "Script monitoring.sh exécutable"
            test_passed
        else
            print_warning "Script monitoring.sh non exécutable"
            test_failed
        fi
    else
        print_error "Script monitoring.sh manquant"
        test_failed
    fi
}

# Test 6: Vérification de la structure du projet
test_project_structure() {
    print_message "Test 6: Vérification de la structure du projet..."
    
    # Vérifier les dossiers essentiels
    ESSENTIAL_DIRS=("src" "src/config" "src/models" "src/services" "src/controllers" "src/routes" "src/middleware" "tests" "logs" "backups")
    
    for dir in "${ESSENTIAL_DIRS[@]}"; do
        if [ -d "$dir" ]; then
            print_success "Dossier $dir trouvé"
            test_passed
        else
            print_error "Dossier $dir manquant"
            test_failed
        fi
    done
}

# Test 7: Vérification des fichiers source
test_source_files() {
    print_message "Test 7: Vérification des fichiers source..."
    
    # Vérifier les fichiers essentiels
    ESSENTIAL_FILES=(
        "src/app.js"
        "src/config/database.js"
        "src/config/oauth2.js"
        "src/models/Player.js"
        "src/models/OAuthToken.js"
        "src/models/DeviceData.js"
        "src/services/OAuth2Service.js"
        "src/services/DataSyncService.js"
        "src/controllers/OAuth2Controller.js"
        "src/controllers/DeviceController.js"
        "src/routes/oauth2.js"
        "src/routes/devices.js"
        "src/middleware/auth.js"
        "src/middleware/errorHandler.js"
    )
    
    for file in "${ESSENTIAL_FILES[@]}"; do
        if [ -f "$file" ]; then
            print_success "Fichier $file trouvé"
            test_passed
        else
            print_error "Fichier $file manquant"
            test_failed
        fi
    done
}

# Test 8: Vérification de la syntaxe JavaScript
test_javascript_syntax() {
    print_message "Test 8: Vérification de la syntaxe JavaScript..."
    
    # Vérifier la syntaxe du fichier principal
    if node -c src/app.js 2>/dev/null; then
        print_success "Syntaxe JavaScript valide pour app.js"
        test_passed
    else
        print_error "Erreur de syntaxe dans app.js"
        test_failed
    fi
    
    # Vérifier quelques fichiers critiques
    CRITICAL_FILES=("src/config/database.js" "src/services/OAuth2Service.js" "src/middleware/auth.js")
    
    for file in "${CRITICAL_FILES[@]}"; do
        if [ -f "$file" ]; then
            if node -c "$file" 2>/dev/null; then
                print_success "Syntaxe JavaScript valide pour $file"
                test_passed
            else
                print_error "Erreur de syntaxe dans $file"
                test_failed
            fi
        fi
    done
}

# Test 9: Vérification des variables d'environnement
test_environment_variables() {
    print_message "Test 9: Vérification des variables d'environnement..."
    
    # Charger les variables d'environnement
    if [ -f ".env" ]; then
        source .env 2>/dev/null || true
        
        # Vérifier les variables critiques
        CRITICAL_VARS=("NODE_ENV" "PORT" "JWT_SECRET" "ENCRYPTION_KEY")
        
        for var in "${CRITICAL_VARS[@]}"; do
            if [ -n "${!var}" ]; then
                print_success "Variable $var configurée"
                test_passed
            else
                print_warning "Variable $var non configurée"
                test_failed
            fi
        done
        
        # Vérifier les variables OAuth2
        OAUTH_VARS=("CATAPULT_CLIENT_ID" "APPLE_CLIENT_ID" "GARMIN_CLIENT_ID")
        
        for var in "${OAUTH_VARS[@]}"; do
            if [ -n "${!var}" ] && [ "${!var}" != "your-*-client-id" ]; then
                print_success "Variable $var configurée"
                test_passed
            else
                print_warning "Variable $var non configurée ou valeur par défaut"
                test_failed
            fi
        done
    else
        print_error "Fichier .env manquant"
        test_failed
    fi
}

# Test 10: Test de démarrage du service
test_service_startup() {
    print_message "Test 10: Test de démarrage du service..."
    
    # Vérifier si le service peut démarrer (test rapide)
    if timeout 10s node -e "
        try {
            require('dotenv').config();
            console.log('Variables d\'environnement chargées');
            process.exit(0);
        } catch (error) {
            console.error('Erreur:', error.message);
            process.exit(1);
        }
    " > /dev/null 2>&1; then
        print_success "Service peut démarrer"
        test_passed
    else
        print_error "Erreur au démarrage du service"
        test_failed
    fi
}

# Test 11: Vérification des tests
test_test_files() {
    print_message "Test 11: Vérification des tests..."
    
    # Vérifier les fichiers de test
    if [ -f "tests/unit/OAuth2Service.test.js" ]; then
        print_success "Tests unitaires trouvés"
        test_passed
    else
        print_warning "Tests unitaires manquants"
        test_failed
    fi
    
    if [ -f "tests/integration/api.test.js" ]; then
        print_success "Tests d'intégration trouvés"
        test_passed
    else
        print_warning "Tests d'intégration manquants"
        test_failed
    fi
    
    # Vérifier si les tests peuvent s'exécuter
    if command -v npm &> /dev/null; then
        if npm run test:unit > /dev/null 2>&1; then
            print_success "Tests unitaires exécutables"
            test_passed
        else
            print_warning "Tests unitaires non exécutables"
            test_failed
        fi
    fi
}

# Test 12: Vérification de la sécurité
test_security() {
    print_message "Test 12: Vérification de la sécurité..."
    
    # Vérifier les permissions des fichiers sensibles
    if [ -f ".env" ]; then
        PERMS=$(stat -c %a .env 2>/dev/null || stat -f %Lp .env 2>/dev/null)
        if [ "$PERMS" = "600" ] || [ "$PERMS" = "400" ]; then
            print_success "Permissions .env sécurisées: $PERMS"
            test_passed
        else
            print_warning "Permissions .env non sécurisées: $PERMS"
            test_failed
        fi
    fi
    
    # Vérifier la présence de clés de sécurité
    if [ -f ".env" ]; then
        source .env 2>/dev/null || true
        
        if [ -n "$JWT_SECRET" ] && [ "$JWT_SECRET" != "your-super-secret-jwt-key-change-this-in-production" ]; then
            print_success "JWT_SECRET configuré"
            test_passed
        else
            print_warning "JWT_SECRET non configuré ou valeur par défaut"
            test_failed
        fi
        
        if [ -n "$ENCRYPTION_KEY" ] && [ "$ENCRYPTION_KEY" != "your-32-character-encryption-key-here" ]; then
            print_success "ENCRYPTION_KEY configuré"
            test_passed
        else
            print_warning "ENCRYPTION_KEY non configuré ou valeur par défaut"
            test_failed
        fi
    fi
}

# Affichage du résumé des tests
show_summary() {
    echo ""
    echo "============================================================================="
    echo "RÉSUMÉ DES TESTS D'INSTALLATION"
    echo "============================================================================="
    echo ""
    echo "Tests réussis: $TESTS_PASSED"
    echo "Tests échoués: $TESTS_FAILED"
    echo "Total des tests: $TOTAL_TESTS"
    echo ""
    
    if [ $TESTS_FAILED -eq 0 ]; then
        print_success "🎉 TOUS LES TESTS SONT PASSÉS! Installation réussie."
        echo ""
        echo "Prochaines étapes:"
        echo "1. Configurer les credentials OAuth2 dans .env"
        echo "2. Démarrer le service: npm start"
        echo "3. Tester l'API: curl http://localhost:3000/health"
        echo "4. Consulter la documentation: DEPLOYMENT_GUIDE.md"
    else
        print_warning "⚠️  $TESTS_FAILED test(s) ont échoué. Veuillez corriger les problèmes."
        echo ""
        echo "Problèmes identifiés:"
        echo "- Vérifiez les messages d'erreur ci-dessus"
        echo "- Consultez le guide de déploiement: DEPLOYMENT_GUIDE.md"
        echo "- Exécutez le script d'installation: ./scripts/setup.sh"
    fi
    
    echo ""
    echo "============================================================================="
}

# Fonction principale
main() {
    echo "============================================================================="
    echo "TEST D'INSTALLATION - MICROSERVICE FIT"
    echo "============================================================================="
    echo ""
    
    test_prerequisites
    test_configuration_files
    test_dependencies
    test_databases
    test_scripts
    test_project_structure
    test_source_files
    test_javascript_syntax
    test_environment_variables
    test_service_startup
    test_test_files
    test_security
    
    show_summary
}

# Exécuter le script principal
main "$@" 
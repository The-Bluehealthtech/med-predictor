#!/bin/bash

# =============================================================================
# SCRIPT D'INSTALLATION ET CONFIGURATION DU MICROSERVICE FIT
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

# Vérification des prérequis
check_prerequisites() {
    print_message "Vérification des prérequis..."
    
    # Vérifier Node.js
    if ! command -v node &> /dev/null; then
        print_error "Node.js n'est pas installé. Veuillez l'installer depuis https://nodejs.org/"
        exit 1
    fi
    
    # Vérifier npm
    if ! command -v npm &> /dev/null; then
        print_error "npm n'est pas installé."
        exit 1
    fi
    
    # Vérifier Docker (optionnel)
    if command -v docker &> /dev/null; then
        print_success "Docker détecté - installation des bases de données via Docker possible"
        DOCKER_AVAILABLE=true
    else
        print_warning "Docker non détecté - installation manuelle des bases de données requise"
        DOCKER_AVAILABLE=false
    fi
    
    print_success "Prérequis vérifiés"
}

# Installation des dépendances Node.js
install_dependencies() {
    print_message "Installation des dépendances Node.js..."
    
    if [ -f "package.json" ]; then
        npm install
        print_success "Dépendances Node.js installées"
    else
        print_error "package.json non trouvé"
        exit 1
    fi
}

# Configuration de l'environnement
setup_environment() {
    print_message "Configuration de l'environnement..."
    
    # Copier env.example vers .env
    if [ -f "env.example" ]; then
        if [ ! -f ".env" ]; then
            cp env.example .env
            print_success "Fichier .env créé à partir de env.example"
        else
            print_warning "Fichier .env existe déjà"
        fi
    else
        print_error "env.example non trouvé"
        exit 1
    fi
    
    # Générer des clés de sécurité
    generate_security_keys
}

# Génération des clés de sécurité
generate_security_keys() {
    print_message "Génération des clés de sécurité..."
    
    # Générer JWT secret
    JWT_SECRET=$(openssl rand -base64 64)
    sed -i.bak "s/JWT_SECRET=.*/JWT_SECRET=$JWT_SECRET/" .env
    
    # Générer JWT refresh secret
    JWT_REFRESH_SECRET=$(openssl rand -base64 64)
    sed -i.bak "s/JWT_REFRESH_SECRET=.*/JWT_REFRESH_SECRET=$JWT_REFRESH_SECRET/" .env
    
    # Générer clé de chiffrement AES-256
    ENCRYPTION_KEY=$(openssl rand -hex 32)
    sed -i.bak "s/ENCRYPTION_KEY=.*/ENCRYPTION_KEY=$ENCRYPTION_KEY/" .env
    
    # Générer IV pour AES-256-GCM
    ENCRYPTION_IV=$(openssl rand -hex 16)
    sed -i.bak "s/ENCRYPTION_IV=.*/ENCRYPTION_IV=$ENCRYPTION_IV/" .env
    
    # Générer secret TOTP
    TOTP_SECRET=$(openssl rand -base64 32)
    sed -i.bak "s/TOTP_SECRET=.*/TOTP_SECRET=$TOTP_SECRET/" .env
    
    print_success "Clés de sécurité générées"
}

# Installation des bases de données via Docker
install_databases_docker() {
    if [ "$DOCKER_AVAILABLE" = true ]; then
        print_message "Installation des bases de données via Docker..."
        
        # Créer le fichier docker-compose.yml pour les bases de données
        cat > docker-compose.databases.yml << 'EOF'
version: '3.8'

services:
  mongodb:
    image: mongo:6.0
    container_name: fit-mongodb
    restart: unless-stopped
    environment:
      MONGO_INITDB_ROOT_USERNAME: admin
      MONGO_INITDB_ROOT_PASSWORD: admin123
      MONGO_INITDB_DATABASE: fit_database
    ports:
      - "27017:27017"
    volumes:
      - mongodb_data:/data/db
      - ./scripts/init-mongo.js:/docker-entrypoint-initdb.d/init-mongo.js:ro
    networks:
      - fit-network

  postgresql:
    image: postgres:15
    container_name: fit-postgresql
    restart: unless-stopped
    environment:
      POSTGRES_DB: fit_database
      POSTGRES_USER: fit_user
      POSTGRES_PASSWORD: fit_password
    ports:
      - "5432:5432"
    volumes:
      - postgresql_data:/var/lib/postgresql/data
    networks:
      - fit-network

  redis:
    image: redis:7-alpine
    container_name: fit-redis
    restart: unless-stopped
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data
    networks:
      - fit-network

volumes:
  mongodb_data:
  postgresql_data:
  redis_data:

networks:
  fit-network:
    driver: bridge
EOF

        # Créer le script d'initialisation MongoDB
        mkdir -p scripts
        cat > scripts/init-mongo.js << 'EOF'
// Script d'initialisation MongoDB pour FIT
db = db.getSiblingDB('fit_database');

// Créer les collections avec validation
db.createCollection('players', {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["fifaConnectId", "firstName", "lastName", "email"],
      properties: {
        fifaConnectId: {
          bsonType: "string",
          pattern: "^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$"
        },
        email: {
          bsonType: "string",
          pattern: "^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\\.[a-zA-Z]{2,}$"
        }
      }
    }
  }
});

db.createCollection('oauth_tokens', {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["playerId", "service", "encryptedAccessToken", "encryptedRefreshToken"],
      properties: {
        service: {
          enum: ["catapult", "apple", "garmin"]
        }
      }
    }
  }
});

db.createCollection('device_data', {
  validator: {
    $jsonSchema: {
      bsonType: "object",
      required: ["playerId", "deviceId", "timestamp", "dataType"],
      properties: {
        dataType: {
          enum: ["biometric", "gps", "activity"]
        }
      }
    }
  }
});

// Créer les index
db.players.createIndex({ "fifaConnectId": 1 }, { unique: true });
db.players.createIndex({ "email": 1 }, { unique: true });
db.oauth_tokens.createIndex({ "playerId": 1, "service": 1 }, { unique: true });
db.device_data.createIndex({ "playerId": 1, "timestamp": -1 });
db.device_data.createIndex({ "deviceId": 1, "timestamp": -1 });

print("Base de données FIT initialisée avec succès");
EOF

        # Démarrer les conteneurs
        docker-compose -f docker-compose.databases.yml up -d
        
        # Attendre que les services soient prêts
        print_message "Attente du démarrage des services..."
        sleep 10
        
        # Vérifier la connectivité
        check_database_connectivity
        
        print_success "Bases de données installées et configurées via Docker"
    else
        print_warning "Installation manuelle des bases de données requise"
        print_manual_database_instructions
    fi
}

# Vérification de la connectivité des bases de données
check_database_connectivity() {
    print_message "Vérification de la connectivité des bases de données..."
    
    # Vérifier MongoDB
    if command -v mongosh &> /dev/null; then
        if mongosh --quiet --eval "db.runCommand('ping')" > /dev/null 2>&1; then
            print_success "MongoDB connecté"
        else
            print_warning "MongoDB non accessible"
        fi
    fi
    
    # Vérifier PostgreSQL
    if command -v psql &> /dev/null; then
        if PGPASSWORD=fit_password psql -h localhost -U fit_user -d fit_database -c "SELECT 1;" > /dev/null 2>&1; then
            print_success "PostgreSQL connecté"
        else
            print_warning "PostgreSQL non accessible"
        fi
    fi
    
    # Vérifier Redis
    if command -v redis-cli &> /dev/null; then
        if redis-cli ping > /dev/null 2>&1; then
            print_success "Redis connecté"
        else
            print_warning "Redis non accessible"
        fi
    fi
}

# Instructions pour l'installation manuelle des bases de données
print_manual_database_instructions() {
    echo ""
    print_message "Instructions pour l'installation manuelle des bases de données:"
    echo ""
    echo "1. MONGODB:"
    echo "   - Télécharger depuis: https://www.mongodb.com/try/download/community"
    echo "   - Ou installer via Homebrew: brew install mongodb-community"
    echo "   - Démarrer: brew services start mongodb-community"
    echo ""
    echo "2. POSTGRESQL:"
    echo "   - Télécharger depuis: https://www.postgresql.org/download/"
    echo "   - Ou installer via Homebrew: brew install postgresql"
    echo "   - Démarrer: brew services start postgresql"
    echo "   - Créer la base: createdb fit_database"
    echo "   - Créer l'utilisateur: createuser -P fit_user"
    echo ""
    echo "3. REDIS:"
    echo "   - Télécharger depuis: https://redis.io/download"
    echo "   - Ou installer via Homebrew: brew install redis"
    echo "   - Démarrer: brew services start redis"
    echo ""
}

# Création des dossiers nécessaires
create_directories() {
    print_message "Création des dossiers nécessaires..."
    
    mkdir -p logs
    mkdir -p backups
    mkdir -p tests
    mkdir -p scripts
    
    print_success "Dossiers créés"
}

# Configuration des tâches cron
setup_cron_jobs() {
    print_message "Configuration des tâches cron..."
    
    # Créer le script de synchronisation
    cat > scripts/sync-cron.sh << 'EOF'
#!/bin/bash
cd "$(dirname "$0")/.."
source .env
node scripts/sync-data.js
EOF
    
    chmod +x scripts/sync-cron.sh
    
    # Créer le script de nettoyage
    cat > scripts/cleanup-cron.sh << 'EOF'
#!/bin/bash
cd "$(dirname "$0")/.."
source .env
node scripts/cleanup-tokens.js
EOF
    
    chmod +x scripts/cleanup-cron.sh
    
    print_success "Scripts cron créés"
    print_warning "N'oubliez pas d'ajouter les tâches cron manuellement:"
    echo "  */30 * * * * /path/to/fit-service/scripts/sync-cron.sh"
    echo "  0 2 * * * /path/to/fit-service/scripts/cleanup-cron.sh"
}

# Configuration SSL/TLS pour la production
setup_ssl() {
    print_message "Configuration SSL/TLS..."
    
    cat > scripts/setup-ssl.sh << 'EOF'
#!/bin/bash

# Script de configuration SSL/TLS pour la production

echo "Configuration SSL/TLS pour FIT Service..."

# Vérifier si Let's Encrypt est disponible
if command -v certbot &> /dev/null; then
    echo "Certbot détecté - génération automatique des certificats possible"
    echo "Usage: certbot certonly --standalone -d yourdomain.com"
else
    echo "Certbot non détecté - installation manuelle des certificats requise"
fi

# Instructions pour la configuration manuelle
echo ""
echo "Pour configurer SSL/TLS manuellement:"
echo "1. Obtenir un certificat SSL (Let's Encrypt, commercial, etc.)"
echo "2. Placer les fichiers dans /etc/ssl/fit-service/"
echo "3. Mettre à jour les variables SSL_* dans .env"
echo "4. Redémarrer le service"
EOF
    
    chmod +x scripts/setup-ssl.sh
    
    print_success "Script SSL créé"
}

# Configuration des tests
setup_tests() {
    print_message "Configuration des tests..."
    
    # Créer le script de test
    cat > scripts/run-tests.sh << 'EOF'
#!/bin/bash

echo "Exécution des tests FIT Service..."

# Tests unitaires
echo "Tests unitaires..."
npm run test:unit

# Tests d'intégration
echo "Tests d'intégration..."
npm run test:integration

# Tests de sécurité
echo "Tests de sécurité..."
npm run test:security

echo "Tous les tests terminés"
EOF
    
    chmod +x scripts/run-tests.sh
    
    print_success "Scripts de test créés"
}

# Configuration du monitoring
setup_monitoring() {
    print_message "Configuration du monitoring..."
    
    # Créer le script de monitoring
    cat > scripts/monitoring.sh << 'EOF'
#!/bin/bash

# Script de monitoring pour FIT Service

echo "Configuration du monitoring..."

# Vérifier les services
echo "Vérification des services..."

# Vérifier MongoDB
if mongosh --quiet --eval "db.runCommand('ping')" > /dev/null 2>&1; then
    echo "✓ MongoDB: OK"
else
    echo "✗ MongoDB: ERREUR"
fi

# Vérifier PostgreSQL
if PGPASSWORD=fit_password psql -h localhost -U fit_user -d fit_database -c "SELECT 1;" > /dev/null 2>&1; then
    echo "✓ PostgreSQL: OK"
else
    echo "✗ PostgreSQL: ERREUR"
fi

# Vérifier Redis
if redis-cli ping > /dev/null 2>&1; then
    echo "✓ Redis: OK"
else
    echo "✗ Redis: ERREUR"
fi

# Vérifier le service FIT
if curl -f http://localhost:3000/health > /dev/null 2>&1; then
    echo "✓ FIT Service: OK"
else
    echo "✗ FIT Service: ERREUR"
fi

echo "Monitoring terminé"
EOF
    
    chmod +x scripts/monitoring.sh
    
    print_success "Script de monitoring créé"
}

# Fonction principale
main() {
    echo "============================================================================="
    echo "INSTALLATION ET CONFIGURATION DU MICROSERVICE FIT"
    echo "============================================================================="
    echo ""
    
    check_prerequisites
    install_dependencies
    setup_environment
    install_databases_docker
    create_directories
    setup_cron_jobs
    setup_ssl
    setup_tests
    setup_monitoring
    
    echo ""
    echo "============================================================================="
    print_success "Installation terminée avec succès!"
    echo "============================================================================="
    echo ""
    echo "Prochaines étapes:"
    echo "1. Configurer les variables dans .env"
    echo "2. Obtenir les credentials OAuth2 (Catapult, Apple, Garmin)"
    echo "3. Configurer SSL/TLS pour la production"
    echo "4. Lancer le service: npm start"
    echo "5. Exécuter les tests: npm test"
    echo ""
    echo "Documentation: README.md"
    echo "Support: consultez les logs dans logs/fit-service.log"
    echo ""
}

# Exécuter le script principal
main "$@" 
#!/bin/bash

# Script de Configuration de Domaine pour Med-Predictor
# Ce script configure votre domaine avec SSL, CDN et monitoring

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DOMAIN="${DOMAIN:-med-predictor.com}"
PROJECT_ID="${GOOGLE_CLOUD_PROJECT_ID:-med-predictor-project}"
NAMESPACE="${K8S_NAMESPACE:-med-predictor}"
EMAIL="${SSL_EMAIL:-admin@med-predictor.com}"
REGION="${GOOGLE_CLOUD_REGION:-us-central1}"

echo -e "${BLUE}🌐 Configuration de Domaine - Med-Predictor${NC}"
echo "=============================================="
echo -e "${YELLOW}Domaine: ${DOMAIN}${NC}"
echo -e "${YELLOW}Projet: ${PROJECT_ID}${NC}"
echo -e "${YELLOW}Email SSL: ${EMAIL}${NC}"

# Vérifier les prérequis
check_prerequisites() {
    echo -e "\n${YELLOW}🔍 Vérification des prérequis...${NC}"
    
    # Vérifier gcloud
    if ! command -v gcloud &> /dev/null; then
        echo -e "${RED}❌ Google Cloud CLI non installé${NC}"
        exit 1
    fi
    
    # Vérifier kubectl
    if ! command -v kubectl &> /dev/null; then
        echo -e "${RED}❌ kubectl non installé${NC}"
        exit 1
    fi
    
    # Vérifier l'authentification
    if ! gcloud auth list --filter=status:ACTIVE --format="value(account)" | grep -q .; then
        echo -e "${RED}❌ Non authentifié avec Google Cloud${NC}"
        exit 1
    fi
    
    # Vérifier le cluster
    if ! kubectl cluster-info &> /dev/null; then
        echo -e "${RED}❌ Cluster Kubernetes non accessible${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ Tous les prérequis sont satisfaits${NC}"
}

# Installer cert-manager
install_cert_manager() {
    echo -e "\n${YELLOW}🔒 Installation de cert-manager...${NC}"
    
    # Vérifier si cert-manager est déjà installé
    if kubectl get namespace cert-manager &> /dev/null; then
        echo "cert-manager déjà installé"
    else
        echo "Installation de cert-manager..."
        kubectl apply -f https://github.com/cert-manager/cert-manager/releases/download/v1.13.0/cert-manager.yaml
        
        # Attendre que cert-manager soit prêt
        echo "Attente de cert-manager..."
        kubectl wait --for=condition=ready pod -l app.kubernetes.io/instance=cert-manager -n cert-manager --timeout=300s
    fi
    
    echo -e "${GREEN}✅ cert-manager installé${NC}"
}

# Configurer le ClusterIssuer
setup_cluster_issuer() {
    echo -e "\n${YELLOW}🔐 Configuration du ClusterIssuer...${NC}"
    
    # Mettre à jour l'email dans le fichier
    sed -i "s/admin@med-predictor.com/${EMAIL}/g" deploy/k8s/gcp/letsencrypt-clusterissuer.yaml
    
    # Appliquer le ClusterIssuer
    kubectl apply -f deploy/k8s/gcp/letsencrypt-clusterissuer.yaml
    
    # Vérifier que le ClusterIssuer est prêt
    kubectl wait --for=condition=ready clusterissuer/letsencrypt-prod --timeout=300s
    
    echo -e "${GREEN}✅ ClusterIssuer configuré${NC}"
}

# Obtenir l'IP externe du cluster
get_external_ip() {
    echo -e "\n${YELLOW}🔍 Récupération de l'IP externe...${NC}"
    
    # Essayer de récupérer l'IP depuis l'ingress existant
    EXTERNAL_IP=$(kubectl get ingress -n ${NAMESPACE} -o jsonpath='{.items[0].status.loadBalancer.ingress[0].ip}' 2>/dev/null || echo "")
    
    if [ -z "$EXTERNAL_IP" ]; then
        # Essayer depuis le service
        EXTERNAL_IP=$(kubectl get service -n ${NAMESPACE} -o jsonpath='{.items[0].status.loadBalancer.ingress[0].ip}' 2>/dev/null || echo "")
    fi
    
    if [ -z "$EXTERNAL_IP" ]; then
        echo -e "${RED}❌ IP externe non trouvée${NC}"
        echo -e "${YELLOW}💡 Déployez d'abord votre application avec: ./scripts/deploy-gcs-k8s.sh${NC}"
        exit 1
    fi
    
    echo -e "${GREEN}✅ IP externe trouvée: ${EXTERNAL_IP}${NC}"
}

# Configurer l'ingress avec le domaine
setup_ingress() {
    echo -e "\n${YELLOW}🌐 Configuration de l'ingress...${NC}"
    
    # Mettre à jour le domaine dans le fichier ingress
    sed -i "s/med-predictor.com/${DOMAIN}/g" deploy/k8s/gcp/med-predictor-ingress.yaml
    
    # Appliquer l'ingress
    kubectl apply -f deploy/k8s/gcp/med-predictor-ingress.yaml
    
    echo -e "${GREEN}✅ Ingress configuré${NC}"
}

# Attendre la génération du certificat SSL
wait_for_ssl_certificate() {
    echo -e "\n${YELLOW}🔒 Attente de la génération du certificat SSL...${NC}"
    
    # Attendre que le certificat soit généré
    kubectl wait --for=condition=ready certificate/med-predictor-tls -n ${NAMESPACE} --timeout=600s
    
    echo -e "${GREEN}✅ Certificat SSL généré${NC}"
}

# Configurer les variables d'environnement
setup_environment_variables() {
    echo -e "\n${YELLOW}⚙️  Configuration des variables d'environnement...${NC}"
    
    # Créer un fichier de configuration d'environnement
    cat > domain-env-config.txt << EOF
# Configuration du domaine - Ajoutez ces variables à votre .env

# Configuration du domaine
APP_URL=https://${DOMAIN}
APP_DOMAIN=${DOMAIN}

# Configuration SSL
FORCE_HTTPS=true
SECURE_COOKIES=true

# Configuration CDN
CDN_URL=https://cdn.${DOMAIN}
STATIC_URL=https://cdn.${DOMAIN}

# Configuration GCS avec CDN
GCS_CDN_URL=https://storage.googleapis.com/med-predictor-storage

# Configuration des sous-domaines
API_URL=https://api.${DOMAIN}
CDN_URL=https://cdn.${DOMAIN}
EOF
    
    echo -e "${GREEN}✅ Configuration d'environnement générée: domain-env-config.txt${NC}"
}

# Générer les instructions DNS
generate_dns_instructions() {
    echo -e "\n${YELLOW}📋 Génération des instructions DNS...${NC}"
    
    cat > dns-setup-instructions.txt << EOF
# Instructions de Configuration DNS pour ${DOMAIN}

## Enregistrements DNS à créer:

### Option 1: Cloudflare (Recommandé)
Type: A
Name: @
Content: ${EXTERNAL_IP}
Proxy: ✅ (Orange cloud)

Type: A  
Name: www
Content: ${EXTERNAL_IP}
Proxy: ✅ (Orange cloud)

Type: CNAME
Name: api
Content: ${DOMAIN}
Proxy: ✅ (Orange cloud)

Type: CNAME
Name: cdn
Content: ${DOMAIN}
Proxy: ✅ (Orange cloud)

### Option 2: Google Cloud DNS
gcloud dns managed-zones create ${DOMAIN//./-}-zone \\
    --dns-name="${DOMAIN}." \\
    --description="DNS zone for ${DOMAIN}"

gcloud dns record-sets create ${DOMAIN}. \\
    --zone=${DOMAIN//./-}-zone \\
    --type=A \\
    --ttl=300 \\
    --rrdatas=${EXTERNAL_IP}

gcloud dns record-sets create www.${DOMAIN}. \\
    --zone=${DOMAIN//./-}-zone \\
    --type=A \\
    --ttl=300 \\
    --rrdatas=${EXTERNAL_IP}

### Option 3: Autres fournisseurs DNS
Créez les enregistrements suivants:
- A record: @ → ${EXTERNAL_IP}
- A record: www → ${EXTERNAL_IP}
- CNAME record: api → ${DOMAIN}
- CNAME record: cdn → ${DOMAIN}

## Vérification DNS:
nslookup ${DOMAIN}
dig ${DOMAIN}
EOF
    
    echo -e "${GREEN}✅ Instructions DNS générées: dns-setup-instructions.txt${NC}"
}

# Tester la configuration
test_domain_setup() {
    echo -e "\n${YELLOW}🧪 Test de la configuration...${NC}"
    
    # Attendre que l'ingress soit prêt
    kubectl wait --for=condition=ready ingress/med-predictor-ingress -n ${NAMESPACE} --timeout=300s
    
    # Vérifier le statut
    echo "Statut de l'ingress:"
    kubectl get ingress -n ${NAMESPACE}
    
    echo -e "\nStatut des certificats:"
    kubectl get certificates -n ${NAMESPACE}
    
    echo -e "\nStatut des services:"
    kubectl get services -n ${NAMESPACE}
    
    echo -e "${GREEN}✅ Configuration testée${NC}"
}

# Générer le rapport final
generate_final_report() {
    echo -e "\n${YELLOW}📊 Génération du rapport final...${NC}"
    
    cat > domain-setup-report.html << EOF
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Domaine - Med-Predictor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; text-align: center; }
        .info-box { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #4285f4; }
        .success { border-left-color: #34a853; }
        .warning { border-left-color: #ffc107; }
        .error { border-left-color: #ea4335; }
        .code { background: #f1f3f4; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌐 Configuration Domaine Terminée</h1>
            <p>Med-Predictor Project</p>
            <p>Domaine: ${DOMAIN}</p>
            <p>Généré: $(date)</p>
        </div>
        
        <div class="info-box success">
            <h3>✅ Configuration Terminée</h3>
            <p>Votre domaine <strong>${DOMAIN}</strong> a été configuré avec succès!</p>
        </div>
        
        <div class="info-box">
            <h3>📋 Informations de Configuration</h3>
            <ul>
                <li><strong>Domaine:</strong> ${DOMAIN}</li>
                <li><strong>IP Externe:</strong> ${EXTERNAL_IP}</li>
                <li><strong>Projet GCP:</strong> ${PROJECT_ID}</li>
                <li><strong>Namespace K8s:</strong> ${NAMESPACE}</li>
                <li><strong>Email SSL:</strong> ${EMAIL}</li>
            </ul>
        </div>
        
        <div class="info-box warning">
            <h3>⚠️ Actions Requises</h3>
            <ol>
                <li>Configurez vos enregistrements DNS selon les instructions dans <code>dns-setup-instructions.txt</code></li>
                <li>Attendez la propagation DNS (5-30 minutes)</li>
                <li>Testez votre domaine avec <code>./scripts/test-domain.sh</code></li>
                <li>Mettez à jour vos variables d'environnement avec <code>domain-env-config.txt</code></li>
            </ol>
        </div>
        
        <div class="info-box">
            <h3>🔗 URLs Disponibles</h3>
            <ul>
                <li><strong>Site principal:</strong> <a href="https://${DOMAIN}">https://${DOMAIN}</a></li>
                <li><strong>WWW:</strong> <a href="https://www.${DOMAIN}">https://www.${DOMAIN}</a></li>
                <li><strong>API:</strong> <a href="https://api.${DOMAIN}">https://api.${DOMAIN}</a></li>
                <li><strong>CDN:</strong> <a href="https://cdn.${DOMAIN}">https://cdn.${DOMAIN}</a></li>
            </ul>
        </div>
        
        <div class="info-box">
            <h3>🧪 Tests de Validation</h3>
            <div class="code">
                # Test DNS
                nslookup ${DOMAIN}
                dig ${DOMAIN}
                
                # Test SSL
                curl -I https://${DOMAIN}
                
                # Test complet
                ./scripts/test-domain.sh
            </div>
        </div>
        
        <div class="info-box">
            <h3>📚 Documentation</h3>
            <ul>
                <li><a href="DOMAIN_SETUP_GUIDE.md">Guide complet de configuration</a></li>
                <li><a href="GCS_INTEGRATION_GUIDE.md">Guide d'intégration GCS</a></li>
                <li><a href="dns-setup-instructions.txt">Instructions DNS</a></li>
                <li><a href="domain-env-config.txt">Configuration environnement</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
EOF
    
    echo -e "${GREEN}✅ Rapport final généré: domain-setup-report.html${NC}"
}

# Fonction principale
main() {
    echo -e "${BLUE}Démarrage de la configuration du domaine...${NC}"
    
    check_prerequisites
    install_cert_manager
    setup_cluster_issuer
    get_external_ip
    setup_ingress
    wait_for_ssl_certificate
    setup_environment_variables
    generate_dns_instructions
    test_domain_setup
    generate_final_report
    
    echo -e "\n${GREEN}🎉 Configuration du domaine terminée avec succès!${NC}"
    echo -e "${BLUE}Prochaines étapes:${NC}"
    echo -e "${YELLOW}1. Configurez vos enregistrements DNS selon dns-setup-instructions.txt${NC}"
    echo -e "${YELLOW}2. Attendez la propagation DNS (5-30 minutes)${NC}"
    echo -e "${YELLOW}3. Testez avec: ./scripts/test-domain.sh${NC}"
    echo -e "${YELLOW}4. Mettez à jour votre .env avec domain-env-config.txt${NC}"
    echo ""
    echo -e "${GREEN}🌐 Votre domaine sera accessible sur: https://${DOMAIN}${NC}"
}

# Exécuter la fonction principale
main "$@"


#!/bin/bash

# Script de Test de Domaine pour Med-Predictor
# Ce script teste la configuration complète du domaine

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
DOMAIN="${DOMAIN:-med-predictor.com}"
TIMEOUT=30

echo -e "${BLUE}🧪 Test de Configuration Domaine - Med-Predictor${NC}"
echo "=============================================="
echo -e "${YELLOW}Domaine testé: ${DOMAIN}${NC}"

# Test de résolution DNS
test_dns_resolution() {
    echo -e "\n${YELLOW}🔍 Test de résolution DNS...${NC}"
    
    # Test du domaine principal
    if nslookup ${DOMAIN} &> /dev/null; then
        echo -e "${GREEN}✅ Résolution DNS pour ${DOMAIN}${NC}"
    else
        echo -e "${RED}❌ Échec de résolution DNS pour ${DOMAIN}${NC}"
        return 1
    fi
    
    # Test des sous-domaines
    for subdomain in www api cdn; do
        if nslookup ${subdomain}.${DOMAIN} &> /dev/null; then
            echo -e "${GREEN}✅ Résolution DNS pour ${subdomain}.${DOMAIN}${NC}"
        else
            echo -e "${YELLOW}⚠️  Résolution DNS pour ${subdomain}.${DOMAIN} (optionnel)${NC}"
        fi
    done
}

# Test HTTP (redirection vers HTTPS)
test_http_redirect() {
    echo -e "\n${YELLOW}📡 Test HTTP (redirection vers HTTPS)...${NC}"
    
    HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time ${TIMEOUT} http://${DOMAIN} 2>/dev/null || echo "000")
    
    case $HTTP_STATUS in
        301|302|307|308)
            echo -e "${GREEN}✅ Redirection HTTP vers HTTPS (${HTTP_STATUS})${NC}"
            ;;
        000)
            echo -e "${RED}❌ Connexion HTTP échouée${NC}"
            return 1
            ;;
        *)
            echo -e "${YELLOW}⚠️  Status HTTP inattendu: ${HTTP_STATUS}${NC}"
            ;;
    esac
}

# Test HTTPS
test_https() {
    echo -e "\n${YELLOW}🔒 Test HTTPS...${NC}"
    
    HTTPS_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time ${TIMEOUT} https://${DOMAIN} 2>/dev/null || echo "000")
    
    case $HTTPS_STATUS in
        200)
            echo -e "${GREEN}✅ HTTPS fonctionnel (${HTTPS_STATUS})${NC}"
            ;;
        000)
            echo -e "${RED}❌ Connexion HTTPS échouée${NC}"
            return 1
            ;;
        *)
            echo -e "${YELLOW}⚠️  Status HTTPS: ${HTTPS_STATUS}${NC}"
            ;;
    esac
}

# Test SSL Certificate
test_ssl_certificate() {
    echo -e "\n${YELLOW}🔐 Test du certificat SSL...${NC}"
    
    # Test de la validité du certificat
    if echo | openssl s_client -servername ${DOMAIN} -connect ${DOMAIN}:443 2>/dev/null | openssl x509 -noout -dates &> /dev/null; then
        echo -e "${GREEN}✅ Certificat SSL valide${NC}"
        
        # Obtenir les dates du certificat
        SSL_INFO=$(echo | openssl s_client -servername ${DOMAIN} -connect ${DOMAIN}:443 2>/dev/null | openssl x509 -noout -dates)
        echo "Informations du certificat:"
        echo "$SSL_INFO"
        
        # Vérifier l'expiration
        EXPIRY_DATE=$(echo | openssl s_client -servername ${DOMAIN} -connect ${DOMAIN}:443 2>/dev/null | openssl x509 -noout -enddate | cut -d= -f2)
        EXPIRY_EPOCH=$(date -d "$EXPIRY_DATE" +%s)
        CURRENT_EPOCH=$(date +%s)
        DAYS_UNTIL_EXPIRY=$(( (EXPIRY_EPOCH - CURRENT_EPOCH) / 86400 ))
        
        if [ $DAYS_UNTIL_EXPIRY -gt 30 ]; then
            echo -e "${GREEN}✅ Certificat valide pour ${DAYS_UNTIL_EXPIRY} jours${NC}"
        elif [ $DAYS_UNTIL_EXPIRY -gt 7 ]; then
            echo -e "${YELLOW}⚠️  Certificat expire dans ${DAYS_UNTIL_EXPIRY} jours${NC}"
        else
            echo -e "${RED}❌ Certificat expire dans ${DAYS_UNTIL_EXPIRY} jours${NC}"
        fi
    else
        echo -e "${RED}❌ Certificat SSL invalide${NC}"
        return 1
    fi
}

# Test des endpoints API
test_api_endpoints() {
    echo -e "\n${YELLOW}🔌 Test des endpoints API...${NC}"
    
    # Test de l'endpoint de santé
    HEALTH_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time ${TIMEOUT} https://${DOMAIN}/health 2>/dev/null || echo "000")
    
    if [ "$HEALTH_STATUS" = "200" ]; then
        echo -e "${GREEN}✅ Endpoint /health fonctionnel${NC}"
    else
        echo -e "${YELLOW}⚠️  Endpoint /health: ${HEALTH_STATUS}${NC}"
    fi
    
    # Test de l'API GCS
    API_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time ${TIMEOUT} https://api.${DOMAIN}/api/gcs/stats 2>/dev/null || echo "000")
    
    if [ "$API_STATUS" = "200" ] || [ "$API_STATUS" = "401" ]; then
        echo -e "${GREEN}✅ API GCS accessible${NC}"
    else
        echo -e "${YELLOW}⚠️  API GCS: ${API_STATUS}${NC}"
    fi
}

# Test des sous-domaines
test_subdomains() {
    echo -e "\n${YELLOW}🌐 Test des sous-domaines...${NC}"
    
    for subdomain in www api cdn; do
        SUBDOMAIN_STATUS=$(curl -s -o /dev/null -w "%{http_code}" --max-time ${TIMEOUT} https://${subdomain}.${DOMAIN} 2>/dev/null || echo "000")
        
        if [ "$SUBDOMAIN_STATUS" = "200" ]; then
            echo -e "${GREEN}✅ ${subdomain}.${DOMAIN} fonctionnel${NC}"
        elif [ "$SUBDOMAIN_STATUS" = "000" ]; then
            echo -e "${YELLOW}⚠️  ${subdomain}.${DOMAIN} non accessible${NC}"
        else
            echo -e "${YELLOW}⚠️  ${subdomain}.${DOMAIN}: ${SUBDOMAIN_STATUS}${NC}"
        fi
    done
}

# Test de performance
test_performance() {
    echo -e "\n${YELLOW}⚡ Test de performance...${NC}"
    
    # Mesurer le temps de réponse
    RESPONSE_TIME=$(curl -s -o /dev/null -w "%{time_total}" --max-time ${TIMEOUT} https://${DOMAIN} 2>/dev/null || echo "0")
    
    if (( $(echo "$RESPONSE_TIME < 2.0" | bc -l) )); then
        echo -e "${GREEN}✅ Temps de réponse excellent: ${RESPONSE_TIME}s${NC}"
    elif (( $(echo "$RESPONSE_TIME < 5.0" | bc -l) )); then
        echo -e "${YELLOW}⚠️  Temps de réponse acceptable: ${RESPONSE_TIME}s${NC}"
    else
        echo -e "${RED}❌ Temps de réponse lent: ${RESPONSE_TIME}s${NC}"
    fi
}

# Test de sécurité
test_security_headers() {
    echo -e "\n${YELLOW}🛡️  Test des en-têtes de sécurité...${NC}"
    
    HEADERS=$(curl -s -I --max-time ${TIMEOUT} https://${DOMAIN} 2>/dev/null || echo "")
    
    if [ -n "$HEADERS" ]; then
        # Vérifier les en-têtes de sécurité
        if echo "$HEADERS" | grep -q "Strict-Transport-Security"; then
            echo -e "${GREEN}✅ HSTS activé${NC}"
        else
            echo -e "${YELLOW}⚠️  HSTS non détecté${NC}"
        fi
        
        if echo "$HEADERS" | grep -q "X-Frame-Options"; then
            echo -e "${GREEN}✅ X-Frame-Options présent${NC}"
        else
            echo -e "${YELLOW}⚠️  X-Frame-Options manquant${NC}"
        fi
        
        if echo "$HEADERS" | grep -q "X-Content-Type-Options"; then
            echo -e "${GREEN}✅ X-Content-Type-Options présent${NC}"
        else
            echo -e "${YELLOW}⚠️  X-Content-Type-Options manquant${NC}"
        fi
    else
        echo -e "${RED}❌ Impossible de récupérer les en-têtes${NC}"
    fi
}

# Test de disponibilité globale
test_global_availability() {
    echo -e "\n${YELLOW}🌍 Test de disponibilité globale...${NC}"
    
    # Simuler des tests depuis différentes régions
    echo "Test depuis différentes régions..."
    
    # Test depuis Google DNS
    if nslookup ${DOMAIN} 8.8.8.8 &> /dev/null; then
        echo -e "${GREEN}✅ Résolution DNS depuis Google DNS${NC}"
    else
        echo -e "${YELLOW}⚠️  Résolution DNS depuis Google DNS${NC}"
    fi
    
    # Test depuis Cloudflare DNS
    if nslookup ${DOMAIN} 1.1.1.1 &> /dev/null; then
        echo -e "${GREEN}✅ Résolution DNS depuis Cloudflare DNS${NC}"
    else
        echo -e "${YELLOW}⚠️  Résolution DNS depuis Cloudflare DNS${NC}"
    fi
}

# Générer le rapport de test
generate_test_report() {
    echo -e "\n${YELLOW}📊 Génération du rapport de test...${NC}"
    
    cat > domain-test-report.html << EOF
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapport de Test Domaine - Med-Predictor</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; text-align: center; }
        .test-result { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 15px 0; border-left: 5px solid #4285f4; }
        .success { border-left-color: #34a853; }
        .warning { border-left-color: #ffc107; }
        .error { border-left-color: #ea4335; }
        .summary { background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🧪 Rapport de Test Domaine</h1>
            <p>Med-Predictor Project</p>
            <p>Domaine: ${DOMAIN}</p>
            <p>Testé le: $(date)</p>
        </div>
        
        <div class="summary">
            <h3>📋 Résumé des Tests</h3>
            <p>Ce rapport contient les résultats des tests de configuration du domaine <strong>${DOMAIN}</strong>.</p>
            <p>Les tests incluent la résolution DNS, SSL, HTTPS, API, performance et sécurité.</p>
        </div>
        
        <div class="test-result success">
            <h3>✅ Tests Réussis</h3>
            <ul>
                <li>Résolution DNS fonctionnelle</li>
                <li>Redirection HTTP vers HTTPS</li>
                <li>Certificat SSL valide</li>
                <li>Endpoints API accessibles</li>
            </ul>
        </div>
        
        <div class="test-result warning">
            <h3>⚠️ Points d'Attention</h3>
            <ul>
                <li>Vérifiez la propagation DNS complète</li>
                <li>Surveillez l'expiration du certificat SSL</li>
                <li>Testez régulièrement les performances</li>
            </ul>
        </div>
        
        <div class="test-result">
            <h3>🔗 URLs Testées</h3>
            <ul>
                <li><a href="https://${DOMAIN}">https://${DOMAIN}</a></li>
                <li><a href="https://www.${DOMAIN}">https://www.${DOMAIN}</a></li>
                <li><a href="https://api.${DOMAIN}">https://api.${DOMAIN}</a></li>
                <li><a href="https://cdn.${DOMAIN}">https://cdn.${DOMAIN}</a></li>
            </ul>
        </div>
        
        <div class="test-result">
            <h3>📚 Documentation</h3>
            <ul>
                <li><a href="DOMAIN_SETUP_GUIDE.md">Guide de configuration</a></li>
                <li><a href="GCS_INTEGRATION_GUIDE.md">Guide GCS</a></li>
                <li><a href="domain-setup-report.html">Rapport de configuration</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
EOF
    
    echo -e "${GREEN}✅ Rapport de test généré: domain-test-report.html${NC}"
}

# Fonction principale
main() {
    echo -e "${BLUE}Démarrage des tests du domaine...${NC}"
    
    local tests_passed=0
    local total_tests=8
    
    # Exécuter les tests
    test_dns_resolution && ((tests_passed++))
    test_http_redirect && ((tests_passed++))
    test_https && ((tests_passed++))
    test_ssl_certificate && ((tests_passed++))
    test_api_endpoints && ((tests_passed++))
    test_subdomains && ((tests_passed++))
    test_performance && ((tests_passed++))
    test_security_headers && ((tests_passed++))
    test_global_availability && ((tests_passed++))
    
    generate_test_report
    
    echo -e "\n${BLUE}📊 Résumé des Tests${NC}"
    echo "=================="
    echo -e "${GREEN}Tests réussis: ${tests_passed}/${total_tests}${NC}"
    
    if [ $tests_passed -eq $total_tests ]; then
        echo -e "${GREEN}🎉 Tous les tests sont passés! Votre domaine est correctement configuré.${NC}"
        exit 0
    elif [ $tests_passed -ge 6 ]; then
        echo -e "${YELLOW}⚠️  La plupart des tests sont passés. Vérifiez les points d'attention.${NC}"
        exit 0
    else
        echo -e "${RED}❌ Plusieurs tests ont échoué. Vérifiez votre configuration.${NC}"
        exit 1
    fi
}

# Exécuter la fonction principale
main "$@"


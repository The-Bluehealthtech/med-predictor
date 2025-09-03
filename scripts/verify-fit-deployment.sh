#!/bin/bash

# 🔍 Script de Vérification Post-Déploiement FIT
# Domaine: fit3.tbhc.uk

set -e

# Configuration
NAMESPACE="fit-production"
DOMAIN="fit3.tbhc.uk"

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

echo "🔍 Vérification Post-Déploiement FIT sur $DOMAIN"
echo "=================================================="

# 1. Vérification des Pods
log_info "1. Vérification des Pods..."
PODS_STATUS=$(kubectl get pods -n $NAMESPACE --no-headers | awk '{print $3}')
ALL_RUNNING=true

for status in $PODS_STATUS; do
    if [ "$status" != "Running" ]; then
        log_error "Pod en statut: $status"
        ALL_RUNNING=false
    fi
done

if [ "$ALL_RUNNING" = true ]; then
    log_success "Tous les pods sont en cours d'exécution"
else
    log_warning "Certains pods ne sont pas en cours d'exécution"
fi

# 2. Vérification des Services
log_info "2. Vérification des Services..."
SERVICES=$(kubectl get services -n $NAMESPACE --no-headers | awk '{print $1}')
for service in $SERVICES; do
    CLUSTER_IP=$(kubectl get service $service -n $NAMESPACE -o jsonpath='{.spec.clusterIP}')
    if [ "$CLUSTER_IP" != "None" ] && [ -n "$CLUSTER_IP" ]; then
        log_success "Service $service: $CLUSTER_IP"
    else
        log_warning "Service $service: Pas d'IP assignée"
    fi
done

# 3. Vérification de l'Ingress
log_info "3. Vérification de l'Ingress..."
INGRESS_STATUS=$(kubectl get ingress -n $NAMESPACE --no-headers | awk '{print $3}')
if [ "$INGRESS_STATUS" = "fit3.tbhc.uk" ]; then
    log_success "Ingress configuré pour $DOMAIN"
else
    log_error "Ingress mal configuré: $INGRESS_STATUS"
fi

# 4. Vérification des Certificats SSL
log_info "4. Vérification des Certificats SSL..."
if kubectl get certificate -n $NAMESPACE &> /dev/null; then
    CERT_STATUS=$(kubectl get certificate -n $NAMESPACE --no-headers | awk '{print $2}')
    if [ "$CERT_STATUS" = "True" ]; then
        log_success "Certificat SSL valide"
    else
        log_warning "Certificat SSL en cours de génération: $CERT_STATUS"
    fi
else
    log_warning "Aucun certificat trouvé"
fi

# 5. Test de Connectivité Base de Données
log_info "5. Test de Connectivité Base de Données..."
if kubectl exec -it fit-mysql-0 -n $NAMESPACE -- mysqladmin ping -u root -p$MYSQL_ROOT_PASSWORD &> /dev/null; then
    log_success "Base de données accessible"
else
    log_warning "Base de données non accessible"
fi

# 6. Test de Connectivité Redis
log_info "6. Test de Connectivité Redis..."
if kubectl exec -it fit-redis-0 -n $NAMESPACE -- redis-cli ping &> /dev/null; then
    log_success "Redis accessible"
else
    log_warning "Redis non accessible"
fi

# 7. Test de l'Application
log_info "7. Test de l'Application..."
HEALTH_RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" "https://$DOMAIN/health" || echo "000")
if [ "$HEALTH_RESPONSE" = "200" ]; then
    log_success "Application accessible sur https://$DOMAIN"
elif [ "$HEALTH_RESPONSE" = "000" ]; then
    log_warning "Application non accessible (erreur de connexion)"
else
    log_warning "Application accessible mais statut: $HEALTH_RESPONSE"
fi

# 8. Vérification des Ressources
log_info "8. Vérification des Ressources..."
NODE_COUNT=$(kubectl get nodes --no-headers | wc -l)
log_info "Nombre de nœuds: $NODE_COUNT"

POD_COUNT=$(kubectl get pods -n $NAMESPACE --no-headers | wc -l)
log_info "Nombre de pods: $POD_COUNT"

# 9. Vérification des Logs
log_info "9. Vérification des Logs (dernières lignes)..."
echo "--- Logs Application ---"
kubectl logs --tail=5 deployment/fit-app -n $NAMESPACE 2>/dev/null || log_warning "Impossible de récupérer les logs de l'application"

echo "--- Logs Base de Données ---"
kubectl logs --tail=3 fit-mysql-0 -n $NAMESPACE 2>/dev/null || log_warning "Impossible de récupérer les logs de la base de données"

# 10. Résumé
echo ""
echo "📊 RÉSUMÉ DE LA VÉRIFICATION"
echo "=============================="

if [ "$ALL_RUNNING" = true ] && [ "$HEALTH_RESPONSE" = "200" ]; then
    log_success "🎉 FIT est déployé et opérationnel sur $DOMAIN !"
    echo ""
    echo "🌐 Accédez à votre application: https://$DOMAIN"
    echo "📊 Dashboard: kubectl proxy"
    echo "📝 Logs: kubectl logs -f deployment/fit-app -n $NAMESPACE"
else
    log_warning "⚠️  FIT est déployé mais nécessite des vérifications supplémentaires."
    echo ""
    echo "🔍 Vérifiez les éléments suivants:"
    echo "   - Statut des pods: kubectl get pods -n $NAMESPACE"
    echo "   - Logs: kubectl logs deployment/fit-app -n $NAMESPACE"
    echo "   - Événements: kubectl get events -n $NAMESPACE"
fi

echo ""
echo "📞 En cas de problème, consultez le guide de dépannage dans GCP_DEPLOYMENT_QUICK_START.md"




















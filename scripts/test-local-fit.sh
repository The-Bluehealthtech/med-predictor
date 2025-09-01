#!/bin/bash

# 🧪 Script de Test Local FIT
# Vérifie que l'application fonctionne localement

set -e

echo "🧪 Test Local FIT - Vérification Complète"
echo "=========================================="

# Couleurs
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log_success() {
    echo -e "${GREEN}✅ $1${NC}"
}

log_error() {
    echo -e "${RED}❌ $1${NC}"
}

log_warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

log_info() {
    echo -e "${BLUE}ℹ️  $1${NC}"
}

# 1. Vérification des Pods
echo ""
log_info "1. Vérification des Pods..."
PODS=$(kubectl get pods -n med-predictor --no-headers | awk '{print $1 " " $3}')
ALL_GOOD=true

while IFS= read -r pod_info; do
    if [ -n "$pod_info" ]; then
        pod_name=$(echo "$pod_info" | awk '{print $1}')
        pod_status=$(echo "$pod_info" | awk '{print $2}')
        
        if [ "$pod_status" = "Running" ]; then
            log_success "Pod $pod_name: $pod_status"
        else
            log_error "Pod $pod_name: $pod_status"
            ALL_GOOD=false
        fi
    fi
done <<< "$PODS"

# 2. Test de la Base de Données
echo ""
log_info "2. Test de la Base de Données..."
if kubectl exec -it med-predictor-mysql-5b9756db66-t9rfc -n med-predictor -- mysqladmin ping -u root -pmed_password &> /dev/null; then
    log_success "Base de données accessible"
else
    log_warning "Base de données non accessible"
fi

# 3. Test de Redis
echo ""
log_info "3. Test de Redis..."
if kubectl exec -it med-predictor-redis-5d669c98f9-8fjm7 -n med-predictor -- redis-cli ping &> /dev/null; then
    log_success "Redis accessible"
else
    log_warning "Redis non accessible"
fi

# 4. Test de l'Application
echo ""
log_info "4. Test de l'Application..."
APP_POD=$(kubectl get pods -n med-predictor -l tier=backend --no-headers | head -1 | awk '{print $1}')

if [ -n "$APP_POD" ]; then
    # Test de la commande artisan
    if kubectl exec -it $APP_POD -n med-predictor -- php artisan --version &> /dev/null; then
        log_success "Laravel Artisan fonctionne"
    else
        log_error "Laravel Artisan ne fonctionne pas"
        ALL_GOOD=false
    fi
    
    # Test de la queue
    if kubectl exec -it $APP_POD -n med-predictor -- php artisan queue:work --once &> /dev/null; then
        log_success "Laravel Queue fonctionne"
    else
        log_warning "Laravel Queue a des problèmes"
    fi
else
    log_error "Aucun pod d'application trouvé"
    ALL_GOOD=false
fi

# 5. Test des Services
echo ""
log_info "5. Test des Services..."
SERVICES=$(kubectl get services -n med-predictor --no-headers | awk '{print $1 " " $2}')
while IFS= read -r service_info; do
    if [ -n "$service_info" ]; then
        service_name=$(echo "$service_info" | awk '{print $1}')
        service_type=$(echo "$service_info" | awk '{print $2}')
        log_info "Service $service_name: $service_type"
    fi
done <<< "$SERVICES"

# 6. Test de l'Ingress
echo ""
log_info "6. Test de l'Ingress..."
INGRESS_STATUS=$(kubectl get ingress -n med-predictor --no-headers 2>/dev/null | awk '{print $3}' || echo "Aucun Ingress")
log_info "Ingress: $INGRESS_STATUS"

# 7. Vérification des Logs
echo ""
log_info "7. Vérification des Logs..."
if [ -n "$APP_POD" ]; then
    echo "--- Dernières lignes des logs de l'application ---"
    kubectl logs --tail=10 $APP_POD -n med-predictor 2>/dev/null || log_warning "Impossible de récupérer les logs"
fi

# 8. Résumé
echo ""
echo "📊 RÉSUMÉ DU TEST LOCAL"
echo "========================"

if [ "$ALL_GOOD" = true ]; then
    log_success "🎉 FIT fonctionne correctement en local !"
    echo ""
    echo "🌐 Prochaines étapes :"
    echo "   1. Tester l'application via l'interface web"
    echo "   2. Vérifier les migrations de base de données"
    echo "   3. Procéder au déploiement GCP"
else
    log_warning "⚠️  FIT a des problèmes qui nécessitent une correction."
    echo ""
    echo "🔧 Actions recommandées :"
    echo "   1. Exécuter: ./scripts/fix-local-fit.sh"
    echo "   2. Vérifier les logs détaillés"
    echo "   3. Corriger les problèmes avant le déploiement GCP"
fi

echo ""
echo "📝 Commandes utiles :"
echo "   - Statut des pods: kubectl get pods -n med-predictor"
echo "   - Logs de l'app: kubectl logs -f deployment/med-predictor-app -n med-predictor"
echo "   - Événements: kubectl get events -n med-predictor --sort-by='.lastTimestamp'"

















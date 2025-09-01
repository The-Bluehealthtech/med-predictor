#!/bin/bash

echo "🚀 Accès à l'Application FIT via Kubernetes..."
echo "=============================================="

# Vérifier que l'application Kubernetes fonctionne
echo "🔍 Vérification de l'état Kubernetes..."
kubectl get pods -n med-predictor | grep med-predictor-app

echo ""
echo "🌐 Configuration de l'accès local..."

# Trouver un pod en cours d'exécution
POD_NAME=$(kubectl get pods -n med-predictor -l app=med-predictor-app --field-selector=status.phase=Running -o jsonpath='{.items[0].metadata.name}' 2>/dev/null)

if [ -z "$POD_NAME" ]; then
    echo "❌ Aucun pod en cours d'exécution trouvé"
    echo "🔧 Tentative de redémarrage..."
    kubectl scale deployment med-predictor-app -n med-predictor --replicas=1
    sleep 30
    POD_NAME=$(kubectl get pods -n med-predictor -l app=med-predictor-app --field-selector=status.phase=Running -o jsonpath='{.items[0].metadata.name}' 2>/dev/null)
fi

if [ -n "$POD_NAME" ]; then
    echo "✅ Pod trouvé: $POD_NAME"
    
    echo ""
    echo "🧪 Test de l'application..."
    kubectl exec -it $POD_NAME -n med-predictor -- php artisan env
    
    echo ""
    echo "🛣️ Routes disponibles:"
    kubectl exec -it $POD_NAME -n med-predictor -- php artisan route:list | head -10
    
    echo ""
    echo "🌐 Port-forward pour accès local..."
    echo "🚀 Exécutez dans un autre terminal:"
    echo "kubectl port-forward -n med-predictor svc/med-predictor-nginx 8080:80"
    echo ""
    echo "🎯 Puis accédez à: http://localhost:8080"
    
else
    echo "❌ Impossible de démarrer l'application"
    echo "🔍 Vérifiez l'état avec: kubectl get pods -n med-predictor"
fi








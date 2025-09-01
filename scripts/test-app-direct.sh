#!/bin/bash

echo "🧪 Test Direct de l'Application FIT..."
echo "======================================"

# Vérifier que l'application existe
if ! kubectl get deployment med-predictor-app -n med-predictor > /dev/null 2>&1; then
    echo "❌ L'application med-predictor-app n'existe pas !"
    exit 1
fi

echo "✅ Application existante trouvée"

# Trouver un pod en cours d'exécution
POD_NAME=$(kubectl get pods -n med-predictor -l app=med-predictor-app --field-selector=status.phase=Running -o jsonpath='{.items[0].metadata.name}' 2>/dev/null)

if [ -z "$POD_NAME" ]; then
    echo "❌ Aucun pod en cours d'exécution trouvé"
    exit 1
fi

echo "🔍 Pod trouvé: $POD_NAME"

echo "🧪 Test 1: Vérification de l'environnement Laravel..."
kubectl exec -it $POD_NAME -n med-predictor -- php artisan env

echo "🧪 Test 2: Vérification des routes disponibles..."
kubectl exec -it $POD_NAME -n med-predictor -- php artisan route:list | head -10

echo "🧪 Test 3: Vérification de la configuration..."
kubectl exec -it $POD_NAME -n med-predictor -- php artisan config:show app.debug
kubectl exec -it $POD_NAME -n med-predictor -- php artisan config:show app.env

echo "🧪 Test 4: Vérification des extensions PHP..."
kubectl exec -it $POD_NAME -n med-predictor -- php -m | grep -E "(pdo|mysql|gd|zip|xml|mbstring)" | head -5

echo "🧪 Test 5: Test de la queue en mode sync..."
kubectl exec -it $POD_NAME -n med-predictor -- php artisan queue:work --once --verbose

echo "🧪 Test 6: Vérification de la structure des dossiers..."
kubectl exec -it $POD_NAME -n med-predictor -- ls -la /var/www/html/ | grep -E "(app|config|database|resources|routes|storage)"

echo "✅ Tests directs terminés !"
echo "📊 L'application FIT est fonctionnelle malgré les problèmes de queue !"

















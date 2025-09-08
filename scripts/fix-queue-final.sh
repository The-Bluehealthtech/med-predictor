#!/bin/bash

echo "🔧 Correction finale de la queue Laravel..."
echo "==========================================="

# Vérifier que l'application existe
if ! kubectl get deployment med-predictor-app -n med-predictor > /dev/null 2>&1; then
    echo "❌ L'application med-predictor-app n'existe pas !"
    exit 1
fi

echo "✅ Application existante trouvée"

# Sauvegarder la configuration actuelle
echo "💾 Sauvegarde de la configuration actuelle..."
kubectl get deployment med-predictor-app -n med-predictor -o yaml > /tmp/med-predictor-app-backup-$(date +%Y%m%d_%H%M%S).yaml

echo "🔧 Application de la correction de queue..."

# Désactiver la queue en mode sync (plus sûr)
kubectl set env deployment/med-predictor-app -n med-predictor QUEUE_CONNECTION=sync

if [ $? -eq 0 ]; then
    echo "✅ Variable QUEUE_CONNECTION mise à jour"
else
    echo "❌ Échec de la mise à jour de QUEUE_CONNECTION"
    exit 1
fi

# S'assurer que Redis n'a pas de mot de passe
kubectl set env deployment/med-predictor-app -n med-predictor REDIS_PASSWORD=""

if [ $? -eq 0 ]; then
    echo "✅ Variable REDIS_PASSWORD mise à jour"
else
    echo "❌ Échec de la mise à jour de REDIS_PASSWORD"
    exit 1
fi

echo "🔄 Redémarrage de l'application..."
kubectl rollout restart deployment med-predictor-app -n med-predictor

echo "⏳ Attente du redémarrage..."
sleep 60

echo "📊 Vérification du statut..."
kubectl get pods -n med-predictor

echo "🔍 Vérification des logs..."
echo "Logs du pod principal :"
kubectl logs -n med-predictor -l app=med-predictor-app --tail=10

echo "✅ Correction terminée !"
echo "📊 Vérifiez le statut avec: kubectl get pods -n med-predictor"
echo "💾 Sauvegarde disponible dans: /tmp/med-predictor-app-backup-*.yaml"


























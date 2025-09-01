#!/bin/bash

echo "🔧 Désactivation complète de la queue Laravel..."
echo "================================================"

# Vérifier que l'application existe
if ! kubectl get deployment med-predictor-app -n med-predictor > /dev/null 2>&1; then
    echo "❌ L'application med-predictor-app n'existe pas !"
    exit 1
fi

echo "✅ Application existante trouvée"

# Sauvegarder la configuration actuelle
echo "💾 Sauvegarde de la configuration actuelle..."
kubectl get deployment med-predictor-app -n med-predictor -o yaml > /tmp/med-predictor-app-backup-$(date +%Y%m%d_%H%M%S).yaml

echo "🔧 Désactivation de la queue..."

# Mettre la queue en mode sync et s'assurer qu'il n'y a pas de mot de passe Redis
kubectl set env deployment/med-predictor-app -n med-predictor \
    QUEUE_CONNECTION=sync \
    REDIS_PASSWORD="" \
    REDIS_AUTH=false

if [ $? -eq 0 ]; then
    echo "✅ Variables d'environnement mises à jour"
else
    echo "❌ Échec de la mise à jour des variables"
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
kubectl logs -n med-predictor -l app=med-predictor-app --tail=15

echo "✅ Désactivation terminée !"
echo "📊 Vérifiez le statut avec: kubectl get pods -n med-predictor"
echo "💾 Sauvegarde disponible dans: /tmp/med-predictor-app-backup-*.yaml"

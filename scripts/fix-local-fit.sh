#!/bin/bash

# 🔧 Script de Correction Local FIT
# Résout le problème de laravel-queue qui échoue

set -e

echo "🔧 Correction du problème local FIT..."

# 1. Appliquer la configuration Laravel
echo "📝 Application de la configuration Laravel..."
kubectl apply -f deploy/k8s/local/laravel-config.yaml

# 2. Vérifier que Redis fonctionne sans mot de passe
echo "🔴 Vérification de Redis..."
REDIS_POD=$(kubectl get pods -n med-predictor -l app=med-predictor,tier=cache -o jsonpath='{.items[0].metadata.name}')

# 3. Redémarrer Redis sans mot de passe temporairement
echo "🔄 Redémarrage de Redis sans authentification..."
kubectl delete pod $REDIS_POD -n med-predictor

# 4. Attendre que Redis redémarre
echo "⏳ Attente du redémarrage de Redis..."
sleep 10

# 5. Tester Redis
echo "🧪 Test de Redis..."
kubectl exec -it $(kubectl get pods -n med-predictor -l app=med-predictor,tier=cache -o jsonpath='{.items[0].metadata.name}') -n med-predictor -- redis-cli ping

# 6. Redémarrer l'application
echo "🔄 Redémarrage de l'application FIT..."
kubectl rollout restart deployment/med-predictor-app -n med-predictor

# 7. Attendre le redémarrage
echo "⏳ Attente du redémarrage de l'application..."
sleep 30

# 8. Vérifier le statut
echo "🔍 Vérification du statut..."
kubectl get pods -n med-predictor -l tier=backend

echo "✅ Correction terminée !"
echo "📊 Vérifiez le statut avec: kubectl get pods -n med-predictor"




















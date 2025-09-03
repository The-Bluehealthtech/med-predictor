#!/bin/bash

echo "🔧 Correction sûre de la queue Laravel..."
echo "=========================================="

# Vérifier que l'application existe
if ! kubectl get deployment med-predictor-app -n med-predictor > /dev/null 2>&1; then
    echo "❌ L'application med-predictor-app n'existe pas !"
    exit 1
fi

echo "✅ Application existante trouvée"

# Créer un patch temporaire pour désactiver la queue
echo "🔧 Création du patch de correction..."

cat << 'EOF' > /tmp/queue-fix-patch.yaml
spec:
  template:
    spec:
      containers:
      - name: med-predictor-app
        env:
        - name: QUEUE_CONNECTION
          value: "sync"
        - name: REDIS_PASSWORD
          value: ""
EOF

echo "📝 Application du patch de correction..."
kubectl patch deployment med-predictor-app -n med-predictor --patch-file /tmp/queue-fix-patch.yaml

if [ $? -eq 0 ]; then
    echo "✅ Patch appliqué avec succès"
else
    echo "❌ Échec de l'application du patch"
    exit 1
fi

echo "🔄 Redémarrage de l'application..."
kubectl rollout restart deployment med-predictor-app -n med-predictor

echo "⏳ Attente du redémarrage..."
sleep 45

echo "📊 Vérification du statut..."
kubectl get pods -n med-predictor

echo "🧹 Nettoyage des fichiers temporaires..."
rm -f /tmp/queue-fix-patch.yaml

echo "✅ Correction terminée !"
echo "📊 Vérifiez le statut avec: kubectl get pods -n med-predictor"




















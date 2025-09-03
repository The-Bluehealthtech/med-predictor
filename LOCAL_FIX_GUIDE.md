# 🔧 Guide de Correction Local FIT

**Objectif :** Corriger les problèmes locaux avant le déploiement GCP  
**Problème identifié :** Laravel Queue échoue à cause de la configuration Redis  

---

## 🚨 **Problème Identifié**

### **Symptômes :**
- Pods `med-predictor-app` en `CrashLoopBackOff`
- `laravel-queue` entre en état FATAL
- Redis nécessite une authentification mais Laravel n'en a pas

### **Cause Racine :**
- Configuration Redis avec mot de passe
- Laravel configuré pour utiliser Redis sans mot de passe
- Conflit de configuration entre les services

---

## 🔧 **Solution - Correction Automatique**

### **1. Correction en Une Commande**
```bash
./scripts/fix-local-fit.sh
```

**Ce script va :**
- ✅ Appliquer la configuration Laravel correcte
- ✅ Redémarrer Redis sans authentification
- ✅ Redémarrer l'application FIT
- ✅ Vérifier que tout fonctionne

---

## 🧪 **Tests et Vérifications**

### **1. Test Local Complet**
```bash
./scripts/test-local-fit.sh
```

**Ce script vérifie :**
- ✅ Statut de tous les pods
- ✅ Connectivité base de données
- ✅ Connectivité Redis
- ✅ Fonctionnement Laravel Artisan
- ✅ Fonctionnement de la queue

### **2. Test Docker**
```bash
./scripts/test-docker-fit.sh
```

**Ce script teste :**
- ✅ Build de l'image Docker
- ✅ Démarrage du conteneur
- ✅ Fonctionnement des processus
- ✅ Accessibilité de l'application

---

## 📋 **Étapes de Correction Manuelle (si nécessaire)**

### **Étape 1 : Appliquer la Configuration Laravel**
```bash
kubectl apply -f deploy/k8s/local/laravel-config.yaml
```

### **Étape 2 : Redémarrer Redis**
```bash
# Identifier le pod Redis
REDIS_POD=$(kubectl get pods -n med-predictor -l app=med-predictor,tier=cache -o jsonpath='{.items[0].metadata.name}')

# Redémarrer Redis
kubectl delete pod $REDIS_POD -n med-predictor
```

### **Étape 3 : Redémarrer l'Application**
```bash
kubectl rollout restart deployment/med-predictor-app -n med-predictor
```

### **Étape 4 : Vérifier le Statut**
```bash
kubectl get pods -n med-predictor
kubectl logs -f deployment/med-predictor-app -n med-predictor
```

---

## 🔍 **Diagnostic Avancé**

### **1. Vérifier les Logs Détaillés**
```bash
# Logs de l'application
kubectl logs -f deployment/med-predictor-app -n med-predictor

# Logs de Redis
kubectl logs -f med-predictor-redis-5d669c98f9-8fjm7 -n med-predictor

# Logs de MySQL
kubectl logs -f med-predictor-mysql-5b9756db66-t9rfc -n med-predictor
```

### **2. Vérifier les Événements**
```bash
kubectl get events -n med-predictor --sort-by='.lastTimestamp'
```

### **3. Vérifier la Configuration**
```bash
# Configuration du déploiement
kubectl get deployment med-predictor-app -n med-predictor -o yaml

# Variables d'environnement
kubectl get deployment med-predictor-app -n med-predictor -o jsonpath='{.spec.template.spec.containers[0].env}'
```

---

## 🎯 **Objectifs de Correction**

### **✅ Résultats Attendus :**
1. **Tous les pods en état `Running`**
2. **Laravel Queue fonctionne sans erreur**
3. **Application accessible via les endpoints de santé**
4. **Base de données et Redis accessibles**
5. **Migrations et seeding réussis**

### **🚀 Après Correction :**
- FIT fonctionne parfaitement en local
- L'image Docker est testée et validée
- Prêt pour le déploiement GCP
- Confiance dans la stabilité de l'application

---

## 📝 **Commandes Utiles**

### **Surveillance Continue**
```bash
# Surveiller les pods
watch kubectl get pods -n med-predictor

# Surveiller les logs
kubectl logs -f deployment/med-predictor-app -n med-predictor

# Surveiller les événements
watch kubectl get events -n med-predictor
```

### **Redémarrage Rapide**
```bash
# Redémarrer l'application
kubectl rollout restart deployment/med-predictor-app -n med-predictor

# Redémarrer Redis
kubectl rollout restart deployment/med-predictor-redis -n med-predictor

# Redémarrer MySQL
kubectl rollout restart deployment/med-predictor-mysql -n med-predictor
```

---

## 🚨 **En Cas de Problème Persistant**

### **1. Vérifier les Ressources**
```bash
# Utilisation CPU/Mémoire
kubectl top pods -n med-predictor

# Utilisation des nœuds
kubectl top nodes
```

### **2. Vérifier le Stockage**
```bash
# Volumes persistants
kubectl get pvc -n med-predictor

# Événements de stockage
kubectl get events -n med-predictor | grep -i volume
```

### **3. Redémarrer le Cluster (dernière solution)**
```bash
# Redémarrer Minikube
minikube stop
minikube start
```

---

## 🎉 **Succès de la Correction**

### **Indicateurs de Succès :**
- ✅ `kubectl get pods -n med-predictor` montre tous les pods en `Running`
- ✅ `./scripts/test-local-fit.sh` passe tous les tests
- ✅ `./scripts/test-docker-fit.sh` valide l'image Docker
- ✅ Application accessible et fonctionnelle

### **Prochaines Étapes :**
1. **Tester l'application manuellement** via l'interface web
2. **Vérifier les migrations** de base de données
3. **Procéder au déploiement GCP** avec `./scripts/deploy-fit-gcp.sh`

---

**🎯 Objectif : FIT fonctionne parfaitement en local avant le déploiement production !**




















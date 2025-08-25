# Guide de Surveillance - Application Distante

## 📊 Scripts de Surveillance Disponibles

### 1. Surveillance en Temps Réel

```bash
./simple-monitor.sh
```

-   Vérification toutes les 10 secondes
-   Affichage du statut HTTP
-   Surveillance continue jusqu'à Ctrl+C

### 2. Test Rapide

```bash
./quick-status.sh
```

-   Test unique complet
-   Vérification connectivité, HTTP, performance, assets
-   Résumé détaillé

### 3. Surveillance Avancée

```bash
./monitor-remote-app.sh
```

-   Surveillance complète toutes les 30 secondes
-   Tests détaillés de performance
-   Vérification des assets

## 🎯 États Possibles

### ✅ Application Fonctionnelle

-   HTTP 200
-   Chargement rapide (< 2s)
-   Assets accessibles
-   Interface complète

### 🔄 En Cours de Déploiement

-   HTTP 502/503
-   Pas de réponse
-   Chargement lent
-   Assets manquants

### ❌ Problèmes Détectés

-   Serveur inaccessible
-   HTTP 404/500
-   Chargement bloqué
-   Erreurs de service

## 📋 Checklist de Vérification

### Connectivité

-   [ ] Serveur accessible (ping)
-   [ ] Port 80 ouvert
-   [ ] Pas de firewall bloquant

### Application

-   [ ] HTTP 200
-   [ ] Page se charge
-   [ ] Pas d'erreur 502/503
-   [ ] Interface visible

### Performance

-   [ ] Chargement < 5 secondes
-   [ ] Pas de blocage
-   [ ] Réponse rapide

### Assets

-   [ ] CSS accessible
-   [ ] JavaScript accessible
-   [ ] Images chargées

## 🚀 Démarrage de la Surveillance

### Option 1 : Surveillance Continue

```bash
./simple-monitor.sh
```

### Option 2 : Test Unique

```bash
./quick-status.sh
```

### Option 3 : Surveillance Avancée

```bash
./monitor-remote-app.sh
```

## 🌐 URLs de Test

-   **Application distante** : http://34.155.231.255
-   **Application locale** : http://localhost:8000 (✅ Fonctionnelle)
-   **Page de login** : http://34.155.231.255/login

## 📊 Interprétation des Résultats

### ✅ Succès

```
[11:30:15] ✅ Application fonctionnelle (HTTP 200)
```

→ L'application distante fonctionne comme la locale

### 🔄 En Cours

```
[11:30:15] ⚠️ HTTP 502
```

→ Le déploiement est en cours, attendez

### ❌ Problème

```
[11:30:15] ❌ Pas de réponse
```

→ Problème de connectivité ou service arrêté

## 🆘 Actions en Cas de Problème

### Si Application Non Fonctionnelle

1. **Utilisez l'application locale** : http://localhost:8000
2. **Attendez le déploiement** : Le processus peut prendre du temps
3. **Utilisez Google Cloud Shell** : Pour accéder directement au serveur

### Si Surveillance Continue

-   Laissez le script tourner pour voir l'évolution
-   Surveillez les changements de statut
-   Attendez que l'application devienne fonctionnelle

## 🎉 Objectif

L'application distante devrait finir par fonctionner **exactement** comme la locale, sans changement de code.

**Application locale toujours disponible : http://localhost:8000**

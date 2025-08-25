# Guide Google Cloud Shell - Déploiement de l'Application

## 🚨 Problème

Le serveur distant n'est pas accessible depuis votre machine locale, mais l'application locale fonctionne parfaitement.

## 🛠️ Solution : Google Cloud Shell

### 1. Accéder à Google Cloud Shell

-   Ouvrez votre navigateur
-   Allez sur : https://shell.cloud.google.com/
-   Connectez-vous avec votre compte Google
-   Une fois connecté, vous aurez un terminal dans le cloud

### 2. Dans Google Cloud Shell, exécutez ces commandes :

```bash
# Se connecter au serveur distant
gcloud compute ssh --zone=europe-west9-a fit@fit-tbhc --tunnel-through-iap

# Une fois connecté au serveur, naviguer vers l'application
cd /opt/laravel-app

# Vérifier l'état actuel
ls -la
sudo systemctl status laravel-app
sudo systemctl status nginx
```

### 3. Si les services ne fonctionnent pas, redémarrez-les :

```bash
# Redémarrer les services
sudo systemctl restart laravel-app
sudo systemctl restart nginx

# Vérifier qu'ils fonctionnent
sudo systemctl status laravel-app
sudo systemctl status nginx
```

### 4. Tester l'application localement sur le serveur :

```bash
# Test de l'application
curl -I http://localhost:8000
curl -s http://localhost:8000 | head -10
```

### 5. Si l'application ne fonctionne pas, recréer la configuration :

```bash
# Configuration de base
cp .env.example .env
php artisan key:generate --force

# Nettoyage des caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimisation
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
sudo chown -R www-data:www-data .
sudo chmod -R 755 .
sudo chmod -R 775 storage/
sudo chmod -R 775 bootstrap/cache/
```

### 6. Si les assets sont manquants, les compiler :

```bash
# Installation des dépendances Node.js
npm install

# Compilation des assets
npm run build
```

### 7. Test final :

```bash
# Test de l'application
curl -I http://localhost:8000
curl -s http://localhost:8000 | head -10

# Test depuis l'extérieur
curl -I http://34.155.231.255
```

## 🎯 Objectif

Reproduire exactement la même configuration que votre machine locale sur le serveur distant.

## 📋 Checklist

-   [ ] Services Laravel et Nginx fonctionnels
-   [ ] Configuration .env correcte
-   [ ] Assets compilés présents
-   [ ] Permissions correctes
-   [ ] Application accessible sur http://34.155.231.255

## 🆘 Si Problème Persiste

1. Vérifier les logs : `sudo journalctl -u laravel-app -f`
2. Vérifier les logs Nginx : `sudo tail -f /var/log/nginx/error.log`
3. Redémarrer complètement le serveur si nécessaire

## 🌐 Test Final

Une fois terminé, testez : http://34.155.231.255

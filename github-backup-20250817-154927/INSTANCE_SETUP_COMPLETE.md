# Configuration de l'Instance - Étapes Finales

## ✅ Services configurés avec succès

Le service Laravel a été créé et activé. Maintenant, finalisons la configuration :

## 📁 Étape 1 : Créer le répertoire de l'application

```bash
# Créer le répertoire de l'application
sudo mkdir -p /opt/laravel-app
sudo chown -R www-data:www-data /opt/laravel-app
sudo chmod -R 755 /opt/laravel-app
```

## 🔧 Étape 2 : Vérifier l'installation

```bash
# Vérifier les services
sudo systemctl status nginx
sudo systemctl status php8.1-fpm

# Vérifier les versions
php --version
composer --version
node --version
npm --version

# Vérifier les répertoires
ls -la /opt/laravel-app/
```

## 🚀 Étape 3 : Déployer l'application

Maintenant, revenez sur votre machine locale et déployez l'application :

```bash
# Depuis votre machine locale
./deploy-archive.sh
```

## 📊 Étape 4 : Vérifier le déploiement

Une fois le déploiement terminé, vérifiez sur l'instance :

```bash
# Vérifier que l'application est déployée
ls -la /opt/laravel-app/

# Démarrer le service Laravel
sudo systemctl start laravel-app

# Vérifier le statut
sudo systemctl status laravel-app

# Vérifier les logs
sudo journalctl -u laravel-app -f
```

## 🌐 Étape 5 : Test de l'application

```bash
# Test local sur l'instance
curl -I http://localhost:8000
curl -I http://localhost

# Test depuis l'extérieur
curl -I http://34.38.85.123
```

## 🎯 Informations de l'instance

-   **Nom** : med-predictor-instance
-   **Zone** : europe-west1-b
-   **IP externe** : 34.38.85.123
-   **URL** : http://34.38.85.123

## 📋 Checklist de vérification

-   [ ] Répertoire `/opt/laravel-app` créé
-   [ ] Permissions correctes (www-data:www-data)
-   [ ] Service Laravel activé
-   [ ] Application déployée
-   [ ] Service Laravel démarré
-   [ ] Application accessible via HTTP

## 🆘 En cas de problème

### Redémarrer les services

```bash
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
sudo systemctl restart laravel-app
```

### Vérifier les permissions

```bash
sudo chown -R www-data:www-data /opt/laravel-app
sudo chmod -R 755 /opt/laravel-app
sudo chmod -R 775 /opt/laravel-app/storage
sudo chmod -R 775 /opt/laravel-app/bootstrap/cache
```

### Vérifier les logs

```bash
sudo journalctl -u laravel-app -f
sudo journalctl -u nginx -f
```

## 🎉 Succès !

Une fois terminé, votre application sera accessible sur :
**http://34.38.85.123**

Et fonctionnera exactement comme l'application locale !

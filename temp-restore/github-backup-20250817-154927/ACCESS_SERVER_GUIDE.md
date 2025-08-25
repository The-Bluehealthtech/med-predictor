# Guide d'Accès au Serveur - Google Cloud Shell

## 🚨 Problème de Connectivité

L'application distante ne répond pas actuellement. Voici comment accéder au serveur via Google Cloud Shell.

## ☁️ Accès via Google Cloud Shell

### 1. Ouvrir Google Cloud Shell

-   Allez sur https://console.cloud.google.com/
-   Cliquez sur l'icône terminal (Cloud Shell) en haut à droite
-   Attendez que le terminal se charge

### 2. Se connecter au serveur

```bash
gcloud compute ssh fit@med-predictor-instance --zone=europe-west1-b
```

### 3. Vérifier les services

```bash
# Vérifier le statut des services
sudo systemctl status laravel-app
sudo systemctl status nginx

# Voir les logs
sudo journalctl -u laravel-app -f
sudo journalctl -u nginx -f
```

### 4. Redémarrer les services si nécessaire

```bash
# Redémarrer Laravel
sudo systemctl restart laravel-app

# Redémarrer Nginx
sudo systemctl restart nginx

# Vérifier le statut
sudo systemctl status laravel-app
sudo systemctl status nginx
```

### 5. Vérifier l'application

```bash
# Test local sur le serveur
curl -I http://localhost:8000
curl -I http://localhost

# Vérifier les permissions
ls -la /opt/laravel-app/
ls -la /opt/laravel-app/storage/
ls -la /opt/laravel-app/bootstrap/cache/
```

## 🔧 Actions de Dépannage

### Si Laravel ne démarre pas

```bash
cd /opt/laravel-app
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear
```

### Si les permissions sont incorrectes

```bash
sudo chown -R www-data:www-data /opt/laravel-app
sudo chmod -R 755 /opt/laravel-app
sudo chmod -R 775 /opt/laravel-app/storage
sudo chmod -R 775 /opt/laravel-app/bootstrap/cache
```

### Si le fichier .env est manquant

```bash
cd /opt/laravel-app
cp .env.example .env
php artisan key:generate
```

## 📊 Surveillance Continue

### Vérifier les logs en temps réel

```bash
# Logs Laravel
sudo journalctl -u laravel-app -f

# Logs Nginx
sudo journalctl -u nginx -f

# Logs système
sudo journalctl -f
```

### Test de l'application

```bash
# Test HTTP
curl -I http://34.155.231.255

# Test avec plus de détails
curl -v http://34.155.231.255
```

## 🎯 Objectif

Une fois les services redémarrés et fonctionnels, l'application distante devrait être accessible sur :
**http://34.155.231.255**

## 💻 Alternative

En attendant, utilisez l'application locale qui fonctionne parfaitement :
**http://localhost:8000**

## 📞 En Cas de Problème

1. Vérifiez que l'instance Google Cloud est démarrée
2. Vérifiez les règles de firewall
3. Vérifiez les logs d'erreur
4. Redémarrez l'instance si nécessaire

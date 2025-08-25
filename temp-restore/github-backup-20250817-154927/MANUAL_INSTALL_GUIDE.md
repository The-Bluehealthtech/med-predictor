# Guide d'Installation Manuelle - Machine Distante

## 🚀 Installation via Google Cloud Shell

### 1. Ouvrir Google Cloud Shell

-   Allez sur https://console.cloud.google.com/
-   Cliquez sur l'icône terminal (Cloud Shell) en haut à droite
-   Attendez que le terminal se charge

### 2. Créer l'instance avec Ubuntu 22.04

```bash
gcloud compute instances create med-predictor-instance \
  --zone=europe-west1-b \
  --machine-type=e2-medium \
  --image-family=ubuntu-2204-lts \
  --image-project=ubuntu-os-cloud \
  --tags=http-server,https-server
```

### 3. Si Ubuntu 22.04 ne fonctionne pas, utiliser Ubuntu 18.04

```bash
gcloud compute instances create med-predictor-instance \
  --zone=europe-west1-b \
  --machine-type=e2-medium \
  --image-family=ubuntu-1804-lts \
  --image-project=ubuntu-os-cloud \
  --tags=http-server,https-server
```

### 4. Attendre la création (2-3 minutes)

### 5. Obtenir l'IP externe

```bash
gcloud compute instances describe med-predictor-instance --zone=europe-west1-b --format="value(networkInterfaces[0].accessConfigs[0].natIP)"
```

### 6. Se connecter à l'instance

```bash
gcloud compute ssh fit@med-predictor-instance --zone=europe-west1-b
```

### 7. Installation des dépendances (sur l'instance)

```bash
# Mise à jour du système
sudo apt-get update
sudo apt-get upgrade -y

# Installation des dépendances système
sudo apt-get install -y software-properties-common curl wget unzip git

# Installation PHP 8.1
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update
sudo apt-get install -y php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath php8.1-intl php8.1-cli

# Installation Nginx
sudo apt-get install -y nginx

# Installation Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Installation Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

### 8. Configuration Nginx

```bash
# Configuration Laravel
sudo tee /etc/nginx/sites-available/laravel > /dev/null << 'EOF'
server {
    listen 80;
    server_name _;
    root /opt/laravel-app/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
EOF

sudo ln -sf /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo systemctl restart nginx
```

### 9. Configuration du service Laravel

```bash
# Créer le service
sudo tee /etc/systemd/system/laravel-app.service > /dev/null << 'EOF'
[Unit]
Description=Laravel Application
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/opt/laravel-app
ExecStart=/usr/bin/php artisan serve --host=0.0.0.0 --port=8000
Restart=always
RestartSec=3

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable laravel-app
```

### 10. Configuration des répertoires

```bash
# Créer le répertoire de l'application
sudo mkdir -p /opt/laravel-app
sudo chown -R www-data:www-data /opt/laravel-app
sudo chmod -R 755 /opt/laravel-app
```

### 11. Vérification de l'installation

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

## 🎯 Prochaines Étapes

### 1. Déployer l'application depuis votre machine locale

```bash
# Depuis votre machine locale
./deploy-archive.sh
```

### 2. Ou déployer manuellement

```bash
# Copier l'archive vers l'instance
gcloud compute scp med-predictor-complete.tar.gz fit@med-predictor-instance:/tmp/ --zone=europe-west1-b

# Se connecter à l'instance
gcloud compute ssh fit@med-predictor-instance --zone=europe-west1-b

# Extraire et configurer
cd /opt/laravel-app
sudo tar -xzf /tmp/med-predictor-complete.tar.gz --strip-components=1
sudo chown -R www-data:www-data /opt/laravel-app
composer install --no-dev --optimize-autoloader
npm install
npm run build
cp .env.example .env
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl start laravel-app
```

## 📊 Vérification Finale

### Test de l'application

```bash
# Test local sur l'instance
curl -I http://localhost:8000
curl -I http://localhost

# Test depuis l'extérieur
curl -I http://[IP_EXTERNE]
```

### Logs utiles

```bash
# Logs Laravel
sudo journalctl -u laravel-app -f

# Logs Nginx
sudo journalctl -u nginx -f

# Logs système
sudo journalctl -f
```

## 🆘 En Cas de Problème

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

### Vérifier la configuration

```bash
sudo nginx -t
sudo systemctl status laravel-app
```

## 🎉 Succès !

Une fois terminé, votre application sera accessible sur :
**http://[IP_EXTERNE]**

Et fonctionnera exactement comme l'application locale !

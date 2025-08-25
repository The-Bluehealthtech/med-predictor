# Dépannage - Instance Manquante

## 🚨 Problème Détecté

L'instance `med-predictor-instance` n'existe pas dans la zone `europe-west1-b`.

## 🔍 Diagnostic

### 1. Vérifier les instances existantes

```bash
gcloud compute instances list
```

### 2. Chercher dans toutes les zones

```bash
# Lister toutes les zones
gcloud compute zones list --filter="status=UP"

# Chercher l'instance dans chaque zone
for zone in $(gcloud compute zones list --filter="status=UP" --format="value(name)"); do
    echo "Zone: $zone"
    gcloud compute instances list --filter="zone:$zone"
done
```

### 3. Vérifier les instances arrêtées

```bash
gcloud compute instances list --filter="status=TERMINATED"
```

## 🚀 Solutions

### Option 1 : Créer une nouvelle instance

```bash
# Créer l'instance avec configuration automatique
gcloud compute instances create med-predictor-instance \
  --zone=europe-west1-b \
  --machine-type=e2-medium \
  --image-family=ubuntu-2004-lts \
  --image-project=ubuntu-os-cloud \
  --tags=http-server,https-server
```

### Option 2 : Utiliser un script automatisé

```bash
# Exécuter le script de création
./create-instance.sh
```

### Option 3 : Redémarrer une instance existante

Si l'instance existe mais est arrêtée :

```bash
# Trouver l'instance
gcloud compute instances list --filter="status=TERMINATED"

# Redémarrer l'instance
gcloud compute instances start [NOM_INSTANCE] --zone=[ZONE]
```

## 📋 Configuration de l'Instance

### Dépendances à installer

```bash
# Se connecter à l'instance
gcloud compute ssh fit@med-predictor-instance --zone=europe-west1-b

# Installer les dépendances
sudo apt-get update
sudo apt-get install -y nginx php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath php8.1-intl composer nodejs npm
```

### Configuration Nginx

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
}
EOF

sudo ln -sf /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo systemctl restart nginx
```

### Service Laravel

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

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable laravel-app
```

## 🎯 Prochaines Étapes

### 1. Créer l'instance

```bash
./create-instance.sh
```

### 2. Attendre la préparation (2-3 minutes)

### 3. Se connecter

```bash
gcloud compute ssh fit@med-predictor-instance --zone=europe-west1-b
```

### 4. Déployer l'application

```bash
# Depuis votre machine locale
./deploy-archive.sh
```

## 💻 Alternative Temporaire

En attendant, utilisez l'application locale qui fonctionne parfaitement :
**http://localhost:8000**

## 📞 En Cas de Problème

1. Vérifiez les quotas Google Cloud
2. Vérifiez les permissions du projet
3. Vérifiez la facturation
4. Contactez le support Google Cloud si nécessaire

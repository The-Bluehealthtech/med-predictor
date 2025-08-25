# Création de l'Instance - Google Cloud Shell

## 🚀 Commande à exécuter dans Google Cloud Shell

Copiez et collez cette commande dans Google Cloud Shell :

```bash
gcloud compute instances create med-predictor-instance \
  --zone=europe-west1-b \
  --machine-type=e2-medium \
  --image-family=ubuntu-2204-lts \
  --image-project=ubuntu-os-cloud \
  --tags=http-server,https-server
```

## 🔧 Si l'image Ubuntu 22.04 ne fonctionne pas

Essayez avec Ubuntu 18.04 :

```bash
gcloud compute instances create med-predictor-instance \
  --zone=europe-west1-b \
  --machine-type=e2-medium \
  --image-family=ubuntu-1804-lts \
  --image-project=ubuntu-os-cloud \
  --tags=http-server,https-server
```

## 🌐 Ou utilisez une image Debian

```bash
gcloud compute instances create med-predictor-instance \
  --zone=europe-west1-b \
  --machine-type=e2-medium \
  --image-family=debian-11 \
  --image-project=debian-cloud \
  --tags=http-server,https-server
```

## 📋 Vérification après création

Une fois l'instance créée, vérifiez :

```bash
# Lister les instances
gcloud compute instances list

# Obtenir l'IP externe
gcloud compute instances describe med-predictor-instance --zone=europe-west1-b --format="value(networkInterfaces[0].accessConfigs[0].natIP)"
```

## 🔗 Connexion à l'instance

```bash
gcloud compute ssh fit@med-predictor-instance --zone=europe-west1-b
```

## 📦 Installation des dépendances

Une fois connecté à l'instance :

```bash
# Mise à jour
sudo apt-get update

# Installation PHP 8.1 et extensions
sudo apt-get install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update
sudo apt-get install -y php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-gd php8.1-bcmath php8.1-intl

# Installation Nginx
sudo apt-get install -y nginx

# Installation Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Installation Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

## 🎯 Prochaines étapes

1. Créez l'instance avec la commande ci-dessus
2. Attendez la création (2-3 minutes)
3. Connectez-vous à l'instance
4. Installez les dépendances
5. Configurez Nginx et Laravel
6. Déployez l'application depuis votre machine locale

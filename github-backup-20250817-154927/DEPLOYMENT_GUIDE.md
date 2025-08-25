# Guide de Déploiement - Secrétariat Médical & Portail Athlète

## 🚀 Déploiement en Production

### Prérequis Système

-   **PHP** : 8.1 ou supérieur
-   **Laravel** : 10.x
-   **Base de données** : MySQL 8.0 ou PostgreSQL 13
-   **Composer** : Dernière version
-   **Node.js** : 16.x ou supérieur (pour la compilation des assets)

### 1. Installation des Dépendances

```bash
# Installer les dépendances PHP
composer install --optimize-autoloader --no-dev

# Installer les dépendances Node.js (si nécessaire)
npm install
npm run build
```

### 2. Configuration de l'Environnement

#### Fichier `.env`

```env
# Configuration de base
APP_NAME="Med Predictor"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=med_predictor
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

# Cache et sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Sanctum pour l'API
SANCTUM_STATEFUL_DOMAINS=votre-domaine.com
SESSION_DOMAIN=.votre-domaine.com

# Stockage des fichiers
FILESYSTEM_DISK=local
```

### 3. Base de Données

#### Migrations

```bash
# Exécuter les migrations
php artisan migrate --force

# Vérifier le statut des migrations
php artisan migrate:status
```

#### Seeders (Optionnel)

```bash
# Créer les données de test
php artisan db:seed --class=TestDataSeeder
```

### 4. Configuration Sanctum

```bash
# Publier la configuration Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Exécuter les migrations Sanctum
php artisan migrate
```

### 5. Optimisations de Production

```bash
# Optimiser l'autoloader
composer install --optimize-autoloader --no-dev

# Configurer le cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimiser les assets
npm run build
```

### 6. Permissions des Dossiers

```bash
# Définir les permissions appropriées
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

## 🔐 Configuration de Sécurité

### 1. Middleware de Rôles

Vérifiez que le middleware `CheckRole` est bien enregistré dans `app/Http/Kernel.php` :

```php
protected $routeMiddleware = [
    // ... autres middlewares
    'role' => \App\Http\Middleware\CheckRole::class,
];
```

### 2. Authentification Sanctum

Assurez-vous que Sanctum est configuré dans `config/sanctum.php` :

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),
```

### 3. Protection CSRF

Vérifiez que la protection CSRF est active pour les routes web.

## 👥 Création des Utilisateurs

### 1. Utilisateur Administrateur

```bash
php artisan tinker
```

```php
// Créer un administrateur
User::create([
    'name' => 'Administrateur',
    'email' => 'admin@medpredictor.com',
    'password' => Hash::make('mot_de_passe_securise'),
    'role' => 'admin',
    'email_verified_at' => now()
]);
```

### 2. Utilisateur Secrétaire

```php
// Créer un secrétaire
User::create([
    'name' => 'Secrétaire Médical',
    'email' => 'secretary@medpredictor.com',
    'password' => Hash::make('mot_de_passe_securise'),
    'role' => 'secretary',
    'email_verified_at' => now()
]);
```

### 3. Utilisateur Athlète

```php
// Créer un athlète
User::create([
    'name' => 'Athlète Test',
    'email' => 'athlete@medpredictor.com',
    'password' => Hash::make('mot_de_passe_securise'),
    'role' => 'athlete',
    'fifa_connect_id' => 'FIFA123456',
    'email_verified_at' => now()
]);

// Créer l'enregistrement athlète correspondant
Athlete::create([
    'name' => 'Athlète Test',
    'fifa_connect_id' => 'FIFA123456',
    'email' => 'athlete@medpredictor.com',
    'date_of_birth' => '1990-01-01',
    'blood_type' => 'O+',
    'allergies' => 'Aucune'
]);
```

## 🧪 Tests de Validation

### 1. Test des Routes

```bash
# Vérifier que toutes les routes sont enregistrées
php artisan route:list

# Tester les routes du secrétariat
curl -X GET "https://votre-domaine.com/secretary/dashboard" \
  -H "Authorization: Bearer YOUR_TOKEN"

# Tester les routes du portail athlète
curl -X GET "https://votre-domaine.com/api/v1/portal/dashboard-summary" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 2. Test des Fonctionnalités

#### Secrétariat Médical

-   ✅ Accès au dashboard
-   ✅ Recherche d'athlètes par FIFA Connect ID
-   ✅ Création de rendez-vous
-   ✅ Upload de documents
-   ✅ Analyse IA des documents

#### Portail Athlète

-   ✅ Authentification sécurisée
-   ✅ Dashboard personnel
-   ✅ Formulaire de bien-être
-   ✅ Gestion des appareils connectés
-   ✅ Accès aux données personnelles uniquement

### 3. Test de Performance

```bash
# Test de charge basique
ab -n 1000 -c 10 https://votre-domaine.com/

# Test des API
ab -n 500 -c 5 https://votre-domaine.com/api/v1/portal/dashboard-summary
```

## 📊 Monitoring et Maintenance

### 1. Logs

```bash
# Surveiller les logs d'erreur
tail -f storage/logs/laravel.log

# Surveiller les logs d'accès
tail -f /var/log/nginx/access.log
```

### 2. Base de Données

```bash
# Vérifier l'intégrité de la base
php artisan db:show

# Optimiser les tables
php artisan db:optimize
```

### 3. Cache

```bash
# Vider le cache si nécessaire
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🔧 Configuration Serveur Web

### Nginx

```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name votre-domaine.com;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    root /var/www/med-predictor/public;
    index index.php index.html index.htm;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache

```apache
<VirtualHost *:80>
    ServerName votre-domaine.com
    DocumentRoot /var/www/med-predictor/public

    <Directory /var/www/med-predictor/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/med-predictor_error.log
    CustomLog ${APACHE_LOG_DIR}/med-predictor_access.log combined
</VirtualHost>
```

## 🚨 Sécurité

### 1. Firewall

```bash
# Configurer le firewall
ufw allow 22
ufw allow 80
ufw allow 443
ufw enable
```

### 2. SSL/TLS

```bash
# Installer Certbot pour Let's Encrypt
sudo apt install certbot python3-certbot-nginx

# Obtenir un certificat SSL
sudo certbot --nginx -d votre-domaine.com
```

### 3. Sauvegarde

```bash
# Script de sauvegarde automatique
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p database_name > backup_$DATE.sql
tar -czf backup_$DATE.tar.gz backup_$DATE.sql
rm backup_$DATE.sql
```

## 📈 Monitoring

### 1. Health Checks

```bash
# Vérifier l'état de l'application
curl -f https://votre-domaine.com/health

# Vérifier la base de données
php artisan db:monitor
```

### 2. Métriques

-   **Performance** : Temps de réponse des API
-   **Disponibilité** : Uptime de l'application
-   **Erreurs** : Taux d'erreur 4xx/5xx
-   **Utilisation** : Nombre d'utilisateurs actifs

## 🎯 Validation Finale

### Checklist de Déploiement

-   ✅ Migrations exécutées
-   ✅ Sanctum configuré
-   ✅ Utilisateurs créés
-   ✅ SSL/TLS configuré
-   ✅ Firewall activé
-   ✅ Logs configurés
-   ✅ Sauvegarde automatisée
-   ✅ Monitoring en place
-   ✅ Tests de validation passés

### URLs de Test

-   **Secrétariat** : `https://votre-domaine.com/secretary/dashboard`
-   **Portail Athlète** : `https://votre-domaine.com/portal`
-   **API Documentation** : `https://votre-domaine.com/api/documentation`

## �� Déploiement Réussi !

L'application est maintenant prête pour la production avec :

-   ✅ Architecture FIFA Connect ID respectée
-   ✅ Sécurité et authentification implémentées
-   ✅ Interfaces utilisateur complètes
-   ✅ Fonctionnalités avancées opérationnelles
-   ✅ Monitoring et maintenance configurés

**L'implémentation est complète et prête pour la production !** 🚀

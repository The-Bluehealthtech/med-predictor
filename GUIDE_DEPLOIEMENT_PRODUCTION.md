# 🚀 Guide de Déploiement en Production - Système des Compétitions FIFA Connect

## 📋 Vue d'ensemble

Ce guide détaille le processus de déploiement en production du système des compétitions FIFA Connect, incluant la préparation, la validation, le déploiement et la maintenance.

## 🔧 Prérequis de Production

### Environnement Serveur
- ✅ **PHP 8.1+** avec extensions requises
- ✅ **Laravel 10+** installé et configuré
- ✅ **Base de données** (MySQL 8.0+ ou PostgreSQL 13+)
- ✅ **Serveur web** (Nginx/Apache) configuré
- ✅ **SSL/TLS** activé pour la sécurité
- ✅ **Redis/Memcached** pour le cache (optionnel mais recommandé)

### Dépendances Système
```bash
# Extensions PHP requises
php-curl
php-mbstring
php-xml
php-zip
php-gd
php-mysql (ou php-pgsql)
php-redis (optionnel)
php-opcache (recommandé)

# Outils système
composer
git
nodejs (pour la compilation des assets)
npm ou yarn
```

### Configuration Serveur
```nginx
# Configuration Nginx recommandée
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
    
    root /var/www/html/public;
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Sécurité
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Cache des assets statiques
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

## 🧪 Phase de Validation Pré-Production

### 1. Tests Complets
```bash
# Exécuter tous les tests automatisés
./scripts/test-competition-integration.sh
./scripts/test-manual-competitions.sh

# Tests de performance
php artisan test --filter=CompetitionTest
php artisan test --filter=PerformanceTest

# Tests de sécurité
php artisan test --filter=SecurityTest
```

### 2. Validation Manuelle
- ✅ **Interface utilisateur** testée sur tous les navigateurs
- ✅ **Workflow FIFA Connect** validé end-to-end
- ✅ **Actions en lot** testées avec de vraies données
- ✅ **Responsivité** validée sur tous les appareils
- ✅ **Autorisations** testées avec tous les rôles

### 3. Tests de Charge
```bash
# Utiliser Apache Bench ou Artillery pour tester la charge
ab -n 1000 -c 10 https://votre-domaine.com/competitions/dashboard

# Tester avec des données volumineuses
php artisan db:seed --class=CompetitionLoadTestSeeder
```

## 🔒 Sécurité et Configuration

### 1. Variables d'Environnement
```env
# .env.production
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=competitions_prod
DB_USERNAME=competitions_user
DB_PASSWORD=password_securise

# Cache et sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Logs
LOG_CHANNEL=daily
LOG_LEVEL=warning

# Sécurité
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict

# FIFA Connect
FIFA_CONNECT_API_KEY=votre_api_key
FIFA_CONNECT_API_SECRET=votre_api_secret
FIFA_CONNECT_ENVIRONMENT=production
```

### 2. Configuration de Sécurité
```php
// config/session.php
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => env('SESSION_HTTP_ONLY', true),
'same_site' => env('SESSION_SAME_SITE', 'strict'),

// config/auth.php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
        'expire' => 120, // 2 heures
    ],
],

// config/cache.php
'default' => env('CACHE_DRIVER', 'redis'),
'stores' => [
    'redis' => [
        'driver' => 'redis',
        'connection' => 'cache',
        'lock_connection' => 'default',
    ],
],
```

### 3. Middlewares de Sécurité
```php
// app/Http/Kernel.php
protected $middleware = [
    \App\Http\Middleware\TrustProxies::class,
    \Illuminate\Http\Middleware\ValidatePostSize::class,
    \App\Http\Middleware\TrimStrings::class,
    \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    \App\Http\Middleware\SecurityHeaders::class, // Custom
];

protected $middlewareGroups = [
    'web' => [
        \App\Http\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \App\Http\Middleware\RateLimiting::class, // Custom
    ],
];
```

## 🚀 Processus de Déploiement

### 1. Préparation du Code
```bash
# Branche de production
git checkout -b production
git merge main

# Vérification finale
composer install --no-dev --optimize-autoloader
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Script de Déploiement
```bash
#!/bin/bash
# deploy.sh

set -e

echo "🚀 Déploiement en production..."

# Variables
PROJECT_DIR="/var/www/html"
BACKUP_DIR="/var/backups/competitions"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Sauvegarde
echo "📦 Sauvegarde de la base de données..."
mysqldump -u $DB_USER -p$DB_PASSWORD $DB_NAME > $BACKUP_DIR/backup_$TIMESTAMP.sql

# Mise en maintenance
echo "🔧 Activation du mode maintenance..."
php artisan down --message="Mise à jour en cours..." --retry=60

# Déploiement du code
echo "📥 Mise à jour du code..."
git pull origin production

# Dépendances
echo "📚 Installation des dépendances..."
composer install --no-dev --optimize-autoloader
npm ci --production

# Compilation des assets
echo "🎨 Compilation des assets..."
npm run build

# Base de données
echo "🗄️  Mise à jour de la base de données..."
php artisan migrate --force

# Cache et optimisation
echo "⚡ Optimisation du cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan queue:restart

# Permissions
echo "🔐 Mise à jour des permissions..."
chown -R www-data:www-data $PROJECT_DIR
chmod -R 755 $PROJECT_DIR/storage
chmod -R 755 $PROJECT_DIR/bootstrap/cache

# Désactivation du mode maintenance
echo "✅ Désactivation du mode maintenance..."
php artisan up

echo "🎉 Déploiement terminé avec succès !"
```

### 3. Déploiement Automatisé (CI/CD)
```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [ production ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mbstring, xml, ctype, iconv, intl, pdo_mysql, gd, curl, zip
        
    - name: Setup Node.js
      uses: actions/setup-node@v3
      with:
        node-version: '18'
        
    - name: Install Dependencies
      run: |
        composer install --no-dev --optimize-autoloader
        npm ci
        
    - name: Build Assets
      run: npm run build
        
    - name: Deploy to Server
      uses: appleboy/ssh-action@v0.1.5
      with:
        host: ${{ secrets.HOST }}
        username: ${{ secrets.USERNAME }}
        key: ${{ secrets.KEY }}
        script: |
          cd /var/www/html
          git pull origin production
          composer install --no-dev --optimize-autoloader
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          sudo systemctl restart php8.1-fpm
          sudo systemctl restart nginx
```

## 📊 Monitoring et Maintenance

### 1. Logs et Surveillance
```php
// config/logging.php
'channels' => [
    'stack' => [
        'driver' => 'stack',
        'channels' => ['daily', 'slack'],
        'ignore_exceptions' => false,
    ],
    'daily' => [
        'driver' => 'daily',
        'path' => storage_path('logs/laravel.log'),
        'level' => env('LOG_LEVEL', 'warning'),
        'days' => 14,
    ],
    'slack' => [
        'driver' => 'slack',
        'url' => env('LOG_SLACK_WEBHOOK_URL'),
        'username' => 'Competitions Bot',
        'emoji' => ':boom:',
        'level' => 'critical',
    ],
    'competition' => [
        'driver' => 'daily',
        'path' => storage_path('logs/competition-YYYY-MM-DD.log'),
        'level' => 'info',
        'days' => 30,
    ],
],
```

### 2. Surveillance des Performances
```bash
# Monitoring avec New Relic ou DataDog
# Configuration dans .env
NEW_RELIC_LICENSE_KEY=votre_licence
NEW_RELIC_APP_NAME="Competitions FIFA Connect"

# Monitoring des logs en temps réel
tail -f storage/logs/competition-$(date +%Y-%m-%d).log

# Surveillance de la base de données
mysql -u root -p -e "SHOW PROCESSLIST;"
mysql -u root -p -e "SHOW STATUS LIKE 'Slow_queries';"
```

### 3. Tâches Cron
```bash
# /etc/crontab
# Nettoyage des logs
0 2 * * * www-data cd /var/www/html && php artisan log:clear

# Sauvegarde automatique
0 3 * * * root /var/www/html/scripts/backup-database.sh

# Vérification de la santé du système
*/5 * * * * www-data cd /var/www/html && php artisan health:check

# Synchronisation FIFA Connect
0 4 * * * www-data cd /var/www/html && php artisan fifa:sync
```

## 🔄 Procédures de Maintenance

### 1. Mise à Jour de Sécurité
```bash
# Vérification des vulnérabilités
composer audit
npm audit

# Mise à jour des dépendances
composer update --with-dependencies
npm update

# Vérification des migrations
php artisan migrate:status
php artisan migrate --force
```

### 2. Sauvegarde et Restauration
```bash
# Script de sauvegarde
#!/bin/bash
# backup-database.sh

DB_NAME="competitions_prod"
BACKUP_DIR="/var/backups/competitions"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Sauvegarde de la base de données
mysqldump -u root -p $DB_NAME > $BACKUP_DIR/backup_$TIMESTAMP.sql

# Compression
gzip $BACKUP_DIR/backup_$TIMESTAMP.sql

# Nettoyage des anciennes sauvegardes (garder 30 jours)
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

# Sauvegarde des fichiers uploadés
tar -czf $BACKUP_DIR/uploads_$TIMESTAMP.tar.gz storage/app/public/

echo "Sauvegarde terminée: backup_$TIMESTAMP.sql.gz"
```

### 3. Gestion des Incidents
```bash
# Script de diagnostic
#!/bin/bash
# diagnose.sh

echo "🔍 Diagnostic du système..."

# Vérification des services
systemctl status nginx
systemctl status php8.1-fpm
systemctl status mysql
systemctl status redis

# Vérification des logs
tail -n 50 storage/logs/laravel.log
tail -n 50 storage/logs/competition-$(date +%Y-%m-%d).log

# Vérification de la base de données
php artisan tinker --execute="echo 'DB Status: ' . (DB::connection()->getPdo() ? 'OK' : 'NOK');"

# Vérification des permissions
ls -la storage/
ls -la bootstrap/cache/

echo "Diagnostic terminé."
```

## 📈 Métriques de Performance

### Objectifs de Performance
- **Temps de réponse** : < 500ms pour 95% des requêtes
- **Disponibilité** : 99.9% uptime
- **Concurrents** : Support de 100+ utilisateurs simultanés
- **Base de données** : < 100ms pour les requêtes critiques

### Outils de Monitoring
- **Application** : New Relic, DataDog, Laravel Telescope
- **Serveur** : Nagios, Zabbix, Prometheus
- **Base de données** : MySQL Workbench, phpMyAdmin
- **Logs** : ELK Stack, Graylog

## 🚨 Procédures d'Urgence

### 1. Rollback Rapide
```bash
# Retour à la version précédente
git checkout HEAD~1
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart php8.1-fpm
```

### 2. Mode Maintenance
```bash
# Activation du mode maintenance
php artisan down --message="Maintenance en cours" --retry=60

# Désactivation
php artisan up
```

### 3. Contact d'Urgence
```
🚨 Équipe de Support
- Email: support@votre-domaine.com
- Téléphone: +33 1 23 45 67 89
- Slack: #competitions-support
- Escalade: #competitions-urgent
```

## ✅ Checklist de Déploiement

### Pré-Déploiement
- [ ] Tests automatisés passent à 100%
- [ ] Tests manuels validés
- [ ] Code review approuvé
- [ ] Base de données sauvegardée
- [ ] Environnement de staging validé

### Déploiement
- [ ] Mode maintenance activé
- [ ] Code déployé
- [ ] Dépendances installées
- [ ] Assets compilés
- [ ] Base de données migrée
- [ ] Cache optimisé
- [ ] Permissions mises à jour
- [ ] Mode maintenance désactivé

### Post-Déploiement
- [ ] Tests de fumée passent
- [ ] Monitoring activé
- [ ] Logs vérifiés
- [ ] Performance validée
- [ ] Équipe notifiée
- [ ] Documentation mise à jour

## 🎯 Critères de Succès

Le déploiement est considéré comme **réussi** si :

1. ✅ **Tous les tests passent** en production
2. ✅ **Performance respecte** les objectifs
3. ✅ **Sécurité validée** et active
4. ✅ **Monitoring fonctionne** correctement
5. ✅ **Équipe utilisateur** formée et opérationnelle
6. ✅ **Documentation** complète et à jour

## 🚀 Prochaines Étapes

Après le déploiement réussi :

1. **Formation des utilisateurs** finaux
2. **Monitoring continu** et optimisation
3. **Collecte de feedback** et améliorations
4. **Planification des évolutions** futures
5. **Maintenance préventive** et mises à jour

---

**Note :** Ce guide doit être adapté selon l'environnement spécifique et les exigences de votre infrastructure de production.






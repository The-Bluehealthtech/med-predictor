# Med Predictor - Production Deployment Guide

This guide provides step-by-step instructions for deploying the Med Predictor application to production with proper security, performance, and reliability configurations.

## 🚀 Prerequisites

### System Requirements

-   **OS**: Ubuntu 20.04+ or CentOS 8+
-   **PHP**: 8.2+ with required extensions
-   **Database**: MySQL 8.0+ or PostgreSQL 13+
-   **Cache**: Redis 6.0+
-   **Web Server**: Nginx 1.18+
-   **SSL Certificate**: Valid SSL certificate for your domain

### Required PHP Extensions

```bash
php8.2-fpm php8.2-mysql php8.2-redis php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl
```

## 📋 Installation Steps

### 1. Server Preparation

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y nginx mysql-server redis-server supervisor git curl unzip

# Install PHP and extensions
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-redis php8.2-curl php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 2. Database Setup

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE med_predictor_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'med_predictor_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON med_predictor_prod.* TO 'med_predictor_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Application Deployment

```bash
# Create application directory
sudo mkdir -p /var/www/med-predictor
sudo chown $USER:$USER /var/www/med-predictor

# Clone repository
cd /var/www/med-predictor
git clone https://github.com/your-org/med-predictor.git .

# Install dependencies
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/med-predictor
sudo chmod -R 755 /var/www/med-predictor
sudo chmod -R 775 /var/www/med-predictor/storage
sudo chmod -R 775 /var/www/med-predictor/bootstrap/cache
```

### 4. Environment Configuration

```bash
# Copy environment file
cp production.env.example .env

# Generate application key
php artisan key:generate

# Edit environment file with your settings
nano .env
```

**Required Environment Variables:**

```env
APP_NAME="Med Predictor"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=med_predictor_prod
DB_USERNAME=med_predictor_user
DB_PASSWORD=secure_password_here

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# FIFA Connect API
FIFA_CONNECT_BASE_URL=https://api.fifa.com/v1
FIFA_CONNECT_API_KEY=your_fifa_api_key_here
FIFA_CONNECT_TIMEOUT=30
FIFA_CONNECT_RATE_LIMIT_DELAY=1
FIFA_CONNECT_CACHE_TTL=3600

# HL7 FHIR API
HL7_FHIR_BASE_URL=https://fhir.your-provider.com
HL7_FHIR_CLIENT_ID=your_fhir_client_id
HL7_FHIR_CLIENT_SECRET=your_fhir_client_secret
HL7_FHIR_TIMEOUT=30
```

### 5. Database Migration

```bash
# Run migrations
php artisan migrate --force

# Seed production data (if needed)
php artisan db:seed --class=ProductionSeeder

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. Web Server Configuration

```bash
# Copy Nginx configuration
sudo cp nginx.conf /etc/nginx/sites-available/med-predictor
sudo ln -s /etc/nginx/sites-available/med-predictor /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### 7. Queue Worker Setup

```bash
# Copy supervisor configuration
sudo cp supervisor.conf /etc/supervisor/conf.d/med-predictor.conf

# Create log directory
sudo mkdir -p /var/log/med-predictor

# Reload supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start med-predictor:*
```

### 8. SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Set up auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

## 🔧 Configuration Files

### Security Configuration

The application includes comprehensive security settings in `config/security.php`:

-   Security headers
-   CORS configuration
-   Rate limiting
-   Session security
-   Password policies
-   Two-factor authentication
-   API security
-   File upload restrictions
-   Audit logging

### Performance Configuration

Performance optimizations are configured in `config/performance.php`:

-   Caching strategies
-   Queue configuration
-   Database optimization
-   API performance
-   Asset optimization
-   Memory management
-   Background jobs
-   Monitoring and metrics

## 🚀 Deployment Script

Use the provided deployment script for future updates:

```bash
# Make script executable
chmod +x deploy-production.sh

# Run deployment
./deploy-production.sh
```

## 📊 Monitoring and Maintenance

### Health Checks

-   Application health: `https://your-domain.com/health`
-   Queue status: `php artisan queue:monitor`
-   Cache status: `php artisan cache:status`

### Log Monitoring

```bash
# Application logs
tail -f /var/www/med-predictor/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/med-predictor.access.log
tail -f /var/log/nginx/med-predictor.error.log

# Queue worker logs
tail -f /var/log/med-predictor/worker.log
```

### Backup Strategy

```bash
# Database backup
mysqldump -u med_predictor_user -p med_predictor_prod > backup_$(date +%Y%m%d_%H%M%S).sql

# Application backup
tar -czf app_backup_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/med-predictor
```

## 🔒 Security Checklist

-   [ ] SSL certificate installed and configured
-   [ ] Firewall configured (UFW)
-   [ ] Security headers implemented
-   [ ] Rate limiting enabled
-   [ ] File upload restrictions configured
-   [ ] Database user has minimal privileges
-   [ ] Application key generated
-   [ ] Debug mode disabled
-   [ ] Error reporting configured for production
-   [ ] Backup strategy implemented
-   [ ] Monitoring and alerting configured

## 🚨 Troubleshooting

### Common Issues

1. **Permission Errors**

    ```bash
    sudo chown -R www-data:www-data /var/www/med-predictor
    sudo chmod -R 755 /var/www/med-predictor
    sudo chmod -R 775 /var/www/med-predictor/storage
    ```

2. **Queue Workers Not Starting**

    ```bash
    sudo supervisorctl status
    sudo supervisorctl restart med-predictor:*
    ```

3. **Database Connection Issues**

    ```bash
    php artisan config:clear
    php artisan cache:clear
    ```

4. **SSL Certificate Issues**
    ```bash
    sudo certbot renew --dry-run
    sudo nginx -t
    sudo systemctl restart nginx
    ```

## 📞 Support

For production support and issues:

-   Check application logs: `/var/www/med-predictor/storage/logs/`
-   Monitor system resources: `htop`, `df -h`, `free -h`
-   Review Nginx error logs: `/var/log/nginx/med-predictor.error.log`
-   Check queue worker status: `sudo supervisorctl status`

## 🔄 Updates and Maintenance

### Regular Maintenance Tasks

-   Weekly: Review logs and performance metrics
-   Monthly: Update system packages and dependencies
-   Quarterly: Review security configurations and SSL certificates
-   Annually: Full security audit and performance review

### Update Process

1. Create backup
2. Pull latest changes
3. Update dependencies
4. Run migrations
5. Clear and rebuild caches
6. Test functionality
7. Monitor for issues

---

**Production deployment completed!** 🎉

Your Med Predictor application is now running in production with:

-   ✅ FIFA Connect API integration
-   ✅ HL7 FHIR API integration
-   ✅ Security optimizations
-   ✅ Performance optimizations
-   ✅ Monitoring and logging
-   ✅ Backup and recovery procedures

#!/bin/bash
set -e

echo "🚀 Starting Med-Predictor Docker Container..."

# Wait for database to be ready
echo "⏳ Waiting for database connection..."
until php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; do
    echo "   Database not ready, waiting..."
    sleep 2
done
echo "✅ Database connection established"

# Run database migrations
echo "🔄 Running database migrations..."
php artisan migrate --force

# Clear and cache configurations
echo "⚙️  Optimizing Laravel..."
php artisan config:clear
php artisan config:cache
php artisan route:clear
php artisan route:cache
php artisan view:clear
php artisan view:cache

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Start supervisor to manage services
echo "🚀 Starting services with Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

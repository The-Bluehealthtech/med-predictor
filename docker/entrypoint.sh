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

# Check if Laravel is properly set up
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env 2>/dev/null || echo "APP_KEY=" > .env
fi

# Generate Laravel key if not exists
if ! grep -q "APP_KEY=base64:" .env; then
    echo "🔑 Generating Laravel application key..."
    php artisan key:generate --force || echo "Warning: Could not generate key"
fi

# Run database migrations
echo "🔄 Running database migrations..."
php artisan migrate --force || echo "Warning: Could not run migrations"

# Clear and cache configurations
echo "⚙️  Optimizing Laravel..."
php artisan config:clear || echo "Warning: Could not clear config"
php artisan config:cache || echo "Warning: Could not cache config"
php artisan route:clear || echo "Warning: Could not clear routes"
php artisan route:cache || echo "Warning: Could not cache routes"
php artisan view:clear || echo "Warning: Could not clear views"
php artisan view:cache || echo "Warning: Could not cache views"

# Set proper permissions
echo "🔐 Setting permissions..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Start supervisor to manage services
echo "🚀 Starting services with Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

#!/bin/sh
set -eu

cd /var/www/html

echo "Preparing canonical FIT data..."

php artisan fit:deploy \
  --days=30 \
  --no-interaction

PORT="${PORT:-10000}"

sed -i "s/Listen 80/Listen ${PORT}/" \
  /etc/apache2/ports.conf

sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" \
  /etc/apache2/sites-available/000-default.conf

exec apache2-foreground

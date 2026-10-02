#!/bin/sh
set -eu
cd /var/www/html
PORT="${PORT:-10000}"
case "$PORT" in
  ''|*[!0-9]*) echo "Invalid PORT"; exit 1 ;;
esac
if [ "$PORT" -lt 1 ] || [ "$PORT" -gt 65535 ]; then
  echo "Invalid PORT"; exit 1
fi
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \\*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf
echo "Clearing compiled Blade views..."
php artisan view:clear
echo "Ensuring public storage link..."
php artisan storage:link --no-interaction || true
echo "Applying required FIT schema..."
# Ne jamais servir l'application avec une migration échouée.
php artisan fit:deploy --schema-only --no-interaction
echo "Starting Apache on port ${PORT}..."
# Les imports et calculs n'empêchent plus l'ouverture du serveur web.
(
  if php artisan fit:deploy --refresh-only --days=30 --demo-data --no-interaction; then
    echo "FIT data refresh completed."
  else
    echo "FIT data refresh failed; existing stored results remain in use." >&2
  fi
) &
exec apache2-foreground

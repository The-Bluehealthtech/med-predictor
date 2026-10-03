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
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

echo "Clearing compiled Blade views..."
php artisan view:clear
echo "Ensuring public storage link..."
php artisan storage:link --no-interaction || true

# Bind Render's port immediately, but keep Laravel unavailable until the
# required schema is confirmed. This avoids Render's port-scan timeout on
# long production migrations while preventing normal traffic on stale schema.
php artisan down --retry=15 --refresh=30 --no-interaction || true

echo "Starting Apache on port ${PORT}..."
apache2-foreground &
APACHE_PID=$!

cleanup() {
  kill -TERM "$APACHE_PID" 2>/dev/null || true
  wait "$APACHE_PID" 2>/dev/null || true
}
trap cleanup INT TERM

sleep 1
if ! kill -0 "$APACHE_PID" 2>/dev/null; then
  echo "Apache failed to start." >&2
  wait "$APACHE_PID"
  exit 1
fi

echo "Applying required FIT schema..."
if ! php artisan fit:deploy --schema-only --no-interaction; then
  echo "FIT schema preparation failed; application remains in maintenance mode." >&2
  cleanup
  exit 1
fi

php artisan up --no-interaction
echo "FIT schema ready; application is accepting traffic."

(
  if php artisan fit:deploy --refresh-only --days=30 --demo-data --no-interaction; then
    echo "FIT data refresh completed."
  else
    echo "FIT data refresh failed; existing stored results remain in use." >&2
  fi
) &

wait "$APACHE_PID"

#!/bin/bash
set -euo pipefail

cd /var/www/html

echo "=== PLAYER PORTAL SEED START ==="

php artisan tinker --execute='
echo "PLAYERS=" . \DB::table("players")->count() . PHP_EOL;
echo "ATHLETES=" . \DB::table("athletes")->count() . PHP_EOL;
echo "HEALTH_BEFORE=" . \DB::table("health_records")->count() . PHP_EOL;
'

php artisan db:seed \
  --class=Database\\Seeders\\PlayerPortalDataSeeder \
  --force

php artisan tinker --execute='
echo "HEALTH_AFTER=" . \DB::table("health_records")->count() . PHP_EOL;
echo "PCMAS=" . \DB::table("pcmas")->count() . PHP_EOL;
echo "DEVICES=" . \DB::table("player_connected_devices")->count() . PHP_EOL;
echo "PERFORMANCES=" . \DB::table("player_performances")->count() . PHP_EOL;
echo "LICENSES=" . \DB::table("player_licenses")->count() . PHP_EOL;
'

echo "=== PLAYER PORTAL SEED DONE ==="
echo "=== STARTING APACHE ==="

exec apache2-foreground

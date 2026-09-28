#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."

printf "External Database URL: "
IFS= read -r -s DATABASE_URL
printf "\n"

if [ -z "$DATABASE_URL" ]; then
    echo "URL absente." >&2
    exit 1
fi

composer dump-autoload --no-interaction

DB_CONNECTION=pgsql DATABASE_URL="$DATABASE_URL" \
php artisan db:seed --class='Database\\Seeders\\SaudiHierarchySeeder' --force

unset DATABASE_URL

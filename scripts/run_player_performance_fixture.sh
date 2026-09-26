#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

DATABASE_URL=''
trap 'unset DATABASE_URL' EXIT
IFS= read -r -s -p "External Database URL: " DATABASE_URL </dev/tty
printf '\n' >/dev/tty
if [[ -z "$DATABASE_URL" ]]; then
    echo "URL absente." >&2
    exit 1
fi

DB_CONNECTION=pgsql DATABASE_URL="$DATABASE_URL" \
    php artisan db:seed \
    --class=Database\\Seeders\\PlayerPerformanceTestFixtureSeeder \
    --force

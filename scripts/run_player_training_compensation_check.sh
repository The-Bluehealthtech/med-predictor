#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
DATABASE_URL=''
trap 'unset DATABASE_URL' EXIT
IFS= read -r -s -p 'External Database URL: ' DATABASE_URL </dev/tty
printf '\n' >/dev/tty
if [[ -z "$DATABASE_URL" ]]; then
    echo 'URL absente.' >&2
    exit 1
fi
DB_CONNECTION=pgsql DATABASE_URL="$DATABASE_URL" \
    php artisan db:seed --class=Database\\Seeders\\PlayerPortalFieldCompletionSeeder --force

set +e
DB_CONNECTION=pgsql DATABASE_URL="$DATABASE_URL" php scripts/audit_player_portal_fields.php
audit_fields_code=$?
DB_CONNECTION=pgsql DATABASE_URL="$DATABASE_URL" php scripts/audit_player_portal_render.php 1
audit_render_code=$?
set -e
if [[ $audit_fields_code -eq 2 || $audit_render_code -eq 2 ]]; then
    exit 2
fi
printf 'Contrôles achevés. Code champs=%d, rendu=%d (1 attendu pour FIT CONNECT absent).\n' \
    "$audit_fields_code" "$audit_render_code"

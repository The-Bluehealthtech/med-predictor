#!/bin/sh
set -eu

cd "$(dirname "$0")/.."

echo "Applying FIT snapshot migrations..."

php artisan migrate \
  --force \
  --no-interaction \
  --path=database/migrations/2026_09_24_160000_create_fit_score_snapshots_table.php

php artisan migrate \
  --force \
  --no-interaction \
  --path=database/migrations/2026_09_24_170000_add_tenant_id_to_fit_score_snapshots_table.php

php artisan migrate \
  --force \
  --no-interaction \
  --path=database/migrations/2026_09_24_171000_add_input_signature_to_fit_score_snapshots_table.php

echo "Generating canonical FIT snapshots..."

php artisan fit:snapshots \
  --days=30 \
  --strict \
  --no-interaction

echo "FIT pre-deploy completed."

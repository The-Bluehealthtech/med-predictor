#!/bin/sh
set -e

# Non-destructive migration sync: mark create_*_table migrations as Ran
# when their corresponding tables already exist in the database.

DB="${DB_DATABASE:-med_predictor}"
USER="${DB_USERNAME:-med_user}"
PASS="${DB_PASSWORD:-med_password}"
HOST="${DB_HOST:-fit-mysql}"

cd /var/www/html

NEW_BATCH=$(mysql --skip-ssl --protocol=TCP -h "$HOST" -u "$USER" -p"$PASS" -N -e "USE $DB; SELECT COALESCE(MAX(batch),0)+1 FROM migrations;")

for p in database/migrations/*create_*_table.php; do
  b=$(basename "$p" .php)
  table=$(echo "$b" | sed -E "s/^.*create_(.*)_table.*$/\1/")
  [ -z "$table" ] && continue

  exists=$(mysql --skip-ssl --protocol=TCP -h "$HOST" -u "$USER" -p"$PASS" -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$DB' AND table_name='${table}'")
  recorded=$(mysql --skip-ssl --protocol=TCP -h "$HOST" -u "$USER" -p"$PASS" -N -e "USE $DB; SELECT COUNT(*) FROM migrations WHERE migration='${b}'")

  if [ "$exists" -gt 0 ] && [ "$recorded" -eq 0 ]; then
    mysql --skip-ssl --protocol=TCP -h "$HOST" -u "$USER" -p"$PASS" -e "USE $DB; INSERT INTO migrations (migration, batch) VALUES ('${b}', ${NEW_BATCH});"
    echo "Marked as Ran: $b ($table)"
  fi
done

echo "Sync complete."



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

# Four readers keep the exhaustive check practical without writing to PostgreSQL.
audit_log_dir=$(mktemp -d "${TMPDIR:-/tmp}/portal-render-audit.XXXXXX")
printf 'Journal de vérification : %s\n' "$audit_log_dir"
audit_pids=()
for audit_worker in 0 1 2 3; do
    audit_offset=$((audit_worker * 211))
    DB_CONNECTION=pgsql DATABASE_URL="$DATABASE_URL" PGCONNECT_TIMEOUT=10 \
        php scripts/audit_player_portal_render.php 211 "$audit_offset" \
        >"$audit_log_dir/worker-$audit_worker.log" 2>&1 &
    audit_pids+=("$!")
done

while [[ $(jobs -rp | wc -l | tr -d ' ') -gt 0 ]]; do
    audit_done=0
    for audit_worker in 0 1 2 3; do
        audit_part=$(grep -c '^Joueur [0-9]' "$audit_log_dir/worker-$audit_worker.log" || true)
        audit_done=$((audit_done + audit_part))
    done
    printf 'Rendus vérifiés : %d/844\n' "$audit_done"
    sleep 30
done

set +e
audit_failed=0
for audit_pid in "${audit_pids[@]}"; do
    wait "$audit_pid"
    audit_code=$?
    if [[ $audit_code -eq 2 ]]; then
        audit_failed=2
    elif [[ $audit_code -ne 0 && $audit_failed -eq 0 ]]; then
        audit_failed=1
    fi
done
set -e

audit_done=0
for audit_worker in 0 1 2 3; do
    audit_part=$(grep -c '^Joueur [0-9]' "$audit_log_dir/worker-$audit_worker.log" || true)
    audit_done=$((audit_done + audit_part))
done
printf 'Total rendus vérifiés : %d/844\n' "$audit_done"
grep -hE '^(AFFICHAGE|ERREUR|Route /performances/analytics|Erreurs de rendu)' "$audit_log_dir"/worker-*.log || true
if [[ $audit_done -ne 844 && $audit_failed -eq 0 ]]; then
    audit_failed=2
fi
exit "$audit_failed"

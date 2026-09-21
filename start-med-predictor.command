#!/bin/bash
set -e
cd "$(dirname "$0")"
PROJECT_DIR="$(pwd)"

echo "=== med-predictor Docker (Option A) ==="
echo "Project: $PROJECT_DIR"

DOCKER_ERR=$(docker info 2>&1) || true
if ! docker info >/dev/null 2>&1; then
  echo ""
  if echo "$DOCKER_ERR" | grep -qi "permission denied"; then
    echo "ERROR: Permission denied on Docker socket."
    echo "Fix: Run this file from Terminal (not from a restricted agent), or:"
    echo "  cd \"$PROJECT_DIR\" && docker compose up -d --build"
  elif echo "$DOCKER_ERR" | grep -qi "Cannot connect\|Is the docker daemon"; then
    echo "ERROR: Docker daemon is not running."
    echo "Fix: Open Docker Desktop, wait until it shows Running, then run this script again."
  else
    echo "ERROR: Cannot connect to Docker."
    echo "$DOCKER_ERR" | tail -5
  fi
  exit 1
fi

echo "Building and starting containers..."
docker compose up -d --build

echo "Waiting for services (up to 120s)..."
for i in $(seq 1 24); do
  if docker compose ps 2>/dev/null | grep -q med-predictor-nginx; then
    sleep 5
    break
  fi
  sleep 5
done

docker compose ps

echo "Running migrations (safe if already applied)..."
docker compose exec -T app php artisan migrate --force 2>/dev/null || true

echo ""
echo "HTTP checks:"
for url in "http://127.0.0.1:8080/" "http://127.0.0.1:80/"; do
  code=$(curl -s -o /dev/null -w "%{http_code}" "$url" 2>/dev/null || echo "000")
  echo "  $url -> HTTP $code"
done

echo ""
echo "Open in browser:"
echo "  App (alt port):  http://127.0.0.1:8080/"
echo "  App (port 80):   http://127.0.0.1:80/"
echo "  Adminer (DB UI): http://127.0.0.1:8081/"
echo "  MailHog:         http://127.0.0.1:8025/"
echo ""
read -p "Press Enter to close this window..."

#!/bin/bash
# Nettoie les anciens assets JS/CSS dans public/build/ qui ne sont plus référencés dans manifest.json

set -e
BUILD_DIR="public/build"
MANIFEST="$BUILD_DIR/manifest.json"

if [ ! -f "$MANIFEST" ]; then
  echo "Manifest introuvable : $MANIFEST"
  exit 1
fi

# Liste tous les fichiers référencés dans le manifest
USED_FILES=$(jq -r 'to_entries[] | .value.file' "$MANIFEST")

# Supprime tous les .js et .css qui ne sont pas dans le manifest
find "$BUILD_DIR" -type f \( -name '*.js' -o -name '*.css' \) | while read -r file; do
  keep=0
  for used in $USED_FILES; do
    if [[ "$file" == "$BUILD_DIR/$used" ]]; then
      keep=1
      break
    fi
  done
  if [ $keep -eq 0 ]; then
    echo "Suppression: $file"
    rm -f "$file"
  fi
done

echo "Nettoyage terminé. Seuls les assets référencés dans manifest.json sont conservés." 
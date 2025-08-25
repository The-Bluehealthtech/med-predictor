#!/bin/bash
# Script d'automigration i18n pour tous les fichiers .vue du projet
# - Remplace les textes statiques par $t('auto.KEY')
# - Ajoute les clés dans auto_i18n_fr.json et auto_i18n_en.json
# - Sauvegarde les fichiers originaux en .bak

set -e

FR_JSON=auto_i18n_fr.json
EN_JSON=auto_i18n_en.json
LOG=auto_i18n_migration.log
> "$FR_JSON"
> "$EN_JSON"
> "$LOG"
echo '{' > "$FR_JSON"
echo '{' > "$EN_JSON"

key_id=1

find resources/js -name '*.vue' | while read -r file; do
  cp "$file" "$file.bak"
  awk '/<template>/,/<\/template>/' "$file" | \
    grep -vE '(^\s*<|^\s*$|\$t\(|v-text|v-html|:[a-zA-Z]+|{{ *\$t|<!--|@|^\s*\})' | \
    grep -E '[a-zA-ZÀ-ÿ]' | \
    while read -r line; do
      clean=$(echo "$line" | sed 's/^\s*//;s/\s*$//')
      if [[ "$clean" != "" ]]; then
        key="auto.key$key_id"
        # Ajout dans les fichiers de traduction
        echo "  \"$key\": \"$clean\"," >> "$FR_JSON"
        echo "  \"$key\": \"$clean\"," >> "$EN_JSON"
        # Remplacement dans le fichier
        sed -i '' "s|$clean|{{ \$t('$key') }}|g" "$file"
        echo "$file: $clean -> $key" >> "$LOG"
        key_id=$((key_id+1))
      fi
    done
done
echo '  "_end": "end"' >> "$FR_JSON"
echo '  "_end": "end"' >> "$EN_JSON"
echo '}' >> "$FR_JSON"
echo '}' >> "$EN_JSON"
echo "Migration terminée. Voir $LOG pour le détail." 
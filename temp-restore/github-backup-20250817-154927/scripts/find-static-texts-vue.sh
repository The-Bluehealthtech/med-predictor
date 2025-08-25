#!/bin/bash
# Script pour détecter les textes statiques à migrer vers i18n dans les fichiers .vue

find resources/js -name '*.vue' | while read -r file; do
  echo "\n--- $file ---"
  # On extrait les lignes du template qui contiennent potentiellement du texte statique
  awk '/<template>/,/<\/template>/' "$file" |
    grep -vE '(^\s*<|^\s*$|\$t\(|v-text|v-html|:[a-zA-Z]+|{{ *\$t|<!--|@|^\s*\})' |
    grep -E '[a-zA-ZÀ-ÿ]' |
    nl -ba
done 
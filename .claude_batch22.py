# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("voiceStatus.textContent = ' Recherche en cours...';",
     "voiceStatus.textContent = PCMA_LABELS.searchingInProgress;", 1),
    ("throw new Error(`Erreur API: ${searchResponse.status}`);",
     "throw new Error(PCMA_LABELS.errApi + searchResponse.status);", 1),
    ("throw new Error('Joueur non trouvé dans la base de données');",
     "throw new Error(PCMA_LABELS.errPlayerNotFoundDb);", 1),
    ("voiceStatus.textContent = ' Joueur trouvé ! Données remplies automatiquement';",
     "voiceStatus.textContent = PCMA_LABELS.playerFoundDataFilled;", 1),
    ("displayPlayerNotFound('Erreur lors de la recherche automatique');",
     "displayPlayerNotFound(PCMA_LABELS.errAutoSearch);", 1),
    ("                    field: 'âge',",
     "                    field: PCMA_LABELS.fieldAge,", 1),
    ("                                     Vocal: <strong>${inconsistency.voice}</strong> | \n                                    💾 Base: <strong>${inconsistency.database}</strong>${similarityText}",
     "                                    ${PCMA_LABELS.vocalPrefix}<strong>${inconsistency.voice}</strong> | \n                                    ${PCMA_LABELS.databasePrefix}<strong>${inconsistency.database}</strong>${similarityText}", 1),
    ("                                    ⚠️ Incohérence\n",
     "                                    ${PCMA_LABELS.inconsistencyBadge}\n", 1),
    ("alert('⚠️ Veuillez remplir au moins un champ de confirmation');",
     "alert(PCMA_LABELS.errFillConfirmationField);", 1),
    ("voiceStatus.textContent = ' Identité confirmée - Données validées';",
     "voiceStatus.textContent = PCMA_LABELS.identityConfirmedValidated;", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:150]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH22 OK", len(replacements), "replacements applied")

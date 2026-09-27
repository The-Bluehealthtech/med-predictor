# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("elements.status.textContent = '❌ Erreur: ' + error.message;",
     "elements.status.textContent = PCMA_LABELS.errGeneric + error.message;", 3),
    ("throw new Error('Échec du démarrage de la reconnaissance');",
     "throw new Error(PCMA_LABELS.errStartRecognitionFailed);", 1),
    ("elements.status.textContent = ' Reconnaissance arrêtée';",
     "elements.status.textContent = PCMA_LABELS.recognitionStopped;", 1),
    ("elements.status.textContent = ' Commande analysée et formulaires remplis !';",
     "elements.status.textContent = PCMA_LABELS.commandAnalyzedFormsFilled;", 1),
    ("elements.status.textContent = ' Données appliquées au formulaire !';",
     "elements.status.textContent = PCMA_LABELS.dataAppliedToForm;", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH21 OK", len(replacements), "replacements applied")

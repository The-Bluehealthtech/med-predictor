# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("alert('Renseignez les informations obligatoires avant de produire le PDF.');",
     "alert(@json(__('pcma.err_pdf_required_fields')));", 1),
    ("alert('❌ Erreur lors de la génération du PDF: ' + error.message);",
     "alert(@json(__('pcma.err_pdf_generation')) + error.message);", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH15 OK", len(replacements), "replacements applied")

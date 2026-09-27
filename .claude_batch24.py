# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('<p><strong>Âge:</strong> ${player.age || \'N/A\'}</p>',
     "<p><strong>${PCMA_LABELS.summaryAgeLabel}</strong> ${player.age || 'N/A'}</p>", 1),
    ('                             Trouvé dans la base\n',
     "                             ${PCMA_LABELS.foundInDatabase}\n", 1),
    ('<h4 class="text-lg font-semibold text-yellow-900">Joueur non trouvé</h4>',
     "<h4 class=\"text-lg font-semibold text-yellow-900\">${PCMA_LABELS.playerNotFoundTitle}</h4>", 1),
    ("<p><strong>Nom recherché:</strong> ${playerName}</strong></p>",
     "<p><strong>${PCMA_LABELS.searchedNameLabel}</strong> ${playerName}</strong></p>", 1),
    ("<p><strong>Statut:</strong> Ce joueur n'existe pas encore dans la base de données</p>",
     "<p><strong>${PCMA_LABELS.statusColonLabel}</strong> ${PCMA_LABELS.playerNotExistYet}</p>", 1),
    ('                            ⚠️ Nouveau joueur\n',
     "                            ${PCMA_LABELS.newPlayerBadge}\n", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:150]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH24 OK", len(replacements), "replacements applied")

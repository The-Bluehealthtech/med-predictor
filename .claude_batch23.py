# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("elements.confidence.textContent = `Confiance: ${(confidence * 100).toFixed(1)}%`;",
     "elements.confidence.textContent = `${PCMA_LABELS.confidenceLabel} ${(confidence * 100).toFixed(1)}%`;", 1),
    ("const timestamp = new Date().toLocaleString('fr-FR');",
     "const timestamp = new Date().toLocaleString('en-GB');", 1),
    ("let note = `[${timestamp}] Données extraites par reconnaissance vocale:\\n\\n`;",
     "let note = `[${timestamp}] ${PCMA_LABELS.voiceExtractionNoteHeader}\\n\\n`;", 1),
    ("if (extractedData.age) note += `📅 ÂGE: ${extractedData.age} ans\\n`;",
     "if (extractedData.age) note += `${PCMA_LABELS.ageNotePrefix}${extractedData.age}${PCMA_LABELS.ageNoteSuffix}`;", 1),
    ("if (extractedData.command) note += ` COMMANDE: ${extractedData.command}\\n`;",
     "if (extractedData.command) note += `${PCMA_LABELS.commandNotePrefix}${extractedData.command}\\n`;", 1),
    ("showSaveStatus(' Données sauvegardées automatiquement !', 'success');",
     "showSaveStatus(PCMA_LABELS.savedAutomaticallySuccess, 'success');", 1),
    ("throw new Error(data.message || 'Erreur de sauvegarde');",
     "throw new Error(data.message || PCMA_LABELS.errSave);", 1),
    ("showSaveStatus('❌ Erreur de sauvegarde: ' + error.message, 'error');",
     "showSaveStatus(PCMA_LABELS.errSaveAlert + error.message, 'error');", 2),
    ('<h4 class="font-medium text-blue-800 mb-2"> Résumé de l\'Extraction Vocale</h4>',
     "<h4 class=\"font-medium text-blue-800 mb-2\">${PCMA_LABELS.voiceExtractionSummaryTitle}</h4>", 1),
    ("${extractedData.player_name ? `<div><strong>Nom:</strong> ${extractedData.player_name}</div>` : ''}",
     "${extractedData.player_name ? `<div><strong>${PCMA_LABELS.summaryNameLabel}</strong> ${extractedData.player_name}</div>` : ''}", 1),
    ("${extractedData.age ? `<div><strong>Âge:</strong> ${extractedData.age} ans</div>` : ''}",
     "${extractedData.age ? `<div><strong>${PCMA_LABELS.summaryAgeLabel}</strong> ${extractedData.age} ans</div>` : ''}", 1),
    ("<strong>Confiance:</strong> ${extractedData.confidence === 'high' ? 'Élevée' : extractedData.confidence === 'medium' ? 'Moyenne' : 'Faible'}",
     "<strong>${PCMA_LABELS.confidenceLabel}</strong> ${extractedData.confidence === 'high' ? PCMA_LABELS.confidenceHigh : extractedData.confidence === 'medium' ? PCMA_LABELS.confidenceMedium : PCMA_LABELS.confidenceLow}", 1),
    ("elements.status.textContent = ` PCMA sauvegardé avec l'ID: ${data.pcma_id || 'N/A'}`;",
     "elements.status.textContent = `${PCMA_LABELS.pcmaSavedWithId}${data.pcma_id || 'N/A'}`;", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:150]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH23 OK", len(replacements), "replacements applied")

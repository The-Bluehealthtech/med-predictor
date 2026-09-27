# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("liveStatus.textContent = 'Transcription reçue';",
     "liveStatus.textContent = PCMA_LABELS.transcriptionReceived;", 1),
    ("voiceStatus.textContent = '❌ Service Google vocal non initialisé';",
     "voiceStatus.textContent = PCMA_LABELS.serviceGoogleNotInitialized;", 2),
    ("voiceStatus.textContent = `❌ Erreur Google: ${error.message}`;",
     "voiceStatus.textContent = PCMA_LABELS.errGooglePrefix + error.message;", 1),
    ("voiceStatus.textContent = '⏸️ Enregistrement Google arrêté';",
     "voiceStatus.textContent = PCMA_LABELS.recordingStoppedGoogle;", 1),
    ("voiceStatus.textContent = `❌ Erreur arrêt: ${error.message}`;",
     "voiceStatus.textContent = PCMA_LABELS.errStopPrefix + error.message;", 1),
    ("voiceStatus.textContent = ' Données du joueur remplies automatiquement';",
     "voiceStatus.textContent = PCMA_LABELS.playerDataFilledAuto;", 1),
    ("voiceStatus.textContent = ' Données extraites avec succès !';",
     "voiceStatus.textContent = PCMA_LABELS.dataExtractedSuccess;", 1),
    ("voiceStatus.textContent = ' Données traitées par ServiceVocal + NLP !';",
     "voiceStatus.textContent = PCMA_LABELS.dataProcessedServiceVocalNlp;", 1),
    ("updateVoiceLiveStatus('⏸️ Reconnaissance vocale arrêtée', 'bg-orange-100 text-orange-700');",
     "updateVoiceLiveStatus(PCMA_LABELS.recognitionStoppedGeneric, 'bg-orange-100 text-orange-700');", 1),
    ("voiceStatus.textContent = '⏸️ Reconnaissance vocale arrêtée';",
     "voiceStatus.textContent = PCMA_LABELS.recognitionStoppedGeneric;", 1),
    ("voiceStatus.textContent = ' Données traitées par intégration directe !';",
     "voiceStatus.textContent = PCMA_LABELS.dataProcessedDirectIntegration;", 1),
    ("nlpText = ` Commande FIFA CONNECT détectée${extractedData.fifa_number ? ` (Numéro: ${extractedData.fifa_number})` : ''}`;",
     "nlpText = `${PCMA_LABELS.fifaConnectCommandDetected}${extractedData.fifa_number ? `${PCMA_LABELS.numberLabelPrefix}${extractedData.fifa_number})` : ''}`;", 1),
    ("nlpText = 'Aucune donnée structurée détectée';",
     "nlpText = PCMA_LABELS.noStructuredDataDetected;", 1),
    ('finalElement.innerHTML = \'<span class="text-gray-400">Aucune transcription finale</span>\';',
     "finalElement.innerHTML = `<span class=\"text-gray-400\">${PCMA_LABELS.noFinalTranscript}</span>`;", 1),
    ('dataElement.innerHTML = \'<span class="text-gray-400">Aucune donnée extraite</span>\';',
     "dataElement.innerHTML = `<span class=\"text-gray-400\">${PCMA_LABELS.noDataExtracted}</span>`;", 1),
    ("voiceStatus.textContent = 'Enregistrement Google Cloud en cours... (60s max)';",
     "voiceStatus.textContent = PCMA_LABELS.googleRecordingInProgress;", 1),
    ("throw new Error('Clé API non trouvée dans la réponse');",
     "throw new Error(PCMA_LABELS.errApiKeyNotFound);", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:150]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH25 OK", len(replacements), "replacements applied")

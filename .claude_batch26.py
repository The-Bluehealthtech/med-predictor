# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("voiceStatus.textContent = 'Test avancé en cours...';",
     "voiceStatus.textContent = PCMA_LABELS.testAdvancedProgress;", 1),
    ("voiceStatus.textContent = 'API Speech Recognition non supportée';",
     "voiceStatus.textContent = PCMA_LABELS.speechRecognitionUnsupported;", 1),
    ("voiceStatus.textContent = 'Test: Reconnaissance démarrée !';",
     "voiceStatus.textContent = PCMA_LABELS.testRecognitionStarted;", 1),
    ("voiceStatus.textContent = `Test: Erreur ${event.error}`;",
     "voiceStatus.textContent = PCMA_LABELS.testErrorPrefix + event.error;", 1),
    ("voiceStatus.textContent = `Test: Erreur de création - ${error.message}`;",
     "voiceStatus.textContent = PCMA_LABELS.testCreationErrorPrefix + error.message;", 1),
    ("error: 'API Speech Recognition non supportée dans ce navigateur'",
     "error: PCMA_LABELS.speechRecognitionUnsupportedBrowser", 1),
    ('error: \'Permission microphone refusée - Cliquez sur "Autoriser"\'',
     "error: PCMA_LABELS.micPermissionDenied", 1),
    ("error: 'Aucun microphone détecté sur cet appareil'",
     "error: PCMA_LABELS.noMicrophoneDetected", 1),
    ("error: `Erreur microphone: ${error.message}`",
     "error: PCMA_LABELS.micErrorPrefix + error.message", 1),
    ("voiceStatus.textContent = 'Reconnaissance arrêtée';",
     "voiceStatus.textContent = PCMA_LABELS.recognitionStoppedPlain;", 1),
    ("voiceStatus.textContent = `Écoute en cours... (${remainingTime}s restantes)`;",
     "voiceStatus.textContent = `${PCMA_LABELS.listeningPrefix}${remainingTime}${PCMA_LABELS.listeningSuffix}`;", 1),
    ("indicator.textContent = `Mode ${mode.charAt(0).toUpperCase() + mode.slice(1)} Actif`;",
     "indicator.textContent = `${mode.charAt(0).toUpperCase() + mode.slice(1)} Mode ${PCMA_LABELS.modeActiveWord}`;", 1),
    ('"le transfert automatique vers le formulaire principal n\'est pas encore implémenté ; veuillez recopier les informations manuellement"',
     "PCMA_LABELS.transferNotImplemented", 3),
    ("const message = `Données ${mode} transférées avec succès vers le formulaire principal !`;",
     "const message = PCMA_LABELS.transferSuccessTemplate.replace(':mode', mode);", 1),
    ("const message = `Erreur lors du transfert ${mode}: ${errorMessage}`;",
     "const message = PCMA_LABELS.transferErrorTemplate.replace(':mode', mode).replace(':error', errorMessage);", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:150]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH26 OK", len(replacements), "replacements applied")

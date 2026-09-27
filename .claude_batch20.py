# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("showServiceStatus('🔄 Chargement de la clé API...', 'info');",
     "showServiceStatus(PCMA_LABELS.loadingApiKey, 'info');", 1),
    ("showServiceStatus(' Clé API chargée automatiquement !', 'success');",
     "showServiceStatus(PCMA_LABELS.apiKeyLoadedAuto, 'success');", 1),
    ("throw new Error('Format de réponse invalide');",
     "throw new Error(PCMA_LABELS.errInvalidResponseFormat);", 1),
    ("showServiceStatus('❌ Erreur chargement clé: ' + error.message, 'error');",
     "showServiceStatus(PCMA_LABELS.errLoadingKey + error.message, 'error');", 1),
    ("throw new Error('Clé API manquante');",
     "throw new Error(PCMA_LABELS.errMissingApiKey);", 1),
    ("showServiceStatus(' Service initialisé avec succès !', 'success');",
     "showServiceStatus(PCMA_LABELS.serviceInitializedSuccess, 'success');", 1),
    ("elements.status.textContent = 'Service prêt - Cliquez pour commencer';",
     "elements.status.textContent = PCMA_LABELS.serviceReadyClick;", 1),
    ("throw new Error('Échec de l\\'initialisation');",
     "throw new Error(PCMA_LABELS.errInitFailed);", 1),
    ("showServiceStatus('❌ Erreur initialisation: ' + error.message, 'error');",
     "showServiceStatus(PCMA_LABELS.errInitError + error.message, 'error');", 1),
    ("showServiceStatus(' Test réussi - Service opérationnel !', 'success');",
     "showServiceStatus(PCMA_LABELS.testSuccessOperational, 'success');", 1),
    ("throw new Error('Test échoué');",
     "throw new Error(PCMA_LABELS.errTestFailed);", 1),
    ("showServiceStatus('❌ Test échoué: ' + error.message, 'error');",
     "showServiceStatus(PCMA_LABELS.errTestFailedAlert + error.message, 'error');", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH20 OK", len(replacements), "replacements applied")

# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("    analysisPending: @json(__('pcma.analysis_pending')),\n",
     "    analysisPending: @json(__('pcma.analysis_pending')),\n"
     "    recognitionInProgress: @json(__('pcma.recognition_in_progress')),\n"
     "    autoSearchInProgress: @json(__('pcma.auto_search_in_progress')),\n"
     "    recordingGoogleInProgress: @json(__('pcma.recording_google_in_progress')),\n"
     "    voiceRecognitionInProgress: @json(__('pcma.voice_recognition_in_progress')),\n"
     "    testInProgress: @json(__('pcma.test_in_progress')),\n", 1),

    ("                elements.status.textContent = ' Reconnaissance en cours...';\n",
     "                elements.status.textContent = PCMA_LABELS.recognitionInProgress;\n", 1),

    ("                        voiceStatus.textContent = ' Recherche automatique en cours...';\n",
     "                        voiceStatus.textContent = PCMA_LABELS.autoSearchInProgress;\n", 1),

    ("        voiceStatus.textContent = ' Enregistrement Google en cours...';\n",
     "        voiceStatus.textContent = PCMA_LABELS.recordingGoogleInProgress;\n", 1),

    ("            updateVoiceLiveStatus(' Reconnaissance vocale en cours...', 'bg-blue-100 text-blue-700');\n",
     "            updateVoiceLiveStatus(PCMA_LABELS.voiceRecognitionInProgress, 'bg-blue-100 text-blue-700');\n", 1),

    ("                voiceStatus.textContent = ' Reconnaissance vocale en cours...';\n",
     "                voiceStatus.textContent = PCMA_LABELS.voiceRecognitionInProgress;\n", 1),

    ("            serviceStatus.textContent = 'Test en cours...';\n",
     "            serviceStatus.textContent = PCMA_LABELS.testInProgress;\n", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH30 OK", len(replacements), "replacements applied")

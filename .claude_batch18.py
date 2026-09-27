# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("alert(' Signature médicale validée!\\n\\nAssessment ID: ' + signedData.assessmentId + '\\nSigned by: ' + signedData.signedBy + '\\nTimestamp: ' + signedData.signedAt);",
     "alert(@json(__('pcma.signature_validated_alert')) + '\\n\\nAssessment ID: ' + signedData.assessmentId + '\\nSigned by: ' + signedData.signedBy + '\\nTimestamp: ' + signedData.signedAt);", 1),
    ('                         Signature Validée\n',
     "                         {{ __('pcma.signature_validated_label') }}\n", 1),
    ('<h6 class="font-semibold text-green-900 mb-2">Décision Globale</h6>',
     "<h6 class=\"font-semibold text-green-900 mb-2\">{{ __('pcma.overall_decision_label') }}</h6>", 1),
    ('<h6 class="font-semibold text-blue-900 mb-1">Score Cardiovasculaire</h6>',
     "<h6 class=\"font-semibold text-blue-900 mb-1\">{{ __('pcma.cardio_score_label') }}</h6>", 1),
    ('<h6 class="font-semibold text-orange-900 mb-1">Score Musculo-squelettique</h6>',
     "<h6 class=\"font-semibold text-orange-900 mb-1\">{{ __('pcma.msk_score_label') }}</h6>", 1),
    ('<h6 class="font-semibold text-purple-900 mb-1">Score Neurologique</h6>',
     "<h6 class=\"font-semibold text-purple-900 mb-1\">{{ __('pcma.neuro_score_label') }}</h6>", 1),
    ('<h6 class="font-semibold text-gray-900 mb-2">Résumé Exécutif</h6>',
     "<h6 class=\"font-semibold text-gray-900 mb-2\">{{ __('pcma.executive_summary_label') }}</h6>", 1),
    ("throw new Error(data.message || 'Erreur lors de la génération du rapport');",
     "throw new Error(data.message || @json(__('pcma.err_fitness_report')));", 1),
    ("alert('❌ Erreur lors de la génération du rapport fitness: ' + error.message);",
     "alert(@json(__('pcma.err_fitness_report_alert')) + error.message);", 1),
    ('            🤖 Générer Rapport Fitness Professionnel\n',
     "            {{ __('pcma.generate_fitness_report_btn') }}\n", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH18 OK", len(replacements), "replacements applied")

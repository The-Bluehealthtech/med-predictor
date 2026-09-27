# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    # 1. add similarityLabel to PCMA_LABELS object (right after summaryAgeLabel entry, reusing existing anchor)
    ("    summaryAgeLabel: @json(__('pcma.summary_age_label')),\n",
     "    summaryAgeLabel: @json(__('pcma.summary_age_label')),\n    similarityLabel: @json(__('pcma.similarity_label')),\n", 1),

    # 2. fitness assessment title reuse
    ('                    <h3>⚽ Évaluation Fitness Football Professionnel</h3>\n',
     "                    <h3>{{ __('pcma.fitness_assessment_title') }}</h3>\n", 1),

    # 3. similarityText line
    ('                        similarityText = ` (Similarité: ${Math.round(inconsistency.similarity.score * 100)}%)`;\n',
     '                        similarityText = ` (${PCMA_LABELS.similarityLabel} ${Math.round(inconsistency.similarity.score * 100)}%)`;\n', 1),

    # 4. Nom: / Âge: in updateNLPAnalysis parts.push
    ('                if (extractedData.player_name) parts.push(`Nom: ${extractedData.player_name}`);\n',
     '                if (extractedData.player_name) parts.push(`${PCMA_LABELS.summaryNameLabel} ${extractedData.player_name}`);\n', 1),

    ('                if (extractedData.age) parts.push(`Âge: ${extractedData.age}`);\n',
     '                if (extractedData.age) parts.push(`${PCMA_LABELS.summaryAgeLabel} ${extractedData.age}`);\n', 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH27 OK", len(replacements), "replacements applied")

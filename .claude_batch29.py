# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("    errGenericPlain: @json(__('pcma.err_generic_prefix_plain')),\n",
     "    errGenericPlain: @json(__('pcma.err_generic_prefix_plain')),\n"
     "    analysisInProgress: @json(__('pcma.analysis_in_progress')),\n"
     "    analysisPending: @json(__('pcma.analysis_pending')),\n", 1),

    ("                nlpAnalysis.textContent = 'Analyse en cours...';\n",
     "                nlpAnalysis.textContent = PCMA_LABELS.analysisInProgress;\n", 1),

    ("            nlpElement.innerHTML = '<span class=\"text-gray-400\">Analyse en attente...</span>';\n",
     "            nlpElement.innerHTML = `<span class=\"text-gray-400\">${PCMA_LABELS.analysisPending}</span>`;\n", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH29 OK", len(replacements), "replacements applied")

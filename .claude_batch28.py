# -*- coding: utf-8 -*-
import sys, re
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

# --- exact count-verified literal replacements first ---
replacements = [
    # add new PCMA_LABELS properties (anchored after similarityLabel entry we just added)
    ("    similarityLabel: @json(__('pcma.similarity_label')),\n",
     "    similarityLabel: @json(__('pcma.similarity_label')),\n"
     "    signoffTeamdoctorUnavailable: @json(__('pcma.signoff_teamdoctor_unavailable')),\n"
     "    errGenericPlain: @json(__('pcma.err_generic_prefix_plain')),\n", 1),

    ("document.getElementById('signoff-license-number').textContent = signoffData.doctorFifaId || 'Inscription TeamDoctor indisponible';\n",
     "document.getElementById('signoff-license-number').textContent = signoffData.doctorFifaId || PCMA_LABELS.signoffTeamdoctorUnavailable;\n", 1),

    ('throw new Error(`Erreur serveur: ${response.status}`);\n',
     'throw new Error(`${PCMA_LABELS.errServer}${response.status}`);\n', 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

# --- regex-based replacements (indentation varies) ---
regex_subs = [
    (r'voiceStatus\.textContent = `❌ Erreur: \$\{error\.message\}`;', 8,
     'voiceStatus.textContent = `${PCMA_LABELS.errGeneric}${error.message}`;'),
    (r'voiceStatus\.textContent = `Erreur: \$\{error\.message\}`;', 2,
     'voiceStatus.textContent = `${PCMA_LABELS.errGenericPlain}${error.message}`;'),
    (r'throw new Error\(`Erreur HTTP: \$\{response\.status\}`\);', 2,
     'throw new Error(`${PCMA_LABELS.errHttp}${response.status}`);'),
]

for pattern, expected_count, replacement in regex_subs:
    matches = re.findall(pattern, content)
    if len(matches) != expected_count:
        print(f"MISMATCH regex expected={expected_count} actual={len(matches)} for pattern: {pattern!r}")
        sys.exit(1)

for pattern, expected_count, replacement in regex_subs:
    content, n = re.subn(pattern, replacement, content)
    assert n == expected_count

open(path, 'w', encoding='utf-8').write(content)
print("BATCH28 OK", len(replacements) + len(regex_subs), "replacements applied")

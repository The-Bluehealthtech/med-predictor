# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('                            Transférer vers le formulaire PCMA\n',
     "                            {{ __('pcma.transfer_to_form_btn') }}\n", 1),
    ("                                 Analyser avec l'IA\n",
     "                                 {{ __('pcma.analyze_ai_btn') }}\n", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

# Handle the 4 remaining bare "Effacer" button labels (verified via grep: only these
# 4 non-comment occurrences remain in the file at this point).
import re
matches = list(re.finditer(r'\n( *)Effacer\n', content))
if len(matches) != 4:
    print(f"MISMATCH: expected 4 bare Effacer lines, found {len(matches)}")
    sys.exit(1)
content = re.sub(r'\n( *)Effacer\n', lambda m: f"\n{m.group(1)}{{{{ __('pcma.clear_btn') }}}}\n", content)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH14 OK")

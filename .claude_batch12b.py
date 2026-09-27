# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ('💾 Enregistrer', "{{ __('pcma.save_btn') }}", 2),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:120]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH12b OK", len(replacements), "replacements applied")

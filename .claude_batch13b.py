# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

replacements = [
    ("<span class=\"ml-2 text-gray-900\" id=\"signoff-license-number\">{{ $teamDoctorRegistration?->person_fifa_id ?? 'Inscription TeamDoctor indisponible' }}</span>",
     "<span class=\"ml-2 text-gray-900\" id=\"signoff-license-number\">{{ $teamDoctorRegistration?->person_fifa_id ?? __('pcma.teamdoctor_registration_unavailable') }}</span>", 1),
]

for old, new, expected in replacements:
    actual = content.count(old)
    if actual != expected:
        print(f"MISMATCH expected={expected} actual={actual} for: {old[:150]!r}")
        sys.exit(1)

for old, new, expected in replacements:
    content = content.replace(old, new, expected)

open(path, 'w', encoding='utf-8').write(content)
print("BATCH13b OK", len(replacements), "replacements applied")

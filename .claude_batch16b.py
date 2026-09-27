# -*- coding: utf-8 -*-
import sys
path = 'resources/views/pcma/create.blade.php'
content = open(path, encoding='utf-8').read()

old = '\'<p style="color: #666; font-style: italic;">Signature numérique capturée</p>\''
new = '@json(\'<p style="color: #666; font-style: italic;">\' . __(\'pcma.report_signature_captured\') . \'</p>\')'

actual = content.count(old)
if actual != 1:
    print(f"MISMATCH expected=1 actual={actual}")
    sys.exit(1)
content = content.replace(old, new, 1)
open(path, 'w', encoding='utf-8').write(content)
print("BATCH16b OK")

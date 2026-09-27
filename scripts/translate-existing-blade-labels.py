#!/usr/bin/env python3
"""Reuse unambiguous existing FR/EN translations for static Blade text."""
import json
import pathlib
import re
import subprocess
from collections import defaultdict

ROOT = pathlib.Path(__file__).resolve().parents[1]
VIEWS = ROOT / 'resources/views'
php = '''$a=[];foreach(glob("resources/lang/fr/*.php") as $f){$e="resources/lang/en/".basename($f);if(is_file($e)){$a[basename($f,".php")]=[require $f,require $e];}}echo json_encode($a,JSON_UNESCAPED_UNICODE);'''
pairs = json.loads(subprocess.check_output(['php', '-r', php], cwd=ROOT))
candidates = defaultdict(list)


def collect(fr, en, prefix):
    for key, value in fr.items():
        english = en.get(key)
        path = f'{prefix}.{key}'
        if isinstance(value, dict) and isinstance(english, dict):
            collect(value, english, path)
        elif isinstance(value, str) and isinstance(english, str) and value != english:
            candidates[value].append((english, path))


for group, (fr, en) in pairs.items():
    collect(fr, en, group)
mapping = {}
for french, options in candidates.items():
    if len({english for english, _ in options}) == 1:
        mapping[french] = options[0][1]
for french, english in json.loads((ROOT / 'resources/lang/en.json').read_text()).items():
    if isinstance(english, str) and french != english and french not in mapping:
        mapping[french] = french


blocked = re.compile(r'(?is)(<script\\b.*?</script>|<style\\b.*?</style>|<!--.*?-->|@php\\b.*?@endphp)')
text_node = re.compile(r'(?s)>([^<>]+)<')
attribute = re.compile(r'(?i)\\b(placeholder|title|aria-label|alt)=(["\\\'])(.*?)\\2')
counts = defaultdict(int)


def translate(match, filename, attribute_name=None):
    raw = match.group(1 if attribute_name is None else 3)
    value = raw.strip()
    if not value or any(marker in value for marker in ('{{', '}}', '@', '<?', '?>')):
        return match.group(0)
    key = mapping.get(value)
    if key is None:
        return match.group(0)
    expression = "{{ __('" + key.replace("'", "\\'") + "') }}"
    counts[filename] += 1
    if attribute_name is None:
        return '>' + raw.replace(value, expression, 1) + '<'
    return match.group(1) + '=' + match.group(2) + expression + match.group(2)


for file in sorted(VIEWS.rglob('*.blade.php')):
    original = file.read_text()
    pieces = blocked.split(original)
    for index in range(0, len(pieces), 2):
        part = pieces[index]
        part = text_node.sub(lambda match: translate(match, str(file.relative_to(ROOT))), part)
        part = attribute.sub(lambda match: translate(match, str(file.relative_to(ROOT)), match.group(1)), part)
        pieces[index] = part
    revised = ''.join(pieces)
    if revised != original:
        file.write_text(revised)
print('Replacements:', sum(counts.values()), 'views:', len(counts))

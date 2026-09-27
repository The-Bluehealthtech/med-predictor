import re, sys, json

def extract(path):
    src = open(path, encoding='utf-8').read()
    results = []
    # text nodes between tags
    for m in re.finditer(r'>([^<>{}\n][^<>{}]{1,200})<', src):
        t = m.group(1)
        if re.search(r'[éèêàçùôâîïûüÉÈÀÇÙÔÂÎÏÛÜ]', t) and t.strip():
            results.append(('text', t.strip()))
    # attribute values
    for m in re.finditer(r'(placeholder|title|aria-label|alt|value)="([^"]{1,200})"', src):
        t = m.group(2)
        if re.search(r'[éèêàçùôâîïûüÉÈÀÇÙÔÂÎÏÛÜ]', t):
            results.append((m.group(1), t.strip()))
    # JS string literals with accents (single or double quoted)
    for m in re.finditer(r'''(['"])((?:(?!\1)[^\\]|\\.){1,300}?)\1''', src):
        t = m.group(2)
        if re.search(r'[éèêàçùôâîïûüÉÈÀÇÙÔÂÎÏÛÜ]', t):
            results.append(('js', t.strip()))
    # dedupe preserving order
    seen = set()
    uniq = []
    for kind, t in results:
        if t not in seen:
            seen.add(t)
            uniq.append((kind, t))
    return uniq

if __name__ == '__main__':
    for kind, t in extract(sys.argv[1]):
        print(f"{kind}\t{t}")

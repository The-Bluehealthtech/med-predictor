import re, subprocess, sys

def strip_blade(s):
    s = re.sub(r"\{\{.*?\}\}", "null", s, flags=re.S)
    s = re.sub(r"\{!!.*?!!\}", "null", s, flags=re.S)
    out = []
    i = 0
    n = len(s)
    while i < n:
        m = re.match(r"@[a-zA-Z]+", s[i:])
        if m and i+m.end() < n and s[i+m.end()] == '(':
            j = i + m.end()
            depth = 0
            k = j
            while k < n:
                if s[k] == '(':
                    depth += 1
                elif s[k] == ')':
                    depth -= 1
                    if depth == 0:
                        k += 1
                        break
                k += 1
            i = k
            continue
        elif m:
            i += m.end()
            continue
        out.append(s[i])
        i += 1
    return "".join(out)

path = sys.argv[1]
with open(path, encoding="utf-8") as f:
    content = f.read()

scripts = re.findall(r"<script[^>]*>(.*?)</script>", content, re.S)
ok = True
for idx, sc in enumerate(scripts):
    stripped = strip_blade(sc)
    tmp = f"/tmp/_check_{idx}.js"
    with open(tmp, "w", encoding="utf-8") as f:
        f.write(stripped)
    r = subprocess.run(["node", "--check", tmp], capture_output=True, text=True)
    if r.returncode != 0:
        ok = False
        print(f"SYNTAX ERROR in script block {idx} of {path}:")
        print(r.stderr[:2000])
if ok:
    print(f"OK: all {len(scripts)} script blocks in {path} pass node --check")

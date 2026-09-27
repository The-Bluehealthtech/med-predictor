def balance(s):
    dc=dp=0
    in_sq=in_dq=in_lc=in_bc=False
    i=0;n=len(s)
    while i<n:
        c=s[i]; nx=s[i+1] if i+1<n else ''
        if in_lc:
            if c=='\n': in_lc=False
            i+=1; continue
        if in_bc:
            if c=='*' and nx=='/': in_bc=False; i+=2; continue
            i+=1; continue
        if in_sq:
            if c=='\\': i+=2; continue
            if c=="'": in_sq=False
            i+=1; continue
        if in_dq:
            if c=='\\': i+=2; continue
            if c=='"': in_dq=False
            i+=1; continue
        if c=='/' and nx=='/': in_lc=True; i+=2; continue
        if c=='/' and nx=='*': in_bc=True; i+=2; continue
        if c=="'": in_sq=True; i+=1; continue
        if c=='"': in_dq=True; i+=1; continue
        if c=='{': dc+=1
        elif c=='}': dc-=1
        elif c=='(': dp+=1
        elif c==')': dp-=1
        i+=1
    return (dc,dp)

import sys

def apply_edit(path, old, new, label):
    with open(path, encoding="utf-8") as f:
        src = f.read()
    before = balance(src)
    cnt = src.count(old)
    if cnt != 1:
        print(f"FAIL {label}: found {cnt} occurrences")
        sys.exit(1)
    src2 = src.replace(old, new)
    after = balance(src2)
    if before != after:
        print(f"BALANCE MISMATCH {label}: before={before} after={after}")
        sys.exit(1)
    with open(path, "w", encoding="utf-8") as f:
        f.write(src2)
    print(f"OK {label} {before} {after}")

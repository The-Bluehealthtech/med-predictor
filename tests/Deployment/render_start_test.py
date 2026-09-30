#!/usr/bin/env python3
"""Tester le démarrage Linux/Render avec commandes PHP simulées, sans base réelle."""
import json, os, signal, socket, subprocess, sys, tempfile, time
from pathlib import Path

source = Path(sys.argv[1]).read_text()
with tempfile.TemporaryDirectory(dir=Path.cwd()) as directory:
    root=Path(directory)
    fake=root/"bin";fake.mkdir()
    app=root/"app";app.mkdir()
    ports=root/"ports.conf"
    host=root/"site.conf"
    events=root/"events"
    log=root/"log"
    def executable(name, text):
        p=fake/name;p.write_text("#!"+sys.executable+"\n"+text);p.chmod(0o755)
    executable("php", """
import json,os,sys,time
with open(os.environ['EVENTS'],'a') as f:f.write(json.dumps([sys.argv[1:],time.monotonic()])+'\\n')
if '--schema-only' in sys.argv and os.environ.get('FAIL_SCHEMA')=='1':sys.exit(1)
if '--refresh-only' in sys.argv:
 time.sleep(3)
 with open(os.environ['EVENTS'],'a') as f:f.write(json.dumps(['refresh_complete',time.monotonic()])+'\\n')
 sys.exit(int(os.environ.get('FAIL_REFRESH','0')))
""")
    executable("apache2-foreground", """
import os,socket,time
s=socket.socket();s.setsockopt(socket.SOL_SOCKET,socket.SO_REUSEADDR,1)
s.bind(('127.0.0.1',int(os.environ['PORT'])));s.listen()
while True:time.sleep(.1)
""")
    script=root/"start.sh"
    script.write_text(source.replace('/var/www/html',str(app))
        .replace('/etc/apache2/ports.conf',str(ports))
        .replace('/etc/apache2/sites-available/000-default.conf',str(host)))
    def run_case(mode):
        events.write_text("");ports.write_text("Listen 80\n");host.write_text("<VirtualHost *:80>\n")
        with socket.socket() as s:s.bind(('127.0.0.1',0));port=s.getsockname()[1]
        env=dict(os.environ,PATH=str(fake)+":"+os.environ["PATH"],EVENTS=str(events),PORT=str(port))
        if mode=="schema_failure":env["FAIL_SCHEMA"]="1"
        if mode=="refresh_failure":env["FAIL_REFRESH"]="1"
        if mode=="invalid_port":env["PORT"]="invalid"
        with log.open("w") as output:
            process=subprocess.Popen(["sh",str(script)],env=env,stdout=output,stderr=output,start_new_session=True)
            try:
                if mode in ("schema_failure","invalid_port"):
                    assert process.wait(timeout=3)!=0
                    calls=events.read_text()
                    assert "--refresh-only" not in calls
                    with socket.socket() as s:
                        assert s.connect_ex(("127.0.0.1",port))!=0
                else:
                    deadline=time.monotonic()+2
                    while True:
                        with socket.socket() as s:
                            opened=s.connect_ex(("127.0.0.1",port))==0
                        if opened:break
                        assert time.monotonic()<deadline,log.read_text()
                        time.sleep(.025)
                    assert "refresh_complete" not in events.read_text()
                    assert ports.read_text()==f"Listen {port}\n"
                    assert host.read_text()==f"<VirtualHost *:{port}>\n"
                    time.sleep(3.2)
                    assert "refresh_complete" in events.read_text()
                    assert process.poll() is None
                    if mode=="refresh_failure":assert "FIT data refresh failed" in log.read_text()
                print("PASS",mode)
            finally:
                try:os.killpg(process.pid,signal.SIGTERM)
                except ProcessLookupError:pass
                process.wait(timeout=3)
    for case in ("normal","schema_failure","refresh_failure","invalid_port"):run_case(case)

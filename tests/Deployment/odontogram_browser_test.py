"""Test Chrome isolé : fixtures uniquement, sans connexion à FIT."""
import html, json, os, re, subprocess, tempfile
from pathlib import Path
root = Path(__file__).resolve().parents[2]
chrome = os.environ.get('FIT_TEST_CHROME', '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome')
with tempfile.TemporaryDirectory(prefix='fit-odontogram-') as directory:
    work = Path(directory)
    for language, width in [('fr',1280), ('fr',390), ('en',1280), ('en',390)]:
        labels = subprocess.check_output(['php','-r',"echo json_encode(require '"+str(root/'resources/lang'/language/'odontogram.php')+"');"],text=True)
        scripts = (root/'public/js/odontogram.js').read_text()+'\n'+(root/'tests/Deployment/odontogram_runtime_test.js').read_text()
        scripts = scripts.replace('</script','<\\/script')
        fixture = work/'fixture.html'
        fixture.write_text('<!DOCTYPE html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><style>body{margin:24px;background:#f1f5f9;font-family:Arial}</style></head><body><div data-fit-odontogram data-labels="'+html.escape(labels,quote=True)+'"><input type="hidden" data-dental-value value="{}"></div><script>'+scripts+'</script></body></html>')
        output = work/'result.html'
        screenshot = Path('/tmp')/('fit-odontogram-'+language+'-'+str(width)+'.png')
        with output.open('w') as out, (work/'chrome.log').open('w') as err:
            process = subprocess.Popen([chrome,'--headless','--disable-gpu','--no-first-run','--user-data-dir='+str(work/('profile-'+language+str(width))),'--window-size='+str(width)+',960','--virtual-time-budget=3000','--screenshot='+str(screenshot),'--dump-dom',fixture.as_uri()],stdout=out,stderr=err)
            try: process.wait(timeout=20)
            except subprocess.TimeoutExpired:
                process.terminate()
                process.wait(timeout=10)
        match = re.search(r'<pre id="odontogram-test-result">(.*?)</pre>',output.read_text(),re.S)
        assert match, 'Résultat navigateur absent'
        result = json.loads(html.unescape(match.group(1)))
        assert result['success'], result
        print(language, width, 'OK', len(result['checks']), 'contrôles', flush=True)

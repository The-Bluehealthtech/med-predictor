#!/usr/bin/env python3
"""Browser smoke cases for the KSA cockpit without a persistent test database."""
import copy
import html
import json
import re
import subprocess
import tempfile
from pathlib import Path

ROOT = Path(__file__).resolve().parents[2]
view = (ROOT / 'resources/views/test-portail-joueur-simple.blade.php').read_text()
adapter = (ROOT / 'public/js/ksa-player-cockpit-adapter.js').read_text()
source = re.search(r'<script>\n/\* ===== 1\..*?</script>', view, re.S).group()
source = source.removeprefix('<script>').removesuffix('</script>')
source = re.sub(r'var DATA=@json\(\$cockpitV2Data\);', 'var DATA=null;', source)
source = re.sub(r'var cockpitPosition=@json\(\$player->position\);', 'var cockpitPosition=null;', source)
assert 'function mountCockpit(host,D)' in source

baseline = json.loads(r'''{"minutes":459,"matches":7,"index":171,"v":{"goals":0.29,"expected_goals":0.15,"shots":0.71,"shots_on_target":0.29,"chances":0.29,"chances_created":0.14,"key_passes":0.29,"passes_for_a_shot":0.43,"dribbling_in_the_final_third":0.43,"involvement_in_scoring_attacks":0.43,"passes":15.29,"short_passes":6.71,"long_passes":0.14,"progressive_passes":1.29,"progressive_open_passes":1.14,"passes_forward_to_the_final_third":0.57,"passes_into_the_penalty_box":1,"crosses":1.14,"tackles":3.86,"interceptions":2.29,"loose_ball_recoveries":3.14,"challenges":13.14,"defensive_challenges":5.71,"attacking_challenges":7.43,"aerial_challenges":2.57,"dribbles":2.29,"fouls_committed":1.43,"fouls_suffered":1.29,"mistakes_leading_to_chances":0.14,"mistakes_leading_to_goals":0.14},"p":{"passes_accuracy":0.8411,"progressive_passes_accurate":0.8889,"short_passes_accurate":0.8511,"passes_into_the_penalty_box_accurate":0.4286,"crosses_accurate":0,"challenges_won":0.337,"defensive_challenges_won":0.375,"attacking_challenges_won":0.3077,"aerial_challenges_won":0.2778,"tackles_successful":0.4444,"dribbles_successful":0.5625}}''')

cases = []
cases.append(('853', copy.deepcopy(baseline), 'RAM'))
another = copy.deepcopy(baseline)
another['index'] = 200
another['v']['goals'] = 0.5
another['extra'] = [{'name':'yellow_cards','label':'Yellow cards','value':0.5,'unit':'count'},
                    {'name':'Height','label':'Height','value':'182','unit':'text'}]
cases.append(('complete_other', another, 'CF'))
missing = copy.deepcopy(baseline)
missing['matches'] = None
missing['v']['goals'] = None
missing['v']['tackles'] = None
missing['p']['passes_accuracy'] = None
missing['p']['dribbles_successful'] = None
cases.append(('missing', missing, 'LB'))
empty = {'minutes':None,'matches':None,'index':None,
         'v':dict.fromkeys(baseline['v']),'p':dict.fromkeys(baseline['p'])}
cases.append(('almost_empty', empty, 'CF'))
zero = copy.deepcopy(baseline)
zero['minutes'] = 0
cases.append(('zero_minutes', zero, 'RAM'))
cases.append(('goalkeeper', copy.deepcopy(baseline), 'GK'))
cases.append(('switch_to_other', another, 'CF'))

parts = ['<!doctype html><html><body><div id="cockpit-joueur"></div><pre id="results"></pre><script>',
         'const errors=[]; window.onerror=(message)=>{errors.push(String(message));return true};',
         'console.error=(...args)=>errors.push(args.join(" "));',
         'const results=[];', source, '</script>']
for name, data, position in cases:
    parts.append('<script>')
    parts.append('DATA='+json.dumps(data, ensure_ascii=False)+';')
    parts.append('cockpitPosition='+json.dumps(position)+';')
    parts.append(adapter)
    parts.append('''{
        const root=document.querySelector('#cockpit-joueur').shadowRoot;
        const markup=root?.innerHTML||'';
        const text=root?.textContent||'';
        let hash=2166136261;
        for(let i=0;i<markup.length;i++)hash=Math.imul(hash^markup.charCodeAt(i),16777619)>>>0;
        results.push({name:CASE_NAME,hash,length:markup.length,
            noErrors:errors.length===0,noBadText:!/(NaN|undefined|Infinity)/.test(markup),
            titleCount:root?.querySelectorAll('.top').length||0,
            radarAxes:root?.querySelectorAll('svg circle').length||0,
            head:root?.querySelector('.head')?.textContent||'',
            matchKpi:[...root.querySelectorAll('.kpi span')].some(x=>x.textContent==='Matchs'),
            disabled90:!!root.querySelector('button[data-m="90"]')?.disabled,
            shortNote:text.includes('Échantillon très court'),
            fallback:text.includes('Profil de réussite indisponible'),
            indexText:root?.querySelectorAll('.kpi b')[2]?.textContent||'',
            hasYellow:text.includes('Yellow cards') && text.includes('0,5'),
            yellowInDiscipline:[...root.querySelectorAll('.blocks .card')].some(c=>c.querySelector('h3')?.textContent==='Discipline et erreurs' && c.textContent.includes('Yellow cards')),
            hasHeight:text.includes('Height')
        });
        if(CASE_NAME==='complete_other'){
            root.querySelector('button[data-m="90"]').click();
            queueMicrotask(()=>{results[results.length-1].yellowAfter90=[...root.querySelectorAll('.blocks .card')]
                .find(c=>c.querySelector('h3')?.textContent==='Discipline et erreurs')
                ?.textContent.includes('Yellow cards0,69')});
        }
    }'''.replace('CASE_NAME', json.dumps(name)))
    parts.append('</script>')
parts += ['<script>document.getElementById("results").textContent=JSON.stringify(results);</script></body></html>']
with tempfile.TemporaryDirectory() as tmp:
    path = Path(tmp) / 'cockpit-cases.html'
    path.write_text('\n'.join(parts))
    chrome = '/Applications/Google Chrome.app/Contents/MacOS/Google Chrome'
    process = subprocess.run([chrome,'--headless','--disable-gpu','--no-sandbox',
        '--disable-extensions','--virtual-time-budget=2000','--dump-dom',path.as_uri()],
        capture_output=True,text=True,timeout=35)
    match = re.search(r'<pre id="results">(.*?)</pre>',process.stdout,re.S)
    if not match:
        raise RuntimeError('Chrome did not return the test results: '+process.stderr[-1000:])
    results=json.loads(html.unescape(match.group(1)))
    for item in results:
        print(json.dumps(item,ensure_ascii=False))
    assert len(results)==len(cases),results
    assert all(x['noErrors'] and x['noBadText'] and x['titleCount']==1 for x in results),results
    assert results[0]['hash']==2820450506 and results[0]['length']==16859,results[0]
    assert results[1]['hash']!=results[0]['hash'] and results[1]['hasYellow'] and results[1]['yellowInDiscipline'] and results[1]['yellowAfter90'] and not results[1]['hasHeight']
    assert not results[2]['matchKpi'] and results[2]['radarAxes']==4
    assert results[3]['fallback'] and results[3]['disabled90']
    assert results[4]['disabled90'] and results[4]['shortNote']
    assert results[5]['head']=='Lecture indisponible pour ce poste'
    assert results[6]['indexText']=='200' and results[6]['titleCount']==1

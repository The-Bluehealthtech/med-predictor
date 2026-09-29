(function () {
    'use strict';
    const host = document.getElementById('cockpit-joueur');
    if (!host) return;
    const finite = x => typeof x === 'number' && Number.isFinite(x);
    const validRate = x => finite(x) && x >= 0 && x <= 1;
    const goalkeeper = /^(GK|GOALKEEPER|GARDIEN)$/i.test(String(cockpitPosition || '').trim());
    const complete = DATA && finite(DATA.matches) && DATA.matches > 0
        && finite(DATA.minutes) && DATA.minutes > 0
        && finite(DATA.index)
        && Object.values(DATA.v || {}).every(finite)
        && Object.values(DATA.p || {}).every(validRate);
    try {
        if (complete && !goalkeeper && DATA.matches >= 3 && DATA.minutes >= 180) {
            host.removeAttribute('role');
            host.removeAttribute('aria-live');
            host.removeAttribute('class');
            host.textContent = '';
            mountCockpit(host, DATA);
            appendExtraMetrics(host, DATA, 1);
            host.shadowRoot.addEventListener('click', event => {
                const button = event.target.closest('button[data-m]');
                if (button) queueMicrotask(() => appendExtraMetrics(host, DATA, Number(button.dataset.m)));
            });
        } else {
            mountPartialCockpit(host, DATA, goalkeeper);
        }
    } catch (error) {
        host.replaceChildren();
        const message = document.createElement('p');
        message.className = 'bg-white/10 rounded-lg p-4 border border-white/20 text-gray-300';
        message.textContent = 'Données indisponibles pour le moment';
        host.appendChild(message);
    }

    function appendExtraMetrics(target, D, mode) {
        const root = target.shadowRoot;
        if (!root) return;
        const groups = {
            assists: 'Attaque', chances_successful: 'Attaque',
            goals_by_head: 'Attaque', free_kick_shots: 'Attaque',
            free_kick_goals: 'Attaque', key_passes_accurate: 'Attaque',
            dribbling_in_the_final_third_successful: 'Attaque',
            passes_accurate: 'Construction', long_passes_accurate: 'Construction',
            passes_forward_to_the_final_third_accurate: 'Construction',
            super_long_passes: 'Construction', super_long_passes_accurate: 'Construction',
            tackles_successful: 'Duels et défense',
            yellow_cards: 'Discipline et erreurs', red_cards: 'Discipline et erreurs'
        };
        const rows = (D.extra || []).filter(row => finite(row.value));
        const blocks = root.querySelector('.blocks');
        if (!blocks || !rows.length) return;
        const cards = new Map([...blocks.querySelectorAll('.card')].map(card =>
            [card.querySelector('h3')?.textContent, card]));
        const touched = new Set();
        rows.forEach(row => {
            const title = groups[row.name] || 'Indicateurs KSA';
            let card = cards.get(title);
            if (!card) {
                card = document.createElement('section');
                card.className = 'card';
                const heading = document.createElement('h3');
                heading.textContent = title;
                card.appendChild(heading);
                blocks.appendChild(card);
                cards.set(title, card);
            }
            touched.add(card);
            const line = document.createElement('div');
            line.className = 'row';
            line.dataset.ksaUnit = row.unit;
            const label = document.createElement('span');
            label.textContent = row.label;
            const value = document.createElement('b');
            const number = row.unit === 'percent' ? row.value * 100
                : mode === 90 && row.unit === 'count' && D.matches > 0 && D.minutes > 0
                    ? row.value * 90 * D.matches / D.minutes : row.value;
            value.textContent = String(Math.round(number * 100) / 100).replace('.', ',')
                + (row.unit === 'percent' ? ' %' : '');
            line.append(label, value);
            card.appendChild(line);
        });
        touched.forEach(card => {
            const lines = [...card.querySelectorAll('.row')];
            const amount = line => Number(line.querySelector('b')?.textContent.replace(',', '.').replace(' %', ''));
            const counts = lines.filter(line => line.dataset.ksaUnit !== 'percent').map(amount).filter(finite);
            const max = Math.max(0, ...counts);
            lines.forEach(line => {
                let track = line.querySelector('.tr');
                if (!track) {
                    track = document.createElement('div');
                    track.className = 'tr';
                    track.appendChild(document.createElement('i'));
                    line.appendChild(track);
                }
                const n = amount(line);
                const width = line.dataset.ksaUnit === 'percent' ? n : max > 0 ? n / max * 100 : 0;
                const visibleWidth = finite(width) ? Math.max(0, Math.min(width, 100)) : 0;
                track.querySelector('i').style.width = (line.dataset.ksaUnit ? Math.max(visibleWidth, 1) : visibleWidth) + '%';
            });
        });
    }

    function mountPartialCockpit(target, D, isGoalkeeper) {
        const root = target.shadowRoot || target.attachShadow({mode: 'open'});
        const v = D.v || {}, p = D.p || {};
        let mode = 1;
        const present = finite;
        const fmt = x => present(x) ? String(Math.round(x * 100) / 100).replace('.', ',') : '—';
        const pct = x => validRate(x) ? Math.round(x * 100) + ' %' : '—';
        const color = x => x >= .75 ? 'var(--acc)' : x < .4 ? 'var(--bad)' : 'var(--warn)';
        const factor = () => mode === 1 ? 1 : 90 * D.matches / D.minutes;
        const el = (tag, cls, label) => {
            const item = document.createElement(tag);
            if (cls) item.className = cls;
            if (label !== undefined) item.textContent = label;
            return item;
        };
        const svgEl = (tag, attrs) => {
            const item = document.createElementNS('http://www.w3.org/2000/svg', tag);
            Object.entries(attrs).forEach(([name, value]) => item.setAttribute(name, String(value)));
            return item;
        };
        const rateGroups = [
            ['Construction', [['passes_accuracy','Passes','passes'],['short_passes_accurate','Passes courtes','short_passes'],['progressive_passes_accurate','Passes progressives','progressive_passes'],['passes_into_the_penalty_box_accurate','Passes dans la surface','passes_into_the_penalty_box'],['crosses_accurate','Centres','crosses']]],
            ['Duels', [['challenges_won','Duels gagnés','challenges'],['attacking_challenges_won','Duels offensifs','attacking_challenges'],['defensive_challenges_won','Duels défensifs','defensive_challenges'],['aerial_challenges_won','Duels aériens','aerial_challenges'],['tackles_successful','Tacles réussis','tackles']]],
            ['Percussion', [['dribbles_successful','Dribbles réussis','dribbles']]]
        ];
        const detailGroups = [
            ['Attaque', [['goals','Buts'],['expected_goals','Buts attendus (xG)'],['shots','Tirs'],['shots_on_target','Tirs cadrés'],['chances','Occasions'],['chances_created','Occasions créées'],['key_passes','Passes clés'],['passes_for_a_shot','Passes menant à un tir'],['dribbling_in_the_final_third','Dribbles dans le dernier tiers'],['involvement_in_scoring_attacks','Implication dans les actions de but']]],
            ['Construction', [['passes','Passes'],['short_passes','Passes courtes'],['long_passes','Passes longues'],['progressive_passes','Passes progressives'],['progressive_open_passes','Progressives (jeu ouvert)'],['passes_forward_to_the_final_third','Passes vers le dernier tiers'],['passes_into_the_penalty_box','Passes dans la surface'],['crosses','Centres']]],
            ['Duels et défense', [['challenges','Duels'],['attacking_challenges','Duels offensifs'],['defensive_challenges','Duels défensifs'],['aerial_challenges','Duels aériens'],['tackles','Tacles'],['interceptions','Interceptions'],['loose_ball_recoveries','Ballons libres récupérés'],['dribbles','Dribbles tentés']]],
            ['Discipline et erreurs', [['fouls_committed','Fautes commises'],['fouls_suffered','Fautes subies'],['mistakes_leading_to_chances','Erreurs menant à une occasion'],['mistakes_leading_to_goals','Erreurs menant à un but']]]
        ];
        const radarKeys = ['passes_accuracy','progressive_passes_accurate','dribbles_successful','tackles_successful','challenges_won','aerial_challenges_won'];
        const flat = rateGroups.flatMap(group => group[1].map(([key,label,attempt]) =>
            ({key, label, attempt:v[attempt], rate:p[key]})));
        const usable = flat.filter(x => present(x.attempt) && x.attempt >= 1 && validRate(x.rate));
        const labels = Object.fromEntries(flat.map(x => [x.key,x.label]));

        function reading() {
            const card = el('section','card read');
            card.appendChild(el('h3','', 'Lecture de la performance'));
            const head = el('p','head');
            if (isGoalkeeper) head.textContent = 'Lecture indisponible pour ce poste';
            else if (usable.length < 2) head.textContent = 'Lecture indisponible : trop peu de données pour ce joueur.';
            else {
                const best = usable.slice().sort((a,b) => b.rate-a.rate)[0];
                const worst = usable.slice().sort((a,b) => a.rate-b.rate)[0];
                head.textContent = 'Fiable dans les « '+best.label.toLowerCase()+' » ('+pct(best.rate)+'), en difficulté dans les « '+worst.label.toLowerCase()+' » ('+pct(worst.rate)+').';
            }
            card.appendChild(head);
            const ok=[], warning=[], work=[];
            if (!isGoalkeeper && usable.length >= 2) {
                usable.filter(x=>x.rate>=.75).sort((a,b)=>b.rate-a.rate).forEach(x=>
                    ok.push([x.label+' : '+pct(x.rate),'Réussite élevée sur '+fmt(x.attempt*factor())+' tentative(s) '+(mode===1?'par match':'par 90 min')+'.']));
                if (present(v.expected_goals) && v.expected_goals>0 && present(v.goals) && v.goals>v.expected_goals*1.3)
                    ok.push(['Finition au-dessus des attentes','Buts '+fmt(v.goals*factor())+' pour '+fmt(v.expected_goals*factor())+' xG. À confirmer : sur un petit échantillon, cet écart dure rarement.']);
                if ([v.tackles,v.interceptions,v.loose_ball_recoveries].every(present)) {
                    const recovered=v.tackles+v.interceptions+v.loose_ball_recoveries;
                    if(recovered>=8) ok.push(['Fort volume de récupération','Environ '+fmt(recovered*factor())+' ballons récupérés (tacles, interceptions, ballons libres).']);
                }
                usable.filter(x=>x.rate<.4&&x.rate>0).sort((a,b)=>a.rate-b.rate).forEach(x=>
                    warning.push([x.label+' : '+pct(x.rate),"Moins d'une réussite sur deux, sur "+fmt(x.attempt*factor())+' tentative(s).']));
                if(validRate(p.passes_into_the_penalty_box_accurate)&&p.passes_into_the_penalty_box_accurate<.5&&present(v.passes_into_the_penalty_box)&&v.passes_into_the_penalty_box>=1)
                    warning.push(['Dernière passe dans la surface : '+pct(p.passes_into_the_penalty_box_accurate),'Le jeu progresse bien jusqu’au dernier tiers, mais la passe décisive manque de précision.']);
                if(present(v.mistakes_leading_to_goals)&&v.mistakes_leading_to_goals>0&&present(v.mistakes_leading_to_chances)&&present(D.matches))
                    warning.push(['Erreurs coûteuses','Une erreur menant à un but sur '+D.matches+' matchs, et autant menant à une occasion.']);
                if(present(v.crosses)&&v.crosses>=1&&validRate(p.crosses_accurate)&&p.crosses_accurate<.15)
                    work.push(['Centres','0 centre précis sur '+fmt(v.crosses*factor())+' tenté(s) : revoir la décision (centrer ou non) et la technique de frappe.']);
                if(usable.some(x=>x.key!=='crosses_accurate'&&x.rate<.4)&&validRate(p.challenges_won)&&validRate(p.aerial_challenges_won))
                    work.push(['Duels','Gagner davantage de duels ('+pct(p.challenges_won)+" aujourd'hui) : placement avant contact et jeu aérien ("+pct(p.aerial_challenges_won)+').']);
                if(validRate(p.passes_into_the_penalty_box_accurate)&&p.passes_into_the_penalty_box_accurate<.5)
                    work.push(['Passe dans la surface','Passer de '+pct(p.passes_into_the_penalty_box_accurate)+" à plus de 60 % en choisissant mieux le moment et l'angle."]);
            }
            const cols=el('div','cols');
            [['ok','Points forts',ok,'Aucun point fort net ressort.'],['w','À surveiller',warning,"Rien d'alarmant."],['t','Axes de travail',work,'Aucun axe prioritaire.']].forEach(([type,title,items,empty])=>{
                const col=el('div','col '+type);col.appendChild(el('h3','',title));
                if(items.length){const list=el('ul');items.forEach(([label,explanation])=>{const li=el('li');li.append(el('b','',label),el('span','',explanation));list.appendChild(li)});col.appendChild(list)}
                else col.appendChild(el('p','none',empty));
                cols.appendChild(col);
            });
            card.appendChild(cols);
            const sample=present(D.matches)?'Échantillon de '+D.matches+' matchs.':'Échantillon : '+(present(D.minutes)?fmt(D.minutes):'—')+' min jouées.';
            const note=el('p','note','Seuils : réussite de 75 % ou plus, point fort ; moins de 40 %, point faible. '+sample);
            note.style.marginTop='14px';card.appendChild(note);
            if((present(D.matches)&&D.matches<3)||(present(D.minutes)&&D.minutes<180)) {
                const short=el('p','note','Échantillon très court : à interpréter avec prudence');short.style.marginTop='8px';card.appendChild(short);
            }
            return card;
        }
        function kpis() {
            const section=el('section','kpis');
            const items=[];
            if(present(D.matches)) items.push(['Matchs',D.matches]);
            items.push(['Minutes',D.minutes],['Index',D.index],['Buts',present(v.goals)?v.goals*factor():null],
                ['xG',present(v.expected_goals)?v.expected_goals*factor():null],
                ['Passes réussies',validRate(p.passes_accuracy)?pct(p.passes_accuracy):'—']);
            items.forEach(([label,value])=>{const item=el('div','kpi');item.append(el('b','',typeof value==='string'?value:fmt(value)),el('span','',label));section.appendChild(item)});
            return section;
        }
        function radar() {
            const keys=radarKeys.filter(key=>validRate(p[key]));
            const card=el('section','card');card.appendChild(el('h3','','Profil de réussite'));
            if(keys.length<3){card.appendChild(el('p','note','Profil de réussite indisponible : données insuffisantes'));return card}
            const svg=svgEl('svg',{viewBox:'0 0 360 310',role:'img','aria-label':'Radar des taux de réussite'});
            const cx=180,cy=150,r=100,N=keys.length;
            const pt=(i,k)=>{const angle=-Math.PI/2+i*2*Math.PI/N;return [cx+Math.cos(angle)*r*k,cy+Math.sin(angle)*r*k]};
            const poly=(fraction,attrs)=>svgEl('polygon',{points:keys.map((_,i)=>pt(i,fraction).join(',')).join(' '),...attrs});
            [.25,.5,.75,1].forEach(x=>svg.appendChild(poly(x,{fill:'none',stroke:'var(--line)','stroke-width':x===.5?1.4:.8})));
            const mark=svgEl('text',{class:'m',x:cx+4,y:cy-r*.5+11});mark.textContent='50 %';svg.appendChild(mark);
            keys.forEach((key,i)=>{const end=pt(i,1),point=pt(i,p[key]),outer=pt(i,1.24),anchor=outer[0]<cx-8?'end':outer[0]>cx+8?'start':'middle';
                svg.appendChild(svgEl('line',{x1:cx,y1:cy,x2:end[0],y2:end[1],stroke:'var(--line)','stroke-width':.8}));
                const dot=svgEl('circle',{cx:point[0],cy:point[1],r:4,fill:'var(--card)',stroke:color(p[key]),'stroke-width':2.5});
                const label=svgEl('text',{x:outer[0],y:outer[1]-2,'text-anchor':anchor});label.textContent=labels[key];
                const amount=svgEl('text',{x:outer[0],y:outer[1]+12,'text-anchor':anchor,style:'font-weight:700;fill:'+color(p[key])});amount.textContent=pct(p[key]);
                svg.append(dot,label,amount);
            });
            const shape=poly(1,{fill:'var(--acc)','fill-opacity':.22,stroke:'var(--acc)','stroke-width':2,'stroke-linejoin':'round'});
            shape.setAttribute('points',keys.map((key,i)=>pt(i,p[key]).join(',')).join(' '));
            svg.insertBefore(shape,svg.querySelector('circle'));
            card.append(svg,el('p','note','Du centre (0 %) au bord (100 %). Couleur du point : bleu dès 75 %, rouge sous 40 %.'));
            return card;
        }
        function gauges() {
            const card=el('section','card');card.appendChild(el('h3','','Réussite par action'));
            let count=0;
            rateGroups.forEach(([title,rows])=>{
                const available=rows.filter(([key,,attempt])=>validRate(p[key])&&present(v[attempt]));
                if(!available.length)return;
                card.appendChild(el('p','gh',title));
                available.forEach(([key,label,attempt])=>{
                    count++;
                    const line=el('div','g'),top=el('div','l'),name=el('span','',label+' '),small=el('small','', 'sur '+fmt(v[attempt]*factor())+(mode===1?' par match':' par 90 min'));
                    const amount=el('b','',pct(p[key]));amount.style.color=color(p[key]);name.appendChild(small);top.append(name,amount);
                    const track=el('div','tr'),fill=el('i');fill.style.width=Math.max(p[key]*100,1)+'%';fill.style.background=color(p[key]);
                    track.append(fill,el('u'));line.append(top,track);card.appendChild(line);
                });
            });
            return count?card:null;
        }
        function blocks() {
            const container=el('div','blocks');
            detailGroups.forEach(([title,rows])=>{
                const available=rows.filter(([key])=>present(v[key]));
                if(!available.length)return;
                const max=Math.max(...available.map(([key])=>v[key]));
                const card=el('section','card');card.appendChild(el('h3','',title));
                available.forEach(([key,label])=>{
                    const line=el('div','row'),track=el('div','tr'),fill=el('i');
                    fill.style.width=(max>0?v[key]/max*100:0)+'%';track.appendChild(fill);
                    line.append(el('span','',label),el('b','',fmt(v[key]*factor())),track);card.appendChild(line);
                });container.appendChild(card);
            });
            return container;
        }
        function render() {
            root.replaceChildren();
            const style=el('style');style.textContent=css;root.appendChild(style);
            const top=el('div','top'),heading=el('div');
            heading.appendChild(el('h2','','Cockpit performance joueur'));
            let subtitle;
            if(present(D.matches)&&D.matches>0&&present(D.minutes))
                subtitle=D.matches+' matchs, '+Math.round(D.minutes/D.matches)+' min de jeu en moyenne. Valeurs '+(mode===1?'par match':'ramenées à 90 min')+'.';
            else subtitle='Échantillon : '+(present(D.minutes)?fmt(D.minutes):'—')+' min jouées. Valeurs '+(mode===1?'par match':'ramenées à 90 min')+'.';
            heading.appendChild(el('p','sub',subtitle));
            const buttons=el('div','tog');buttons.setAttribute('role','group');buttons.setAttribute('aria-label','Base de calcul');
            [['1','Par match'],['90','Par 90 min']].forEach(([value,label])=>{
                const button=el('button','',label);button.type='button';button.dataset.m=value;button.setAttribute('aria-pressed',String(mode===Number(value)));
                if(value==='90'&&!(present(D.matches)&&D.matches>0&&present(D.minutes)&&D.minutes>0))button.disabled=true;
                button.addEventListener('click',()=>{mode=Number(value);render()});buttons.appendChild(button);
            });
            top.append(heading,buttons);root.appendChild(top);
            root.append(reading(),kpis());
            const grid=el('div','grid');grid.appendChild(radar());const gauge=gauges();if(gauge)grid.appendChild(gauge);root.append(grid,blocks());
            appendExtraMetrics(target, D, mode);
        }
        /* The original stylesheet is copied exactly from mountCockpit, inside this Shadow DOM. */
        const css=':host{display:block;--bg:var(--ck-bg,#111827);--card:var(--ck-card,rgba(255,255,255,.1));--ink:var(--ck-ink,#ffffff);--mute:var(--ck-mute,#9ca3af);--line:var(--ck-line,rgba(255,255,255,.2));--acc:var(--ck-acc,#326295);--warn:var(--ck-warn,#b4530a);--bad:var(--ck-bad,#a4262c);--rad:var(--ck-radius,8px);color:var(--ink);font-family:inherit;line-height:1.5}'
+'@media(prefers-color-scheme:dark){:host(:not([data-light])){--bg:var(--ck-bg,#111827);--card:var(--ck-card,rgba(255,255,255,.1));--ink:var(--ck-ink,#ffffff);--mute:var(--ck-mute,#9ca3af);--line:var(--ck-line,rgba(255,255,255,.2));--acc:var(--ck-acc,#6ea4de);--warn:var(--ck-warn,#e69a4d);--bad:var(--ck-bad,#ef7b80)}}'
+'*{box-sizing:border-box}h2,h3,p{margin:0}.top{display:flex;flex-wrap:wrap;gap:12px;justify-content:space-between;align-items:flex-end;margin-bottom:14px}.top h2{font-size:1.6rem;line-height:1.15}.sub,.note{color:var(--mute);font-size:.9rem}'
+'.tog{display:flex;border:1px solid var(--line);border-radius:8px;overflow:hidden}.tog button{font:inherit;padding:6px 14px;border:0;background:var(--card);color:var(--ink);cursor:pointer}.tog button[aria-pressed=true]{background:var(--acc);color:#fff}button:focus-visible{outline:3px solid var(--warn);outline-offset:-3px}'
+'.card{background:var(--card);border:1px solid var(--line);border-radius:var(--rad);padding:18px;min-width:0}.card h3{font-size:1.05rem;margin-bottom:12px}'
+'.read{padding:22px;margin-bottom:14px;border-left:6px solid var(--acc)}.read .head{font-size:1.35rem;line-height:1.3;font-weight:700;margin-bottom:16px;max-width:60ch}.cols{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px}'
+'.col h3{display:flex;align-items:center;gap:8px;font-size:1rem;margin-bottom:8px}.col h3:before{content:"";width:10px;height:10px;border-radius:50%;background:var(--c)}.col.ok{--c:var(--acc)}.col.w{--c:var(--warn)}.col.t{--c:var(--bad)}'
+'.col ul{list-style:none;margin:0;padding:0;display:grid;gap:10px}.col li{padding-left:12px;border-left:2px solid var(--line)}.col li b{display:block}.col li span{color:var(--mute);font-size:.92rem}.col .none{color:var(--mute);font-size:.92rem}'
+'.kpis{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:1px;background:var(--line);border:1px solid var(--line);border-radius:var(--rad);overflow:hidden;margin-bottom:14px}.kpi{background:var(--card);padding:10px 14px}.kpi b{display:block;font-size:1.7rem;line-height:1.15}.kpi span{color:var(--mute);font-size:.88rem}'
+'.grid{display:grid;grid-template-columns:1fr;gap:14px}@media(min-width:860px){.grid{grid-template-columns:400px 1fr}}'
+'svg{width:100%;height:auto;display:block}svg text{fill:var(--ink);font:inherit;font-size:11.5px}svg .m{fill:var(--mute)}'
+'.g{padding:7px 0;border-bottom:1px solid var(--line)}.g:last-child{border:0}.g .l{display:flex;justify-content:space-between;gap:10px;align-items:baseline}.g .l small{color:var(--mute);font-size:.82rem}.g .l b{font-variant-numeric:tabular-nums}'
+'.tr{position:relative;height:6px;margin-top:5px;background:var(--bg);border-radius:3px}.tr i{position:absolute;left:0;top:0;bottom:0;border-radius:3px}.tr u{position:absolute;left:50%;top:-3px;bottom:-3px;width:1px;background:var(--mute);opacity:.5}'
+'.gh{font-size:.88rem;color:var(--mute);margin:12px 0 2px}.gh:first-of-type{margin-top:0}'
+'.blocks{display:grid;grid-template-columns:repeat(auto-fit,minmax(290px,1fr));gap:14px;margin-top:14px}.row{display:grid;grid-template-columns:1fr auto;gap:2px 10px;padding:5px 0;border-bottom:1px solid var(--line)}.row:last-child{border:0}.row b{font-variant-numeric:tabular-nums}.row .tr{grid-column:1/-1;height:4px;margin:0}.row .tr i{background:var(--acc)}';
        target.removeAttribute('role');target.removeAttribute('aria-live');target.removeAttribute('class');target.textContent='';
        render();
    }
})();

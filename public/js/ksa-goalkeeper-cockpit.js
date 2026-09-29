
/* ===== 1. DONNÉES : seul bloc à brancher sur les données réelles du site ===== */
var DATA_GK=window.FIT_GOALKEEPER_DATA || {minutes:900,matches:10,
v:{shots_on_target_against:3.9,saves:2.7,goals_conceded:1.2,post_shot_xg:1.35,goals_prevented:.15,high_claims:1.8,punches:.6,sweeper_actions:1.1,passes:28,long_passes:12.5,forward_passes:9,goal_kicks:6.2,errors_leading_to_shot:.2,errors_leading_to_goal:.1,penalties_conceded:.1,fouls_committed:.2},
p:{saves_pct:.692,claims_success:.84,sweeper_success:.9,passes_accuracy:.71,long_passes_accurate:.38,forward_passes_accurate:.55}};

/* ===== 2. COMPOSANT (isolé du CSS du site par Shadow DOM) ===== */
function mountCockpitGardien(host,D){
var root=host.shadowRoot||host.attachShadow({mode:"open"});
/* Variables : remplacer le 2e argument de var() par la variable du site, ex. var(--couleur-fond-carte) */
var css=':host{display:block;--bg:var(--ck-bg,#111827);--card:var(--ck-card,rgba(255,255,255,.1));--ink:var(--ck-ink,#ffffff);--mute:var(--ck-mute,#9ca3af);--line:var(--ck-line,rgba(255,255,255,.2));--acc:var(--ck-acc,#326295);--warn:var(--ck-warn,#b4530a);--bad:var(--ck-bad,#a4262c);--rad:var(--ck-radius,8px);color:var(--ink);font-family:inherit;line-height:1.5}'
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

var RT=[["Arrêts",[["saves_pct","Arrêts","shots_on_target_against"]]],["Surface",[["claims_success","Sorties aériennes réussies","high_claims"],["sweeper_success","Sorties hors surface réussies","sweeper_actions"]]],["Jeu au pied",[["passes_accuracy","Passes","passes"],["long_passes_accurate","Passes longues","long_passes"],["forward_passes_accurate","Relances vers l'avant","forward_passes"]]]];
var G=[["Arrêts et buts évités",[["shots_on_target_against","Tirs cadrés subis"],["saves","Arrêts"],["goals_conceded","Buts encaissés"],["post_shot_xg","Buts attendus sur tirs cadrés"],["goals_prevented","Buts évités"]]],["Gestion de la surface",[["high_claims","Sorties aériennes"],["punches","Dégagements du poing"],["sweeper_actions","Sorties hors surface"]]],["Jeu au pied",[["passes","Passes"],["long_passes","Passes longues"],["forward_passes","Relances vers l'avant"],["goal_kicks","Dégagements au pied"]]],["Sécurité",[["errors_leading_to_shot","Erreurs menant à un tir"],["errors_leading_to_goal","Erreurs menant à un but"],["penalties_conceded","Penalties concédés"],["fouls_committed","Fautes commises"]]]];
var RAD=["saves_pct","claims_success","sweeper_success","passes_accuracy","long_passes_accurate","forward_passes_accurate"];
var TH={saves_pct:[.6,.75]};
var mode=1,v=D.v,p=D.p;
function fx(){return mode==1?1:90/(D.minutes/D.matches)}
function n(x){var s=(Math.round(x*100)/100).toString().replace(".",",");return s}
function pc(x){return Math.round(x*100)+" %"}
function col(x,k){var t=TH[k]||[.4,.75];return x>=t[1]?"var(--acc)":x<t[0]?"var(--bad)":"var(--warn)"}
var flat=[];RT.forEach(function(g){g[1].forEach(function(r){flat.push({k:r[0],l:r[1],a:v[r[2]],r:p[r[0]]})})});
var rel=flat.filter(function(x){return x.a>=1&&x.r!=null});

function reading(){
if(rel.length<2)return '<section class="card read"><h3>Lecture de la performance</h3><p class="head">Lecture indisponible : trop peu de données pour ce joueur.</p></section>';
function th(x){return TH[x.k]||[.4,.75]}
var best=rel.slice().sort(function(a,b){return b.r-a.r})[0],worst=rel.slice().sort(function(a,b){return a.r-b.r})[0];
var ok=[],w=[],t=[],per=mode==1?"par match":"par 90 min";
rel.filter(function(x){return x.r>=th(x)[1]}).sort(function(a,b){return b.r-a.r}).forEach(function(x){ok.push([x.l+" : "+pc(x.r),"Réussite élevée sur "+n(x.a*fx())+" tentative(s) "+per+"."])});
if(v.goals_prevented>0)ok.push(["Buts évités : +"+n(v.goals_prevented*fx())+" "+per,"Il encaisse moins que ce que les tirs cadrés subis laissaient prévoir ("+n(v.goals_conceded*fx())+" but(s) pour "+n(v.post_shot_xg*fx())+" attendu(s))."]);
rel.filter(function(x){return x.r<th(x)[0]&&x.r>0}).sort(function(a,b){return a.r-b.r}).forEach(function(x){w.push([x.l+" : "+pc(x.r),"Sous le seuil attendu, sur "+n(x.a*fx())+" tentative(s) "+per+"."])});
if(v.goals_prevented<0)w.push(["Buts évités : "+n(v.goals_prevented*fx())+" "+per,"Il encaisse plus que ce que les tirs cadrés subis laissaient prévoir."]);
if(v.errors_leading_to_goal>0)w.push(["Erreurs coûteuses","Environ "+Math.round(v.errors_leading_to_goal*D.matches)+" erreur(s) menant à un but sur "+D.matches+" matchs."]);
if(p.long_passes_accurate<.5&&v.long_passes>=3)t.push(["Jeu long","Seulement "+pc(p.long_passes_accurate)+" de passes longues réussies sur "+n(v.long_passes*fx())+" tentées : privilégier la relance courte ou travailler la précision."]);
if(worst.r<th(worst)[0]&&worst.k!="long_passes_accurate")t.push(["Priorité : "+worst.l,"Le taux le plus faible du profil ("+pc(worst.r)+")."]);
var head="Fiable dans les « "+best.l.toLowerCase()+" » ("+pc(best.r)+"), en difficulté dans les « "+worst.l.toLowerCase()+" » ("+pc(worst.r)+").";
function col3(c,ti,a,e){return '<div class="col '+c+'"><h3>'+ti+'</h3>'+(a.length?'<ul>'+a.map(function(x){return '<li><b>'+x[0]+'</b><span>'+x[1]+'</span></li>'}).join("")+'</ul>':'<p class="none">'+e+'</p>')+'</div>'}
return '<section class="card read"><h3>Lecture de la performance</h3><p class="head">'+head+'</p><div class="cols">'+col3("ok","Points forts",ok,"Aucun point fort net ressort.")+col3("w","À surveiller",w,"Rien d'alarmant.")+col3("t","Axes de travail",t,"Aucun axe prioritaire.")+'</div><p class="note" style="margin-top:14px">Seuils : réussite de 75 % ou plus, point fort ; moins de 40 %, point faible (arrêts : moins de 60 %). Échantillon de '+D.matches+' matchs.</p></section>';
}
function kpis(){var k=[["Matchs",D.matches],["Minutes",D.minutes],["Arrêts",pc(p.saves_pct)],["Buts encaissés",n(v.goals_conceded*fx())],["Buts évités",(v.goals_prevented>=0?"+":"")+n(v.goals_prevented*fx())],["Passes réussies",pc(p.passes_accuracy)]];
return '<section class="kpis">'+k.map(function(a){return '<div class="kpi"><b>'+a[1]+'</b><span>'+a[0]+'</span></div>'}).join("")+'</section>'}
function radar(){var cx=180,cy=150,r=100,N=6,s="",lab={};flat.forEach(function(x){lab[x.k]=x.l});
function pt(i,k){var a=-Math.PI/2+i*2*Math.PI/N;return [cx+Math.cos(a)*r*k,cy+Math.sin(a)*r*k]}
[.25,.5,.75,1].forEach(function(k){s+='<polygon points="'+RAD.map(function(_,i){return pt(i,k).join(",")}).join(" ")+'" fill="none" stroke="var(--line)" stroke-width="'+(k==.5?1.4:.8)+'"/>'});
s+='<text class="m" x="'+(cx+4)+'" y="'+(cy-r*.5+11)+'">50 %</text>';
RAD.forEach(function(_,i){var q=pt(i,1);s+='<line x1="'+cx+'" y1="'+cy+'" x2="'+q[0]+'" y2="'+q[1]+'" stroke="var(--line)" stroke-width=".8"/>'});
s+='<polygon points="'+RAD.map(function(k,i){return pt(i,p[k]).join(",")}).join(" ")+'" fill="var(--acc)" fill-opacity=".22" stroke="var(--acc)" stroke-width="2" stroke-linejoin="round"/>';
RAD.forEach(function(k,i){var q=pt(i,p[k]),o=pt(i,1.24),an=o[0]<cx-8?"end":o[0]>cx+8?"start":"middle";s+='<circle cx="'+q[0]+'" cy="'+q[1]+'" r="4" fill="var(--card)" stroke="'+col(p[k],k)+'" stroke-width="2.5"/><text x="'+o[0]+'" y="'+(o[1]-2)+'" text-anchor="'+an+'">'+lab[k]+'</text><text x="'+o[0]+'" y="'+(o[1]+12)+'" text-anchor="'+an+'" style="font-weight:700;fill:'+col(p[k],k)+'">'+pc(p[k])+'</text>'});
return '<section class="card"><h3>Profil de réussite</h3><svg viewBox="0 0 360 310" role="img" aria-label="Radar des taux de réussite">'+s+'</svg><p class="note">Du centre (0 %) au bord (100 %). Couleur du point : bleu dès 75 %, rouge sous 40 % (arrêts : rouge sous 60 %).</p></section>'}
function gauges(){return '<section class="card"><h3>Réussite par action</h3>'+RT.map(function(g){return '<p class="gh">'+g[0]+'</p>'+g[1].map(function(r){var x=p[r[0]],a=v[r[2]];return '<div class="g"><div class="l"><span>'+r[1]+' <small>sur '+n(a*fx())+(mode==1?" par match":" par 90 min")+'</small></span><b style="color:'+col(x,r[0])+'">'+pc(x)+'</b></div><div class="tr"><i style="width:'+Math.max(x*100,1)+'%;background:'+col(x,r[0])+'"></i><u></u></div></div>'}).join("")}).join("")+'</section>'}
function blocks(){return '<div class="blocks">'+G.map(function(g){var mx=Math.max.apply(null,g[1].map(function(r){return v[r[0]]}));return '<section class="card"><h3>'+g[0]+'</h3>'+g[1].map(function(r){var x=v[r[0]];return '<div class="row"><span>'+r[1]+'</span><b>'+n(x*fx())+'</b><div class="tr"><i style="width:'+(x/mx*100)+'%"></i></div></div>'}).join("")+'</section>'}).join("")+'</div>'}
function render(){
root.innerHTML='<style>'+css+'</style><div class="top"><div><h2>Cockpit gardien</h2><p class="sub">'+D.matches+' matchs, '+Math.round(D.minutes/D.matches)+' min de jeu en moyenne. Valeurs '+(mode==1?"par match":"ramenées à 90 min")+'.</p></div><div class="tog" role="group" aria-label="Base de calcul"><button data-m="1" aria-pressed="'+(mode==1)+'">Par match</button><button data-m="90" aria-pressed="'+(mode!=1)+'">Par 90 min</button></div></div>'+reading()+kpis()+'<div class="grid">'+radar()+gauges()+'</div>'+blocks();
Array.prototype.forEach.call(root.querySelectorAll("button"),function(b){b.onclick=function(){mode=+b.getAttribute("data-m");render()}});
}
render();
}
/* Mounted by the route adapter below. */

/* Sparse data safeguards. The reference mountCockpitGardien above handles complete data unchanged. */
function mountSparseGardien(host, D) {
  const root = host.shadowRoot || host.attachShadow({mode:'open'});
  const finite = x => typeof x === 'number' && Number.isFinite(x);
  const value = x => finite(x) && x >= 0 ? x : null;
  const rate = x => finite(x) && x >= 0 && x <= 1 ? x : null;
  const v = D.v || {}, p = D.p || {};
  const rows = [
    ['Arrêts','saves_pct','shots_on_target_against','Arrêts'],
    ['Surface','claims_success','high_claims','Sorties aériennes réussies'],
    ['Surface','sweeper_success','sweeper_actions','Sorties hors surface réussies'],
    ['Jeu au pied','passes_accuracy','passes','Passes'],
    ['Jeu au pied','long_passes_accurate','long_passes','Passes longues'],
    ['Jeu au pied','forward_passes_accurate','forward_passes','Relances vers l\'avant']
  ];
  const groups = [
    ['Arrêts et buts évités', [['shots_on_target_against','Tirs cadrés subis'],['saves','Arrêts'],['goals_conceded','Buts encaissés'],['post_shot_xg','Buts attendus sur tirs cadrés'],['goals_prevented','Buts évités']]],
    ['Gestion de la surface', [['high_claims','Sorties aériennes'],['punches','Dégagements du poing'],['sweeper_actions','Sorties hors surface']]],
    ['Jeu au pied', [['passes','Passes'],['long_passes','Passes longues'],['forward_passes','Relances vers l\'avant'],['goal_kicks','Dégagements au pied']]],
    ['Sécurité', [['errors_leading_to_shot','Erreurs menant à un tir'],['errors_leading_to_goal','Erreurs menant à un but'],['penalties_conceded','Penalties concédés'],['fouls_committed','Fautes commises']]]
  ];
  const svgNS = 'http://www.w3.org/2000/svg';
  const el = (tag, className, content) => {
    const node=document.createElement(tag);
    if (className) node.className=className;
    if (content !== undefined) node.textContent=content;
    return node;
  };
  const fmt = x => (Math.round(x*100)/100).toString().replace('.',',');
  const pct = x => Math.round(x*100)+' %';
  const color = (x,k) => x >= .75 ? 'var(--acc)' : x < (k==='saves_pct'?.6:.4) ? 'var(--bad)' : 'var(--warn)';
  const can90 = value(D.matches)>0 && value(D.minutes)>0;
  let mode=1;
  const factor=()=>mode===90 && can90 ? 90*D.matches/D.minutes : 1;
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
  function card(title, className='card') { const c=el('section',className);c.appendChild(el('h3','',title));return c; }
  function reading() {
    const c=card('Lecture de la performance','card read');
    const usable=rows.filter(r=>value(v[r[2]])>=1 && rate(p[r[1]])!==null);
    if(usable.length<2){c.appendChild(el('p','head','Lecture indisponible : trop peu de données pour ce joueur.'));return c;}
    const ordered=[...usable].sort((a,b)=>p[a[1]]-p[b[1]]);
    c.appendChild(el('p','head',`Fiable dans les « ${ordered.at(-1)[3].toLowerCase()} » (${pct(p[ordered.at(-1)[1]])}), en difficulté dans les « ${ordered[0][3].toLowerCase()} » (${pct(p[ordered[0][1]])}).`));
    const cols=el('div','cols');
    const ok=[], warn=[], work=[];
    usable.forEach(r=>{
      const x=p[r[1]], count=fmt(v[r[2]]*factor());
      if(x>=.75)ok.push([`${r[3]} : ${pct(x)}`,`Réussite élevée sur ${count} tentative(s) ${mode===1?'par match':'par 90 min'}.`]);
      if(x>0 && x<(r[1]==='saves_pct'?.6:.4))warn.push([`${r[3]} : ${pct(x)}`,`Sous le seuil attendu, sur ${count} tentative(s) ${mode===1?'par match':'par 90 min'}.`]);
    });
    if(value(v.goals_prevented)!==null && v.goals_prevented>0 && value(v.goals_conceded)!==null && value(v.post_shot_xg)!==null)
      ok.push([`Buts évités : +${fmt(v.goals_prevented*factor())} ${mode===1?'par match':'par 90 min'}`,`Il encaisse moins que ce que les tirs cadrés subis laissaient prévoir (${fmt(v.goals_conceded*factor())} but(s) pour ${fmt(v.post_shot_xg*factor())} attendu(s)).`]);
    if(finite(v.goals_prevented)&&v.goals_prevented<0)warn.push([`Buts évités : ${fmt(v.goals_prevented*factor())} ${mode===1?'par match':'par 90 min'}`,'Il encaisse plus que ce que les tirs cadrés subis laissaient prévoir.']);
    if(value(v.errors_leading_to_goal)>0 && value(D.matches)>0)warn.push(['Erreurs coûteuses',`Environ ${Math.round(v.errors_leading_to_goal*D.matches)} erreur(s) menant à un but sur ${D.matches} matchs.`]);
    if(rate(p.long_passes_accurate)!==null && p.long_passes_accurate<.5 && value(v.long_passes)>=3)
      work.push(['Jeu long',`Seulement ${pct(p.long_passes_accurate)} de passes longues réussies sur ${fmt(v.long_passes*factor())} tentées : privilégier la relance courte ou travailler la précision.`]);
    if(p[ordered[0][1]]<(ordered[0][1]==='saves_pct'?.6:.4) && ordered[0][1]!=='long_passes_accurate')
      work.push([`Priorité : ${ordered[0][3]}`,`Le taux le plus faible du profil (${pct(p[ordered[0][1]])}).`]);
    [['ok','Points forts',ok,'Aucun point fort net ressort.'],['w','À surveiller',warn,"Rien d'alarmant."],['t','Axes de travail',work,'Aucun axe prioritaire.']].forEach(([cl,title,items,empty])=>{
      const col=el('div','col '+cl);col.appendChild(el('h3','',title));
      if(!items.length)col.appendChild(el('p','none',empty));
      else{const ul=el('ul');items.forEach(([head,body])=>{const li=el('li');li.append(el('b','',head),el('span','',body));ul.appendChild(li)});col.appendChild(ul)}cols.appendChild(col)
    });
    c.appendChild(cols);
    c.appendChild(el('p','note',`Seuils : réussite de 75 % ou plus, point fort ; moins de 40 %, point faible (arrêts : moins de 60 %). Échantillon de ${D.matches} matchs.`)).style.marginTop='14px';
    return c;
  }
  function kpis(){
    const strip=el('section','kpis');
    [['Matchs',D.matches],['Minutes',D.minutes],['Arrêts',rate(p.saves_pct)===null?null:pct(p.saves_pct)],['Buts encaissés',value(v.goals_conceded)===null?null:fmt(v.goals_conceded*factor())],['Buts évités',finite(v.goals_prevented)?(v.goals_prevented>=0?'+':'')+fmt(v.goals_prevented*factor()):null],['Passes réussies',rate(p.passes_accuracy)===null?null:pct(p.passes_accuracy)]].forEach(([label,num])=>{
      const box=el('div','kpi');box.append(el('b','',num===null?'—':String(num)),el('span','',label));strip.appendChild(box)
    });return strip;
  }
  function radar(){
    const c=card('Profil de réussite');const available=rows.filter(r=>rate(p[r[1]])!==null);
    if(available.length<3){c.appendChild(el('p','note','Profil de réussite indisponible : données insuffisantes'));return c}
    const svg=document.createElementNS(svgNS,'svg');svg.setAttribute('viewBox','0 0 360 310');svg.setAttribute('role','img');svg.setAttribute('aria-label','Radar des taux de réussite');
    const N=available.length,pt=(i,k)=>{const a=-Math.PI/2+i*2*Math.PI/N;return [180+Math.cos(a)*100*k,150+Math.sin(a)*100*k]};
    function shape(tag,attrs){const n=document.createElementNS(svgNS,tag);Object.entries(attrs).forEach(([k,v])=>n.setAttribute(k,String(v)));svg.appendChild(n);return n}
    [.25,.5,.75,1].forEach(k=>shape('polygon',{points:available.map((_,i)=>pt(i,k).join(',')).join(' '),fill:'none',stroke:'var(--line)','stroke-width':k===.5?1.4:.8}));
    shape('text',{x:184,y:111,class:'m'}).textContent='50 %';
    available.forEach((_,i)=>{const q=pt(i,1);shape('line',{x1:180,y1:150,x2:q[0],y2:q[1],stroke:'var(--line)','stroke-width':.8})});
    shape('polygon',{points:available.map((r,i)=>pt(i,p[r[1]]).join(',')).join(' '),fill:'var(--acc)','fill-opacity':'.22',stroke:'var(--acc)','stroke-width':2,'stroke-linejoin':'round'});
    available.forEach((r,i)=>{const q=pt(i,p[r[1]]),o=pt(i,1.24),anchor=o[0]<172?'end':o[0]>188?'start':'middle';shape('circle',{cx:q[0],cy:q[1],r:4,fill:'var(--card)',stroke:color(p[r[1]],r[1]),'stroke-width':2.5});shape('text',{x:o[0],y:o[1]-2,'text-anchor':anchor}).textContent=r[3];shape('text',{x:o[0],y:o[1]+12,'text-anchor':anchor,style:'font-weight:700;fill:'+color(p[r[1]],r[1])}).textContent=pct(p[r[1]])});
    c.append(svg,el('p','note','Du centre (0 %) au bord (100 %). Couleur du point : bleu dès 75 %, rouge sous 40 % (arrêts : rouge sous 60 %).'));return c;
  }
  function gauges(){
    const available=rows.filter(r=>rate(p[r[1]])!==null && value(v[r[2]])!==null);
    if(!available.length)return null;
    const c=card('Réussite par action');let previous='';
    available.forEach(r=>{if(r[0]!==previous){c.appendChild(el('p','gh',r[0]));previous=r[0]}
      const g=el('div','g'),line=el('div','l'),span=el('span','',r[3]+' '),small=el('small','',`sur ${fmt(v[r[2]]*factor())}${mode===1?' par match':' par 90 min'}`);span.appendChild(small);
      const bold=el('b','',pct(p[r[1]]));bold.style.color=color(p[r[1]],r[1]);line.append(span,bold);
      const track=el('div','tr'),fill=el('i'),mark=el('u');fill.style.width=Math.max(p[r[1]]*100,1)+'%';fill.style.background=color(p[r[1]],r[1]);track.append(fill,mark);g.append(line,track);c.appendChild(g)
    });return c;
  }
  function blocks(){
    const container=el('div','blocks');groups.forEach(([title,items])=>{
      const available=items.filter(([key])=>finite(v[key]));if(!available.length)return;
      const c=card(title),max=Math.max(0,...available.map(([key])=>Math.abs(v[key])));
      available.forEach(([key,label])=>{const line=el('div','row'),track=el('div','tr'),fill=el('i');line.append(el('span','',label),el('b','',fmt(v[key]*factor())));fill.style.width=(max>0?Math.abs(v[key])/max*100:0)+'%';track.appendChild(fill);line.appendChild(track);c.appendChild(line)});container.appendChild(c)
    });return container;
  }
  function render(){
    root.replaceChildren();const style=el('style');style.textContent=css;root.appendChild(style);
    const top=el('div','top'),heading=el('div');heading.appendChild(el('h2','','Cockpit gardien'));
    const subtitle=can90?`${D.matches} matchs, ${Math.round(D.minutes/D.matches)} min de jeu en moyenne. Valeurs ${mode===1?'par match':'ramenées à 90 min'}.`:`Échantillon : ${value(D.minutes)===null?'—':fmt(D.minutes)} min jouées. Valeurs par match.`;
    heading.appendChild(el('p','sub',subtitle));const buttons=el('div','tog');buttons.setAttribute('role','group');buttons.setAttribute('aria-label','Base de calcul');
    [[1,'Par match'],[90,'Par 90 min']].forEach(([m,label])=>{const b=el('button','',label);b.type='button';b.dataset.m=String(m);b.setAttribute('aria-pressed',String(mode===m));b.disabled=m===90&&!can90;b.addEventListener('click',()=>{mode=m;render()});buttons.appendChild(b)});
    top.append(heading,buttons);root.append(top,reading(),kpis());
    const grid=el('div','grid');grid.appendChild(radar());const gauge=gauges();if(gauge)grid.appendChild(gauge);root.append(grid,blocks());
  }
  host.removeAttribute('role');host.removeAttribute('aria-live');host.removeAttribute('class');host.textContent='';render();
}
(function(){
  const host=document.getElementById('cockpit-joueur');if(!host)return;
  const D=DATA_GK;
  const finite=x=>typeof x==='number'&&Number.isFinite(x);
  const complete=D&&finite(D.matches)&&D.matches>0&&finite(D.minutes)&&D.minutes>0
    && Object.values(D.v||{}).length===16&&Object.values(D.v).every(finite)
    && Object.values(D.p||{}).length===6&&Object.values(D.p).every(x=>finite(x)&&x>=0&&x<=1);
  try{if(complete)mountCockpitGardien(host,D);else mountSparseGardien(host,D||{});}catch(error){
    host.replaceChildren();const message=document.createElement('p');message.className='bg-white/10 rounded-lg p-4 border border-white/20 text-gray-300';message.textContent='Données indisponibles pour le moment';host.appendChild(message);
  }
})();

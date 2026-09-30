/* Presentation-only English localization. The validated French cockpit algorithms are unchanged. */
(() => {
  if (document.documentElement.lang !== 'en') return;
  const host = document.getElementById('cockpit-joueur');
  if (!host?.shadowRoot) return;
  const labels = {
    'Cockpit performance joueur':'Player performance cockpit',
    'Cockpit gardien':'Goalkeeper cockpit',
    'Lecture de la performance':'Performance reading',
    'Points forts':'Strengths', 'À surveiller':'Watch points', 'Axes de travail':'Areas to improve',
    'Profil de réussite':'Success profile', 'Réussite par action':'Success by action',
    'Par match':'Per match', 'Par 90 min':'Per 90 min',
    'Matchs':'Matches', 'Minutes':'Minutes', 'Index':'Index', 'Buts':'Goals',
    'Buts attendus (xG)':'Expected goals (xG)', 'Buts encaissés':'Goals conceded',
    'Buts évités':'Goals prevented', 'Buts attendus sur tirs cadrés':'Post-shot expected goals',
    'Arrêts et buts évités':'Saves and goals prevented', 'Arrêts':'Saves',
    'Tirs cadrés subis':'Shots on target faced', 'Gestion de la surface':'Box management',
    'Sorties aériennes réussies':'Successful high claims', 'Sorties hors surface réussies':'Successful sweeper actions',
    'Sorties aériennes':'High claims', 'Dégagements du poing':'Punches',
    'Sorties hors surface':'Sweeper actions', 'Jeu au pied':'Distribution',
    'Passes réussies':'Pass accuracy', 'Passes longues':'Long passes',
    'Relances vers l’avant':'Forward distribution', "Relances vers l'avant":'Forward distribution',
    'Dégagements au pied':'Goal kicks', 'Sécurité':'Safety',
    'Erreurs menant à un tir':'Errors leading to a shot',
    'Erreurs menant à un but':'Errors leading to a goal',
    'Penalties concédés':'Penalties conceded', 'Fautes commises':'Fouls committed',
    'Attaque':'Attack', 'Construction':'Build-up', 'Duels et défense':'Duels and defence',
    'Discipline et erreurs':'Discipline and errors', 'Duels':'Duels', 'Percussion':'Dribbling',
    'Passes progressives':'Progressive passes', 'Passes courtes':'Short passes',
    'Passes dans la surface':'Passes into the box', 'Passes vers le dernier tiers':'Passes into the final third',
    'Progressives (jeu ouvert)':'Progressive passes (open play)', 'Centres':'Crosses',
    'Duels gagnés':'Duels won', 'Duels offensifs':'Attacking duels',
    'Duels défensifs':'Defensive duels', 'Duels aériens':'Aerial duels',
    'Tacles réussis':'Successful tackles', 'Tacles':'Tackles',
    'Dribbles réussis':'Successful dribbles', 'Dribbles tentés':'Dribbles attempted',
    'Dribbles dans le dernier tiers':'Dribbles in the final third',
    'Interceptions':'Interceptions', 'Ballons libres récupérés':'Loose balls recovered',
    'Buts':'Goals', 'Tirs cadrés':'Shots on target', 'Tirs':'Shots',
    'Occasions créées':'Chances created', 'Occasions':'Chances',
    'Passes clés':'Key passes', 'Passes menant à un tir':'Passes leading to a shot',
    'Implication dans les actions de but':'Involvement in scoring attacks',
    'Fautes subies':'Fouls suffered', 'Erreurs menant à une occasion':'Errors leading to a chance',
    'Passes':'Passes', 'Aucun point fort net ressort.':'No clear strength stands out.',
    "Rien d'alarmant.":'Nothing of concern.', 'Aucun axe prioritaire.':'No priority area.',
    'Lecture indisponible : trop peu de données pour ce joueur.':'Reading unavailable: insufficient data for this player.',
    'Profil de réussite indisponible : données insuffisantes':'Success profile unavailable: insufficient data',
    'Poste non renseigné':'Position not recorded',
    'Finition au-dessus des attentes':'Finishing above expectations',
    'Fort volume de récupération':'High ball recovery volume',
    'Dernière passe dans la surface':'Final pass into the box',
    'Erreurs coûteuses':'Costly errors', 'Passe dans la surface':'Pass into the box',
    'Jeu long':'Long passing', 'Priorité :':'Priority:',
    'Base de calcul':'Calculation basis', 'Radar des taux de réussite':'Success rate radar',
  };
  const ordered = Object.entries(labels).sort((a,b)=>b[0].length-a[0].length);
  const esc = s => s.replace(/[.*+?^${}()|[\]\\]/g,'\\$&');
  const replacements = [
    [/^(\d+) matchs, (\d+) min de jeu en moyenne\. Valeurs (par match|ramenées à 90 min)\.$/,(_,m,n,mode)=>`${m} matches, ${n} average minutes played. Values ${mode==='par match'?'per match':'per 90 min'}.`],
    [/^Échantillon : (.*?) min jouées\. Valeurs par match\.$/,(_,n)=>`Sample: ${n} minutes played. Values per match.`],
    [/^Fiable dans les « (.*?) » \((\d+) %\), en difficulté dans les « (.*?) » \((\d+) %\)\.$/,(_,a,ap,b,bp)=>`Reliable in “${word(a)}” (${ap}%), struggling in “${word(b)}” (${bp}%).`],
    [/^Réussite élevée sur (.*?) tentative\(s\) (par match|par 90 min)\.$/,(_,n,m)=>`High success rate across ${n} attempt(s) ${m==='par match'?'per match':'per 90 min'}.`],
    [/^Sous le seuil attendu, sur (.*?) tentative\(s\) (par match|par 90 min)\.$/,(_,n,m)=>`Below the expected threshold across ${n} attempt(s) ${m==='par match'?'per match':'per 90 min'}.`],
    [/^Seuils : réussite de 75 % ou plus, point fort ; moins de 40 %, point faible( \(arrêts : moins de 60 %\))?\. Échantillon de (\d+) matchs\.$/,(_,g,n)=>`Thresholds: 75% or higher indicates a strength; below 40% indicates a weakness${g?' (saves: below 60%)':''}. Sample of ${n} matches.`],
    [/^sur (.*?) (par match|par 90 min)$/,(_,n,m)=>`across ${n} ${m==='par match'?'per match':'per 90 min'}`],
    [/^Buts (.*?) pour (.*?) xG\. À confirmer : sur un petit échantillon, cet écart dure rarement\.$/,(_,g,x)=>`Goals ${g} against ${x} xG. To be confirmed: this gap rarely persists in a small sample.`],
    [/^Moins d'une réussite sur deux, sur (.*?) tentative\(s\)\.$/,(_,n)=>`Fewer than half successful across ${n} attempt(s).`],
    [/^Une erreur menant à un but sur (\d+) matchs, et autant menant à une occasion\.$/,(_,n)=>`One error leading to a goal over ${n} matches, and another leading to a chance.`],
    [/^0 centre précis sur (.*?) tenté\(s\) : revoir la décision \(centrer ou non\) et la technique de frappe\.$/,(_,n)=>`No accurate cross from ${n} attempt(s): review the crossing decision and technique.`],
    [/^Passer de (.*?) à plus de 60 % en choisissant mieux le moment et l'angle\.$/,(_,n)=>`Improve from ${n} to over 60% by choosing better timing and angles.`],
    [/^Gagner davantage de duels \((\d+) % aujourd'hui\) : placement avant contact et jeu aérien \((\d+) %\)\.$/,(_,d,a)=>`Win more duels (${d}% currently): improve positioning before contact and aerial play (${a}%).`],
  ];
  const snippets = [
    ['Valeurs par match','Values per match'],['ramenées à 90 min','per 90 min'],
    [' par match',' per match'],[' par 90 min',' per 90 min'],
    ['Du centre (0 %) au bord (100 %). Couleur du point : bleu dès 75 %, rouge sous 40 % (arrêts : rouge sous 60 %).','From the centre (0%) to the edge (100%). Point color: blue from 75%, red below 40% (saves: red below 60%).'],
    ['Du centre (0 %) au bord (100 %). Couleur du point : bleu dès 75 %, rouge sous 40 %.','From the centre (0%) to the edge (100%). Point color: blue from 75%, red below 40%.'],
    ['À confirmer : sur un petit échantillon, cet écart dure rarement.','To be confirmed: this gap rarely persists in a small sample.'],
    ["Moins d'une réussite sur deux, sur",'Fewer than half successful across'],
    ['Le jeu progresse bien jusqu’au dernier tiers, mais la passe décisive manque de précision.','Build-up reaches the final third, but the final pass lacks accuracy.'],
    ['Le jeu progresse bien jusqu\'au dernier tiers, mais la passe décisive manque de précision.','Build-up reaches the final third, but the final pass lacks accuracy.'],
    ['Gagner davantage de duels','Win more duels'],
    ['placement avant contact et jeu aérien','positioning before contact and aerial play'],
    ['Environ','Approximately'], ['erreur(s) menant à un but sur','error(s) leading to a goal across'],
    ['matchs, et autant menant à une occasion.','matches, and as many leading to a chance.'],
    ['tentative(s)','attempt(s)'], ['attendu(s)','expected'], ['but(s)','goal(s)'],
    ['Il encaisse moins que ce que les tirs cadrés subis laissaient prévoir','He concedes fewer than the shots on target faced would predict'],
    ['Il encaisse plus que ce que les tirs cadrés subis laissaient prévoir.','He concedes more than the shots on target faced would predict.'],
    ['Seulement','Only'], ['de passes longues réussies','of long passes completed'],
    ['tentées : privilégier la relance courte ou travailler la précision.','attempted: favor short distribution or improve accuracy.'],
    ['Le taux le plus faible du profil','The lowest rate in the profile'],
    ['une réussite sur deux','one success in two'],['ballons récupérés','balls recovered'],
    ['tacles, interceptions, ballons libres','tackles, interceptions, loose balls'],
    ['centre précis sur','accurate cross across'],['tenté(s) : revoir la décision (centrer ou non) et la technique de frappe.','attempt(s): review the crossing decision and technique.'],
    ['Passer de','Improve from'], ['à plus de 60 % en choisissant mieux le moment et l’angle.','to over 60% by choosing better timing and angles.'],
  ];
  function word(s){
    let out=s;
    for(const [fr,en] of ordered)out=out.replace(new RegExp(esc(fr),'gi'),match=>match[0]===match[0].toLowerCase()?en[0].toLowerCase()+en.slice(1):en);
    return out;
  }
  function translate(source){
    const lead=source.match(/^\s*/)[0],trail=source.match(/\s*$/)[0];
    let s=source.trim();if(!s)return source;
    for(const [pattern,fn] of replacements)if(pattern.test(s))return lead+s.replace(pattern,fn).replace(/(\d),(\d)/g,'$1.$2')+trail;
    for(const [fr,en] of snippets)if(s.includes(fr))s=s.replaceAll(fr,en);
    s=word(s).replace(/(\d),(\d)/g,'$1.$2');
    return lead+s+trail;
  }
  let active=false;
  function localize(){
    if(active)return; active=true;
    try{
      const walker=document.createTreeWalker(host.shadowRoot,NodeFilter.SHOW_TEXT);
      let node;while((node=walker.nextNode())){
        if(node.parentElement?.tagName==='STYLE'||node.parentElement?.tagName==='SCRIPT')continue;
        const next=translate(node.nodeValue);if(next!==node.nodeValue)node.nodeValue=next;
      }
      host.shadowRoot.querySelectorAll('[aria-label]').forEach(node=>{
        const text=node.getAttribute('aria-label'),next=translate(text);if(next!==text)node.setAttribute('aria-label',next);
      });
    }finally{active=false}
  }
  localize();new MutationObserver(localize).observe(host.shadowRoot,{subtree:true,childList:true,characterData:true});
})();

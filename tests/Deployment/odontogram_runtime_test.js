/* Vérifications navigateur avec observations fictives, sans API ni écriture réelle. */
(async () => {
 const browserErrors=[];window.addEventListener('error',event=>browserErrors.push(event.message));window.addEventListener('unhandledrejection',event=>browserErrors.push(String(event.reason)));
 const wait=()=>new Promise(resolve=>setTimeout(resolve,50));await wait();
 const labels=JSON.parse(document.querySelector('[data-fit-odontogram]').dataset.labels);
 let h=document.querySelector('[data-fit-odontogram]'),r=h.shadowRoot;
 const get=()=>JSON.parse(h.querySelector('[data-dental-value]').value);
 const eq=(a,b)=>{if(JSON.stringify(a)!==JSON.stringify(b))throw new Error('Valeurs différentes : '+JSON.stringify(a)+' / '+JSON.stringify(b));};
 const checks=[];let error;
 try {
  eq(r.querySelectorAll('.tooth').length,32);eq(get(),{});checks.push('32 dents ; aucune donnée saine par défaut');
  r.querySelector('[data-tooth="11"]').dispatchEvent(new MouseEvent('click'));r.querySelector('#tooth-status').value='cavity';r.querySelector('#tooth-notes').value='<script>fixture</script>';r.querySelector('.primary').click();eq(get()['11'].status,'cavity');eq(Object.keys(get()).filter(k=>!k.startsWith('_')).length,1);checks.push('État et notes par dent');
  r.querySelector('[data-face="mesial"]').dispatchEvent(new MouseEvent('click'));r.querySelector('#tooth-status').value='filling';r.querySelector('.primary').click();eq(get()['11'].surfaces.mesial.status,'filling');checks.push('Observation par face');
  r.querySelector('.toolbar button:last-child').click();eq(get()['11'].surfaces,undefined);checks.push('Annulation sans altération de la dent');
  r.querySelector('#tooth-status').value='healthy';r.querySelector('[data-tooth="12"]').dispatchEvent(new MouseEvent('click'));eq(get()['11'].surfaces.mesial.status,'healthy');checks.push('Changement de dent sans perte de la saisie');
  const filter=r.querySelector('.toolbar select');filter.value='documented';filter.dispatchEvent(new Event('change'));eq([...r.querySelectorAll('.tooth')].filter(t=>t.style.display!=='none').length,1);checks.push('Filtre des observations');
  const data=get();h.remove();h=document.createElement('div');h.dataset.fitOdontogram='';h.dataset.labels=JSON.stringify(labels);h.dataset.initial=JSON.stringify(data);h.dataset.readonly='true';document.body.append(h);window.FitOdontogram.mount(h);r=h.shadowRoot;r.querySelector('[data-tooth="11"]').dispatchEvent(new MouseEvent('click'));eq(r.querySelectorAll('.primary').length,0);eq(h.querySelectorAll('input').length,0);eq(r.querySelectorAll('.editor script').length,0);if(!r.querySelector('.editor').textContent.includes('<script>fixture</script>'))throw new Error('Notes non relues');checks.push('Lecture seule et texte protégé');
  data['legacy-1']={status:'healthy'};h.remove();h=document.createElement('div');h.dataset.fitOdontogram='';h.dataset.labels=JSON.stringify(labels);let input=document.createElement('input');input.type='hidden';input.setAttribute('data-dental-value','');input.value=JSON.stringify(data);h.append(input);document.body.append(h);window.FitOdontogram.mount(h);r=h.shadowRoot;r.querySelector('[data-tooth="11"]').dispatchEvent(new MouseEvent('click'));r.querySelector('#tooth-notes').value='Observation de démonstration';r.querySelector('.primary').click();eq(get()['legacy-1'].status,'healthy');checks.push('Ancien format conservé');
  r.querySelector('[data-tooth="12"]').dispatchEvent(new KeyboardEvent('keydown',{key:'Enter'}));eq(r.querySelector('[data-tooth="12"]').getAttribute('aria-pressed'),'true');checks.push('Sélection au clavier');
  r.querySelector('.primary').click();eq(Object.keys(get()).filter(k=>!k.startsWith('_')).length,2);checks.push('Observation vide refusée');
  h.remove();h=document.createElement('div');h.dataset.fitOdontogram='';h.dataset.labels=JSON.stringify(labels);input=document.createElement('input');input.type='hidden';input.setAttribute('data-dental-value','');input.value=JSON.stringify({'11':{status:'missing'}});h.append(input);document.body.append(h);window.FitOdontogram.mount(h);r=h.shadowRoot;
  r.querySelector('[data-tooth="11"]').dispatchEvent(new MouseEvent('click'));eq(r.querySelector('#tooth-status').value,'');r.querySelector('[data-tooth="12"]').dispatchEvent(new MouseEvent('click'));r.querySelector('#tooth-status').value='healthy';r.querySelector('.primary').click();eq(get()._legacy['11'].status,'missing');eq(get()['11'],undefined);checks.push('Ancienne notation ambiguë non interprétée, original conservé');
  eq(browserErrors,[]);checks.push('Aucune erreur JavaScript');
  if(document.documentElement.scrollWidth>innerWidth)throw new Error('Débordement horizontal de la page');checks.push('Page sans débordement horizontal');
 } catch(e){error=e.message;}
 const result=document.createElement('pre');result.id='odontogram-test-result';result.textContent=JSON.stringify({success:!error,error,checks});document.body.append(result);document.title=error?'TEST FAILED':'TEST OK';
})();

(() => {
'use strict';
function init(){
 const box=document.getElementById('medical-icd11'); if(!box)return;
 const query=document.getElementById('medical-icd-query'), results=document.getElementById('medical-icd-results');
 const status=document.getElementById('medical-icd-status'), chosen=document.getElementById('medical-icd-chosen');
 const input=document.getElementById('medical-icd-selection');
 let items=[]; try {items=JSON.parse(input.value)||[];}catch(e){items=[];}
 let existing=[];try{existing=JSON.parse(box.dataset.existing)||[];}catch(e){}
 items=items.map(x=>existing.find(y=>y.id===x.id&&y.release===x.release&&y.language===x.language)||x);
 let timer, controller, sequence=0;
 function draw(){
  chosen.replaceChildren();
  items.forEach((item,i)=>{
   const row=document.createElement('li'), button=document.createElement('button');
   row.textContent=(item.code||item.id)+' — '+(item.label||item.release)+' ';
   button.type='button'; button.textContent='×'; button.setAttribute('aria-label','Remove '+(item.code||item.id));
   button.addEventListener('click',()=>{items.splice(i,1);draw();});
   row.append(button); chosen.append(row);
  });
  input.value=JSON.stringify(items.map(({id,release,language})=>({id,release,language})));
 }
 draw();
 query.addEventListener('input',()=>{
  clearTimeout(timer); controller?.abort(); const current=++sequence;
  results.replaceChildren(); status.textContent='';
  if(query.value.trim().length<2)return;
  timer=setTimeout(async()=>{
   controller=new AbortController();
   const url=new URL(box.dataset.url,location.origin);
   url.searchParams.set('q',query.value.trim());url.searchParams.set('language',box.dataset.language);
   try{
    const response=await fetch(url,{headers:{Accept:'application/json'},signal:controller.signal});
    if(!response.ok)throw new Error('Unavailable');
    const data=await response.json();if(current!==sequence)return;
    const list=Array.isArray(data.items)?data.items:[];
    status.textContent=list.length?'':box.dataset.empty;
    list.forEach(item=>{
     const button=document.createElement('button'); button.type='button';
     button.className='block w-full text-left border rounded p-2 my-1';
     button.textContent=item.code+' — '+item.label;
     button.addEventListener('click',()=>{
      if(!items.some(x=>x.id===item.id&&x.release===item.release&&x.language===item.language))items.push(item);
      draw();results.replaceChildren();query.value='';
     });results.append(button);
    });
   }catch(e){if(e.name!=='AbortError'&&current===sequence)status.textContent=box.dataset.error;}
  },300);
 });
}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',init);else init();
})();

// Recherche RxNorm via FIT ; aucun texte externe n'est inséré comme HTML.
document.addEventListener('DOMContentLoaded', () => {
    const root=document.getElementById('pcma-medication-catalogue');
    if (!root) return;
    const labels=JSON.parse(root.dataset.labels);
    const search=document.getElementById('medication_search'), results=document.getElementById('medication_results');
    const selected=document.getElementById('medication_selected'), hidden=document.getElementById('medication_selection');
    let items=[], timer, pending, sequence=0;
    try { const value=JSON.parse(hidden.value || '[]'); if(Array.isArray(value)) items=value; } catch {}
    const element=(tag,text,cls='')=>{const e=document.createElement(tag);if(text!==null)e.textContent=text;e.className=cls;return e;};
    const sync=()=>{hidden.value=JSON.stringify(items.map(p=>({id:p.id,
        presentation_id:p.presentation_id ?? p.presentation?.id ?? null,
        dose:p.dose??null,route:p.route??null,frequency:p.frequency??null})));
        hidden.dispatchEvent(new Event('change',{bubbles:true}));};
    const checks=new Map();
    const paintAlert=(node,a)=>{
        node.replaceChildren();node.setAttribute('role','status');
        const hit=a?.status==='mentions_found';
        node.className='border rounded-md p-3 mt-2 '+(hit?'border-yellow-500 bg-yellow-50':'border-gray-300');
        node.append(element('strong',hit?labels.alert_title:(a?.status==='unavailable'?labels.alert_unavailable:labels.alert_unresolved)));
        if(hit){
            node.append(element('p',labels.alert_note,'text-sm'));
            (a.matches||[]).forEach(m=>{
                const details=element('details',null,'mt-2');
                details.append(element('summary',m.ingredient.name+' — '+m.category+' · '+labels.alert_context));
                (m.context||[]).forEach(r=>details.append(element('p',r.text,'text-sm')));
                node.append(details);
            });
        }
    };
    const checkAlert=async(p,node)=>{
        if(p.source!=='RxNorm'){paintAlert(node,{status:'unresolved'});return;}
        if(p.antidoping)paintAlert(node,p.antidoping);
        else node.append(element('p',labels.alert_loading));
        if(!checks.has(p.id))checks.set(p.id,(async()=>{
            try{
                const response=await fetch(root.dataset.endpoint+'/'+encodeURIComponent(p.id)+'/antidoping',
                    {headers:{Accept:'application/json'},credentials:'same-origin'});
                if(!response.ok)throw new Error('Unavailable');
                return await response.json();
            }catch{return {status:'unavailable',version:'2025',matches:[]};}
        })());
        const result=await checks.get(p.id);
        p.antidoping=result;
        if(node.isConnected&&items.includes(p))paintAlert(node,result);
    };
    const render=()=>{
        selected.replaceChildren();
        items.forEach((p,index)=>{
            const card=element('div',null,'border border-gray-300 rounded-md p-3');
            card.append(element('strong',p.name));
            card.append(element('p',[p.source,p.rxcui||p.id,p.version].filter(Boolean).join(' · '),'text-xs text-gray-500'));
            card.append(element('p',(p.substances||[]).join(', '),'text-xs text-gray-500'));
            const remove=element('button',labels.remove,'ml-3 text-red-600');remove.type='button';
            remove.addEventListener('click',()=>{items.splice(index,1);render();sync();});card.append(remove);
            const presentations=p.presentations || (p.presentation?[p.presentation]:[]);
            if(presentations.length){
                const label=element('label',labels.presentation,'block text-sm mt-2');
                const select=element('select',null,'w-full border rounded-md p-1');
                const empty=element('option',labels.unspecified);empty.value='';select.append(empty);
                presentations.forEach(pr=>{const option=element('option',pr.name);option.value=pr.id;select.append(option);});
                select.value=p.presentation_id ?? p.presentation?.id ?? '';
                select.addEventListener('change',()=>{p.presentation_id=select.value||null;sync();});
                label.append(select);card.append(label);
            }
            ['dose','route','frequency'].forEach(key=>{
                const label=element('label',labels[key],'block text-sm mt-2');
                const input=element('input',null,'w-full border rounded-md p-1');input.type='text';input.maxLength=200;
                input.value=p[key]??'';input.addEventListener('input',()=>{p[key]=input.value||null;sync();});
                label.append(input);card.append(label);
            });
            selected.append(card);
            const alert=element('div',null);card.append(alert);checkAlert(p,alert);
        });
    };
    search.addEventListener('input',()=>{
        clearTimeout(timer);if(pending)pending.abort();
        const current=++sequence;results.replaceChildren();
        const q=search.value.trim();if(q.length<2)return;
        timer=setTimeout(async()=>{
            pending=new AbortController();results.append(element('p',labels.loading,'text-sm'));
            try{
                const response=await fetch(root.dataset.endpoint+'?q='+encodeURIComponent(q),
                    {headers:{Accept:'application/json'},credentials:'same-origin',signal:pending.signal});
                if(!response.ok)throw new Error('Unavailable');
                const data=await response.json();if(current!==sequence)return;results.replaceChildren();
                if(!data.products?.length)results.append(element('p',labels.empty,'text-sm'));
                (data.products||[]).forEach(p=>{
                    const button=element('button',p.name+' — '+(p.substances||[]).join(', '),'block w-full text-left border rounded-md p-2');
                    button.type='button';button.addEventListener('click',()=>{
                        if(!items.some(x=>x.id===p.id)){items.push(p);render();sync();}
                        search.value='';results.replaceChildren();
                    });results.append(button);
                });
            }catch(error){if(error.name!=='AbortError'&&current===sequence){results.replaceChildren(element('p',labels.unavailable,'text-sm text-red-600'));}}
        },300);
    });
    render();sync();
});

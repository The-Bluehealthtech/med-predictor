/* Relecture sûre et activation explicite : aucune donnée normale par défaut sauvegardée. */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-section-capture]').forEach(fieldset => {
        const checkbox=document.querySelector('input[name="capture['+fieldset.dataset.sectionCapture+']"]');
        if(checkbox) checkbox.addEventListener('change',()=>{
            fieldset.disabled=!checkbox.checked;
            if(checkbox.checked) fieldset.closest('details').open=true;
        });
    });
    const fieldsNode=document.getElementById('medical-section-fields');
    let sections={};
    try { sections=JSON.parse(fieldsNode?.textContent || '{}'); } catch (_) {}
    Object.entries(sections).forEach(([section,definition])=>{
        const names=Object.keys(definition.fields || {});
        Array.from(document.querySelectorAll('input,select,textarea')).forEach(el=>{
            const match=el.name.match(/^section_values\[([^\]]+)\]\[([^\]]+)\]$/);
            const field=match && match[1]===section?match[2]:el.name.replace(/\[\]$/, '');
            if(!names.includes(field)) return;
            el.addEventListener('change',()=>{
                // Les deux présentations du même champ restent synchronisées.
                const controls=Array.from(document.querySelectorAll('input,select,textarea'));
                if(match) {
                    controls.filter(other=>other.name===field || other.name===field+'[]').forEach(other=>{
                        if(other.type==='checkbox') other.checked=el.value.split('\n').includes(other.value);
                        else if(other.type!=='file') other.value=el.value;
                    });
                } else {
                    let value=el.type==='checkbox'?controls.filter(other=>other.name===el.name && other.checked).map(other=>other.value).join('\n'):el.value;
                    if(definition.fields[field]==='boolean') value=el.checked?'1':'0';
                    controls.filter(other=>other.name==='section_values['+section+']['+field+']').forEach(other=>{other.value=value;});
                }
                if(definition.fields[el.name]==='json') {
                    try { if(!Object.keys(JSON.parse(el.value || '{}')).length) return; } catch (_) { return; }
                }
                const check=document.querySelector('input[name="capture['+section+']"]');
                const set=document.querySelector('[data-section-capture="'+section+'"]');
                if(check && set) {check.checked=true;set.disabled=false;set.closest('details').open=true;}
            });
        });
    });
    const node=document.getElementById('medical-section-values');
    if(!node) return;
    let values;
    try { values=JSON.parse(node.textContent); } catch (_) { return; }
    Object.entries(values).forEach(([name,value])=>{
        if(value===null || value===undefined) return;
        const controls=Array.from(document.querySelectorAll('input,select,textarea')).filter(el=>el.name===name || el.name===name+'[]');
        controls.forEach(el=>{
            if(el.type==='file') return;
            if(el.type==='checkbox' || el.type==='radio') {
                el.checked=Array.isArray(value)?value.map(String).includes(el.value):String(value)===el.value;
            } else el.value=typeof value==='object'?JSON.stringify(value):String(value);
        });
    });
});

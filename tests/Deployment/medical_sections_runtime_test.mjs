import fs from 'node:fs';
import vm from 'node:vm';
import assert from 'node:assert/strict';
const control=(name,value='',type='text')=>({name,value,type,checked:false,events:{},addEventListener(k,fn){this.events[k]=fn;}});
const check=control('capture[mapa]','','checkbox');
const original=control('mapa_pas_24h','120');
const structured=control('section_values[mapa][mapa_pas_24h]','120');
const hidden=control('dental_data','{}','hidden');
const dental=control('capture[dental]','','checkbox');
const detail={open:false};
const dentalDetail={open:false};
const fieldset={dataset:{sectionCapture:'mapa'},disabled:true,closest(){return detail;}};
const dentalSet={dataset:{sectionCapture:'dental'},disabled:true,closest(){return dentalDetail;}};
const controls=[check,original,structured,hidden,dental];
let mounted;
const document={addEventListener(_,fn){mounted=fn;},querySelectorAll(selector){return selector==='[data-section-capture]'?[fieldset,dentalSet]:controls;},
querySelector(selector){return selector.includes('capture[mapa]')?check:selector.includes('capture[dental]')?dental:selector.includes('="mapa"')?fieldset:dentalSet;},
getElementById(id){return {textContent:JSON.stringify(id==='medical-section-fields'?{mapa:{fields:{mapa_pas_24h:'number'}},dental:{fields:{dental_data:'json'}}}:{})};}};
vm.runInNewContext(fs.readFileSync('public/js/medical-sections.js','utf8'),{document});mounted();
original.value='125';original.events.change();
assert.equal(structured.value,'125');assert.equal(check.checked,true);assert.equal(fieldset.disabled,false);assert.equal(detail.open,true);
structured.value='130';structured.events.change();assert.equal(original.value,'130');
hidden.events.change();assert.equal(dental.checked,false);
hidden.value='{"11":{"notes":"Fixture observation"}}';hidden.events.change();assert.equal(dental.checked,true);assert.equal(dentalSet.disabled,false);
check.checked=false;check.events.change();assert.equal(fieldset.disabled,true);
console.log('Activation, synchronisation des valeurs et absence de faux odontogramme : OK.');

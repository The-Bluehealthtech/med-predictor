/* Odontogramme FDI : données absentes non évaluées, aucune valeur clinique par défaut. */
(() => {
'use strict';
const ROWS = [[18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28],[48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38]];
const IDS = new Set(ROWS.flat().map(String));
const COLORS = {healthy:'#16805d',cavity:'#dc3545',filling:'#c68b17',crown:'#8255c5',missing:'#718096',implant:'#326295',treatment:'#d46b18'};
const FACES = ['mesial','distal','occlusal','lingual','buccal'];
const copy = value => JSON.parse(JSON.stringify(value));
const svgNS = 'http://www.w3.org/2000/svg';
function node(tag, attrs={}, text, svg=false) {
 const el=svg?document.createElementNS(svgNS,tag):document.createElement(tag);
 Object.entries(attrs).forEach(([key,value])=>el.setAttribute(key,String(value)));
 if(text!==undefined) el.textContent=String(text);
 return el;
}
function parse(value) { try { const v=JSON.parse(value || '{}'); return Array.isArray(v)?Object.fromEntries(v.map((item,index)=>[String(index),item])):(v && typeof v==='object'?v:{}); } catch (_) { return {}; } }
class Odontogram {
 constructor(host) {
  this.host=host;this.labels=parse(host.dataset.labels);
  this.readOnly=host.dataset.readonly==='true';this.field=host.querySelector('[data-dental-value]');
  this.data=parse(this.field?this.field.value:host.dataset.initial);
  this.legacyData=this.data._meta?.notation==='FDI'?null:(Object.keys(this.data).length?copy(this.data):null);
  if(this.legacyData)this.data={};
  this.selected=null;this.face=null;this.undo=[];this.filter='all';this.zoom=1;
  this.root=host.attachShadow({mode:'open'});this.build();this.paint();
  host.closest('form')?.addEventListener('click',e=>{if(e.target.closest('button[type="submit"],input[type="submit"]'))this.flush();},true);
  host.closest('form')?.addEventListener('submit',()=>this.flush());
 }
 t(key) {return this.labels[key] || key;}
 status(value) {return this.t(value || 'unevaluated');}
 build() {
  const style=node('style',{},`:host{display:block;font-family:inherit;color:#1f2937}*{box-sizing:border-box}button,input,select,textarea{font:inherit}button{cursor:pointer}button:disabled{cursor:default;opacity:.45}.shell{border:1px solid #dce3ed;border-radius:12px;background:#fff;overflow:hidden}.header{padding:18px 20px;background:linear-gradient(120deg,#edf4fc,#f8fafc);border-bottom:1px solid #dce3ed}h3{margin:0 0 6px;font-size:20px}p{margin:4px 0;font-size:13px;color:#64748b;line-height:1.5}.toolbar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-top:14px}button,select{border:1px solid #cbd5e1;border-radius:7px;background:#fff;padding:8px 10px;color:#334155}button:focus-visible,select:focus-visible,textarea:focus-visible,[role=button]:focus-visible{outline:3px solid #6ea4de;outline-offset:3px}.layout{display:grid;grid-template-columns:minmax(0,1fr) 285px}.chart{background:radial-gradient(ellipse,#fff 30%,#f1f5fa);padding:12px;overflow:auto}.chart svg{display:block;min-width:540px;width:100%;height:auto}.tooth{cursor:pointer;outline:none}.tooth .enamel{fill:#fff;stroke:#bdc8d6;stroke-width:1.7}.tooth .fissure{fill:none;stroke:#c0cbd7;stroke-width:1;pointer-events:none}.tooth text{font-size:13px;font-weight:600;fill:#526278;pointer-events:none}.tooth.selected .enamel,.tooth:focus .enamel{stroke:#326295;stroke-width:3}.tooth.selected text{fill:#326295}.tooth:hover .enamel{stroke:#6ea4de;stroke-width:3}.badge{stroke:white;stroke-width:1.5}.center-label{font-size:14px;fill:#7a899b;letter-spacing:1px}.editor{padding:18px;border-left:1px solid #dce3ed;background:#fff}.editor h4{margin:0 0 8px;font-size:19px}.editor label{display:block;font-size:13px;font-weight:600;margin:14px 0 6px}.editor select,.editor textarea{width:100%}.editor textarea{border:1px solid #cbd5e1;border-radius:7px;padding:10px;resize:vertical;min-height:80px}.primary{background:#326295;color:#fff;border-color:#326295;width:100%;margin-top:14px}.face-map{width:160px;height:135px;display:block;margin:10px auto}.face-map path{stroke:#b7c5d6;stroke-width:2;cursor:pointer}.face-map .active{stroke:#326295;stroke-width:4}.summary{display:flex;flex-wrap:wrap;gap:7px;padding:14px 18px;border-top:1px solid #dce3ed}.chip{font-size:12px;padding:5px 9px;border:1px solid #dce3ed;border-radius:30px;display:flex;align-items:center;gap:6px}.dot{width:8px;height:8px;border-radius:50%;display:inline-block}.list{padding:0 18px 16px;display:flex;gap:7px;flex-wrap:wrap}.list button{font-size:12px}.notice{padding:8px 18px;color:#64748b;font-size:12px}.message{min-height:35px}.empty{padding:22px 0}.legend{font-size:12px;color:#64748b;text-align:center}.face-label{font-size:11px;fill:#526278;pointer-events:none}@media(max-width:850px){.layout{grid-template-columns:1fr}.editor{border-left:0;border-top:1px solid #dce3ed}.chart svg{min-width:500px}.editor .face-map{margin-left:0}.toolbar button{padding:7px}.header{padding:15px}}@media print{.toolbar,.primary{display:none}.layout{grid-template-columns:1fr}.chart svg{min-width:0}}`);
  this.root.append(style);const shell=node('section',{class:'shell','aria-label':this.t('title')});this.root.append(shell);
  const header=node('header',{class:'header'});header.append(node('h3',{},this.t('title')),node('p',{},this.t('subtitle')));shell.append(header);
  const toolbar=node('div',{class:'toolbar'});header.append(toolbar);
  this.filterSelect=node('select',{'aria-label':this.t('filter')});['all','upper','lower','documented'].forEach(key=>this.filterSelect.append(node('option',{value:key},this.t(key))));toolbar.append(this.filterSelect);
  this.filterSelect.addEventListener('change',()=>{this.filter=this.filterSelect.value;this.paint();});
  [['−',-.15],['+',.15]].forEach(([label,delta])=>{const b=node('button',{type:'button','aria-label':this.t(delta>0?'zoom_in':'zoom_out')},label);b.addEventListener('click',()=>{this.zoom=Math.max(.75,Math.min(1.75,this.zoom+delta));this.svg.style.width=(this.zoom*100)+'%';});toolbar.append(b);});
  const reset=node('button',{type:'button'},this.t('fit'));reset.addEventListener('click',()=>{this.zoom=1;this.svg.style.width='100%';});toolbar.append(reset);
  if(!this.readOnly){this.undoButton=node('button',{type:'button',disabled:true},this.t('undo'));this.undoButton.addEventListener('click',()=>{if(this.undo.length){this.data=this.undo.pop();this.publish();this.paint();this.edit();}});toolbar.append(this.undoButton);}
  const layout=node('div',{class:'layout'});shell.append(layout);const chart=node('div',{class:'chart'});layout.append(chart);
  this.svg=node('svg',{viewBox:'0 0 820 570',role:'group','aria-label':this.t('chart')},undefined,true);chart.append(this.svg);this.teeth=new Map();
  ROWS.forEach((row,r)=>{
   row.forEach((id,i)=>{
    const angle=Math.PI-i*Math.PI/15,x=410+285*Math.cos(angle),y=r===0?245-180*Math.sin(angle):325+180*Math.sin(angle);
    const tooth=node('g',{class:'tooth',transform:`translate(${x} ${y})`,tabindex:0,role:'button','data-tooth':id},undefined,true);
    const type=id%10,w=type>=6?18:type>=4?16:12,rotation=`rotate(${r===0?-(i-7.5)*12:(i-7.5)*12})`;
    tooth.append(node('path',{class:'enamel',transform:rotation,d:`M ${-w} -15 Q ${-w-2} -26 0 -25 Q ${w+2} -26 ${w} -15 L ${w-2} 14 Q 0 23 ${-w+2} 14 Z`},undefined,true));
    tooth.append(node('path',{class:'fissure',transform:rotation,d:type>=4?'M -12 -8 Q 0 2 12 -8 M 0 -18 L 0 15 M -12 10 Q 0 2 12 10':'M -8 -14 Q 0 -19 8 -14 M -8 12 Q 0 17 8 12'},undefined,true));
    tooth.append(node('circle',{class:'badge',transform:rotation,cx:w-3,cy:-26,r:5,fill:'#d9e2ed'},undefined,true),node('text',{x:Math.cos(angle)*43,y:(r===0?-1:1)*Math.sin(angle)*43+5,'text-anchor':'middle'},id,true),node('title',{},'',true));
    const choose=()=>this.select(String(id));tooth.addEventListener('click',choose);tooth.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();choose();}else if(['ArrowRight','ArrowLeft'].includes(e.key)){e.preventDefault();const n=(i+(e.key==='ArrowRight'?1:15))%16;this.teeth.get(String(row[n]))?.focus();}});
    this.svg.append(tooth);this.teeth.set(String(id),tooth);
   });
  });
  this.svg.append(node('text',{x:410,y:220,'text-anchor':'middle',class:'center-label'},this.t('upper'),true),node('text',{x:410,y:356,'text-anchor':'middle',class:'center-label'},this.t('lower'),true),node('text',{x:88,y:287,'text-anchor':'middle',class:'center-label'},this.t('right'),true),node('text',{x:732,y:287,'text-anchor':'middle',class:'center-label'},this.t('left'),true));
  chart.append(node('p',{class:'legend'},this.t('orientation')));this.editor=node('aside',{class:'editor','aria-label':this.t('selection')});layout.append(this.editor);
  this.summary=node('div',{class:'summary'});this.list=node('div',{class:'list'});this.legacy=node('p',{class:'notice'});shell.append(this.summary,this.list,this.legacy);
  this.message=node('p',{class:'notice message',role:'status','aria-live':'polite'});shell.append(this.message);this.edit();
 }
 select(id) { this.flush();this.selected=id;this.face=null;this.paint();this.edit(); }
 edit() {
  this.editor.replaceChildren();if(!this.selected){this.editor.append(node('p',{class:'empty'},this.t('choose')));return;}
  const data=this.data[this.selected] || {};
  this.editor.append(node('h4',{},this.t('tooth')+' '+this.selected),node('p',{},this.t('quadrant_'+this.selected[0])));
  this.faceMap=node('svg',{viewBox:'0 0 150 130',class:'face-map',role:'group','aria-label':this.t('faces')},undefined,true);
  const paths={buccal:'M 15 10 H 135 L 100 45 H 50 Z',lingual:'M 15 120 H 135 L 100 85 H 50 Z',mesial:'M 15 10 L 50 45 V 85 L 15 120 Z',distal:'M 135 10 L 100 45 V 85 L 135 120 Z',occlusal:'M 50 45 H 100 V 85 H 50 Z'};
  FACES.forEach(face=>{const p=node('path',{d:paths[face],fill:COLORS[data.surfaces?.[face]?.status] || '#f1f5f9',tabindex:0,role:'button','aria-label':this.t(face),'data-face':face},undefined,true);p.append(node('title',{},this.t(face),true));const choose=()=>{this.flush();this.face=this.face===face?null:face;this.edit();};p.addEventListener('click',choose);p.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();choose();}});if(this.face===face)p.classList.add('active');this.faceMap.append(p);});
  this.editor.append(node('p',{},this.t('faces_hint')),this.faceMap);
  const faceButtons=node('div',{class:'toolbar'});
  FACES.forEach(face=>{const button=node('button',{type:'button','aria-pressed':String(this.face===face)},this.t(face));button.addEventListener('click',()=>{this.flush();this.face=this.face===face?null:face;this.edit();});faceButtons.append(button);});
  this.editor.append(faceButtons);
  const target=this.face?(data.surfaces?.[this.face] || {}):data;
  this.editor.append(node('p',{},this.face?this.t(this.face):this.t('whole_tooth')));
  if(this.readOnly){this.editor.append(node('p',{},this.status(target.status)),node('p',{},target.notes || this.t('no_notes')));if(data.lastUpdated)this.editor.append(node('p',{},this.t('updated')+' '+data.lastUpdated));return;}
  this.statusSelect=node('select',{id:'tooth-status'});this.statusSelect.append(node('option',{value:''},this.t('unevaluated')));Object.keys(COLORS).forEach(key=>this.statusSelect.append(node('option',{value:key},this.t(key))));
  if(target.status && !COLORS[target.status])this.statusSelect.append(node('option',{value:target.status},target.status));this.statusSelect.value=target.status || '';
  const statusLabel=node('label',{for:'tooth-status'},this.t('status'));this.editor.append(statusLabel,this.statusSelect);
  this.notes=node('textarea',{id:'tooth-notes',rows:3,placeholder:this.t('notes_hint')});this.notes.value=target.notes || '';
  this.editor.append(node('label',{for:'tooth-notes'},this.t('notes')),this.notes);
  const save=node('button',{type:'button',class:'primary'},this.t('apply'));save.addEventListener('click',()=>this.apply());this.editor.append(save,node('p',{},this.t('save_hint')));
 }
 flush() {
  if(this.readOnly || !this.selected || !this.statusSelect) return;
  const data=this.data[this.selected] || {},target=this.face?(data.surfaces?.[this.face] || {}):data;
  if((this.statusSelect.value!==(target.status || '') || this.notes.value.trim()!==(target.notes || '')) && (this.statusSelect.value || this.notes.value.trim())) this.apply();
 }
 apply() {
  const status=this.statusSelect.value,notes=this.notes.value.trim();
  if(!status&&!notes){this.message.textContent=this.t('empty_observation');return;}
  this.undo.push(copy(this.data));if(this.undo.length>20)this.undo.shift();
  const data=copy(this.data[this.selected] || {}),value={...(this.face?data.surfaces?.[this.face]:data),status:status || null,notes};
  if(this.face){data.surfaces={...(data.surfaces || {}),[this.face]:value};}else{Object.assign(data,value);}
  data.lastUpdated=new Date().toISOString();this.data[this.selected]=data;
  this.data._meta={notation:'FDI',version:1};
  if(this.legacyData)this.data._legacy=copy(this.legacyData);
  this.publish();this.paint();this.message.textContent=this.t('applied');
 }
 publish() {if(this.field){this.field.value=JSON.stringify(Object.keys(this.data).length?this.data:(this.legacyData || {}));this.field.dispatchEvent(new Event('change',{bubbles:true}));}}
 paint() {
  const counts={unevaluated:0};Object.keys(COLORS).forEach(key=>counts[key]=0);
  this.teeth.forEach((tooth,id)=>{
   const data=this.data[id],documented=!!data && (Boolean(data.status)||Boolean(data.notes)||Object.keys(data.surfaces || {}).length>0);
   const visible=this.filter==='all'||this.filter==='upper'&&['1','2'].includes(id[0])||this.filter==='lower'&&['3','4'].includes(id[0])||this.filter==='documented'&&documented;
   tooth.style.display=visible?'':'none';tooth.classList.toggle('selected',id===this.selected);
   const status=data?.status;counts[status in counts?status:'unevaluated']++;
   tooth.querySelector('.badge').setAttribute('fill',COLORS[status] || (documented?'#6ea4de':'#d9e2ed'));
   tooth.querySelector('.enamel').setAttribute('style',`fill:${COLORS[status]?COLORS[status]+'22':'#fff'};`);
   const label=this.t('tooth')+' '+id+' · '+this.status(status);tooth.setAttribute('aria-label',label);tooth.setAttribute('aria-pressed',String(id===this.selected));tooth.querySelector('title').textContent=label;
  });
  this.summary.replaceChildren();Object.entries(counts).forEach(([key,count])=>{const chip=node('span',{class:'chip'});chip.append(node('span',{class:'dot',style:'background:'+(COLORS[key] || '#d9e2ed')}),node('span',{},this.t(key)+' '+count));this.summary.append(chip);});
  this.list.replaceChildren();Object.entries(this.data).filter(([id])=>IDS.has(id)).forEach(([id,value])=>{const b=node('button',{type:'button'},id+' · '+this.status(value?.status));b.addEventListener('click',()=>{this.filter='all';this.filterSelect.value='all';this.select(id);});this.list.append(b);});
  const unknown=[...Object.keys(this.legacyData || this.data._legacy || {}),...Object.keys(this.data).filter(id=>!IDS.has(id) && !['_meta','_legacy'].includes(id))];this.legacy.textContent=unknown.length?this.t('legacy')+' '+unknown.join(', '):'';
  if(this.undoButton)this.undoButton.disabled=this.undo.length===0;
 }
}
window.FitOdontogram={mount:host=>host.shadowRoot?null:new Odontogram(host)};
const mount=()=>setTimeout(()=>document.querySelectorAll('[data-fit-odontogram]').forEach(host=>window.FitOdontogram.mount(host)),0);
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',mount);else mount();
})();

<div class="clinical-body-map rounded-xl border border-slate-200 bg-white overflow-hidden" id="clinical-body-map">
    <div class="px-4 py-3 border-b bg-slate-50 flex items-center justify-between gap-3">
        <div>
            <div class="text-sm font-semibold text-slate-900">Localisation sur le corps</div>
            <div class="text-xs text-slate-500">Sélection anatomique précise : région, sous-région et latéralité sont reportées dans le motif de consultation.</div>
        </div>
        <div class="inline-flex rounded-lg border bg-white p-1" role="group" aria-label="Vue anatomique">
            <button type="button" class="body-view-btn is-active" data-body-view="front" aria-pressed="true">Antérieure</button>
            <button type="button" class="body-view-btn" data-body-view="back" aria-pressed="false">Postérieure</button>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-[minmax(260px,390px)_1fr]">
        <div class="relative bg-gradient-to-b from-white to-slate-50 min-h-[470px]">
            <img class="body-map-image absolute inset-0 w-full h-full object-contain p-3" data-body-image="front"
                 src="{{ asset('images/postural/anterior-view.svg') }}" alt="Vue antérieure anatomique" draggable="false">
            <img class="body-map-image absolute inset-0 w-full h-full object-contain p-3 hidden" data-body-image="back"
                 src="{{ asset('images/postural/posterior-view.svg') }}" alt="Vue postérieure anatomique" draggable="false">

            <svg viewBox="0 0 812.94 946.13" preserveAspectRatio="xMidYMid meet" class="absolute inset-0 w-full h-full p-3 body-map-overlay">
                <g data-body-zones="front">
                    <ellipse class="body-zone" cx="406" cy="105" rx="58" ry="54" data-region="Tête / cou" data-detail="Tête" data-side="" tabindex="0"/>
                    <path class="body-zone" d="M365 158 Q406 144 447 158 L456 215 Q406 226 356 215 Z" data-region="Tête / cou" data-detail="Rachis cervical / cou" data-side="" tabindex="0"/>
                    <ellipse class="body-zone" cx="292" cy="249" rx="54" ry="39" data-region="Épaule" data-detail="Épaule" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="521" cy="249" rx="54" ry="39" data-region="Épaule" data-detail="Épaule" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="406" cy="315" rx="94" ry="72" data-region="Thorax" data-detail="Thorax antérieur" data-side="" tabindex="0"/>
                    <ellipse class="body-zone" cx="406" cy="410" rx="80" ry="55" data-region="Abdomen" data-detail="Abdomen" data-side="" tabindex="0"/>
                    <ellipse class="body-zone" cx="349" cy="478" rx="48" ry="42" data-region="Bassin / hanche" data-detail="Hanche / région inguinale" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="464" cy="478" rx="48" ry="42" data-region="Bassin / hanche" data-detail="Hanche / région inguinale" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="337" cy="575" rx="45" ry="70" data-region="Cuisse" data-detail="Cuisse antérieure" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="476" cy="575" rx="45" ry="70" data-region="Cuisse" data-detail="Cuisse antérieure" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="339" cy="704" rx="43" ry="42" data-region="Genou" data-detail="Genou antérieur" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="474" cy="704" rx="43" ry="42" data-region="Genou" data-detail="Genou antérieur" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="332" cy="790" rx="39" ry="60" data-region="Jambe" data-detail="Jambe antérieure" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="481" cy="790" rx="39" ry="60" data-region="Jambe" data-detail="Jambe antérieure" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="326" cy="857" rx="38" ry="28" data-region="Cheville" data-detail="Cheville" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="487" cy="857" rx="38" ry="28" data-region="Cheville" data-detail="Cheville" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="314" cy="904" rx="51" ry="27" data-region="Pied" data-detail="Pied" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="499" cy="904" rx="51" ry="27" data-region="Pied" data-detail="Pied" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="239" cy="335" rx="36" ry="65" data-region="Bras / coude" data-detail="Bras" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="574" cy="335" rx="36" ry="65" data-region="Bras / coude" data-detail="Bras" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="210" cy="425" rx="34" ry="34" data-region="Bras / coude" data-detail="Coude" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="603" cy="425" rx="34" ry="34" data-region="Bras / coude" data-detail="Coude" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="187" cy="494" rx="30" ry="49" data-region="Avant-bras" data-detail="Avant-bras" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="626" cy="494" rx="30" ry="49" data-region="Avant-bras" data-detail="Avant-bras" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="169" cy="550" rx="31" ry="35" data-region="Poignet / main" data-detail="Poignet / main" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="644" cy="550" rx="31" ry="35" data-region="Poignet / main" data-detail="Poignet / main" data-side="Droite" tabindex="0"/>
                </g>
                <g data-body-zones="back" class="hidden">
                    <ellipse class="body-zone" cx="406" cy="105" rx="58" ry="54" data-region="Tête / cou" data-detail="Occiput" data-side="" tabindex="0"/>
                    <path class="body-zone" d="M365 158 Q406 144 447 158 L456 215 Q406 226 356 215 Z" data-region="Tête / cou" data-detail="Rachis cervical / cou" data-side="" tabindex="0"/>
                    <ellipse class="body-zone" cx="292" cy="249" rx="54" ry="39" data-region="Épaule" data-detail="Épaule postérieure" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="521" cy="249" rx="54" ry="39" data-region="Épaule" data-detail="Épaule postérieure" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="345" cy="315" rx="56" ry="70" data-region="Dos / rachis" data-detail="Région scapulaire" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="468" cy="315" rx="56" ry="70" data-region="Dos / rachis" data-detail="Région scapulaire" data-side="Droite" tabindex="0"/>
                    <path class="body-zone" d="M382 225 Q406 215 430 225 L435 390 Q406 405 377 390 Z" data-region="Dos / rachis" data-detail="Rachis thoracique" data-side="" tabindex="0"/>
                    <path class="body-zone" d="M374 390 Q406 380 438 390 L448 493 Q406 510 364 493 Z" data-region="Dos / rachis" data-detail="Rachis lombaire" data-side="" tabindex="0"/>
                    <ellipse class="body-zone" cx="350" cy="490" rx="52" ry="44" data-region="Bassin / hanche" data-detail="Région fessière / hanche" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="463" cy="490" rx="52" ry="44" data-region="Bassin / hanche" data-detail="Région fessière / hanche" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="337" cy="575" rx="45" ry="70" data-region="Cuisse" data-detail="Cuisse postérieure" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="476" cy="575" rx="45" ry="70" data-region="Cuisse" data-detail="Cuisse postérieure" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="339" cy="704" rx="43" ry="42" data-region="Genou" data-detail="Creux poplité / genou postérieur" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="474" cy="704" rx="43" ry="42" data-region="Genou" data-detail="Creux poplité / genou postérieur" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="332" cy="790" rx="39" ry="60" data-region="Jambe" data-detail="Mollet" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="481" cy="790" rx="39" ry="60" data-region="Jambe" data-detail="Mollet" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="326" cy="857" rx="38" ry="28" data-region="Cheville" data-detail="Cheville postérieure / tendon d’Achille" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="487" cy="857" rx="38" ry="28" data-region="Cheville" data-detail="Cheville postérieure / tendon d’Achille" data-side="Droite" tabindex="0"/>
                    <ellipse class="body-zone" cx="314" cy="904" rx="51" ry="27" data-region="Pied" data-detail="Pied" data-side="Gauche" tabindex="0"/>
                    <ellipse class="body-zone" cx="499" cy="904" rx="51" ry="27" data-region="Pied" data-detail="Pied" data-side="Droite" tabindex="0"/>
                </g>
            </svg>
        </div>
        <div class="p-4 border-t md:border-t-0 md:border-l border-slate-200">
            <div class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Zone sélectionnée</div>
            <div id="body-map-selection" class="mt-1 text-base font-semibold text-slate-900" aria-live="polite">Aucune</div>
            <div id="body-map-detail" class="mt-1 text-xs text-blue-700 font-medium"></div>
            <input type="hidden" id="complaint-anatomical-detail" value="">
            <p class="text-xs text-slate-500 mt-2 leading-5">La carte est un raccourci de localisation. Les menus restent disponibles pour corriger ou préciser la sélection.</p>
            <button type="button" id="body-map-clear" class="mt-4 text-xs px-3 py-2 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50">Effacer la sélection</button>
        </div>
    </div>
</div>
<style>
#clinical-body-map .body-view-btn{padding:.35rem .65rem;border-radius:.4rem;font-size:.72rem;font-weight:600;color:#64748b}
#clinical-body-map .body-view-btn.is-active{background:#0f172a;color:#fff}
#clinical-body-map .body-map-image{pointer-events:none;user-select:none}
#clinical-body-map .body-map-overlay{z-index:2}
#clinical-body-map .body-zone{fill:rgba(37,99,235,0);stroke:rgba(37,99,235,0);stroke-width:4;cursor:pointer;transition:.15s}
#clinical-body-map .body-zone:hover,#clinical-body-map .body-zone:focus{fill:rgba(37,99,235,.14);stroke:rgba(37,99,235,.78);outline:none}
#clinical-body-map .body-zone.is-selected{fill:rgba(239,68,68,.22);stroke:#ef4444;stroke-width:5}
</style>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const root=document.getElementById('clinical-body-map'); if(!root)return;
 const region=document.getElementById('complaint-region'), side=document.getElementById('complaint-side'), label=root.querySelector('#body-map-selection'), detailLabel=root.querySelector('#body-map-detail'), detailInput=root.querySelector('#complaint-anatomical-detail');
 const activate=view=>{
   root.querySelectorAll('[data-body-image]').forEach(x=>x.classList.toggle('hidden',x.dataset.bodyImage!==view));
   root.querySelectorAll('[data-body-zones]').forEach(x=>x.classList.toggle('hidden',x.dataset.bodyZones!==view));
   root.querySelectorAll('.body-view-btn').forEach(x=>{const active=x.dataset.bodyView===view;x.classList.toggle('is-active',active);x.setAttribute('aria-pressed',active?'true':'false');});
 };
 root.querySelectorAll('.body-view-btn').forEach(b=>b.addEventListener('click',()=>activate(b.dataset.bodyView)));
 const selectZone=z=>{
   root.querySelectorAll('.body-zone').forEach(x=>x.classList.remove('is-selected')); z.classList.add('is-selected');
   if(region){region.value=z.dataset.region; region.dispatchEvent(new Event('change',{bubbles:true}));}
   if(side){side.value=z.dataset.side||''; side.dispatchEvent(new Event('change',{bubbles:true}));}
   label.textContent=z.dataset.region+(z.dataset.side?' · '+z.dataset.side:'');
   detailInput.value=z.dataset.detail||'';
   detailLabel.textContent=z.dataset.detail||'';
 };
 root.querySelectorAll('.body-zone').forEach(z=>{
   z.setAttribute('role','button');
   z.setAttribute('aria-label',[z.dataset.detail||z.dataset.region,z.dataset.side].filter(Boolean).join(', '));
   z.addEventListener('click',()=>selectZone(z));
   z.addEventListener('keydown',e=>{if(e.key==='Enter'||e.key===' '){e.preventDefault();selectZone(z);}});
 });
 root.querySelector('#body-map-clear').addEventListener('click',()=>{
   root.querySelectorAll('.body-zone').forEach(x=>x.classList.remove('is-selected')); label.textContent='Aucune'; detailLabel.textContent=''; detailInput.value='';
   if(region){region.value='';region.dispatchEvent(new Event('change',{bubbles:true}));}
   if(side){side.value='';side.dispatchEvent(new Event('change',{bubbles:true}));}
 });
 activate('front');
});
</script>
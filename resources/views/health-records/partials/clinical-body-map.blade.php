<div class="clinical-body-map rounded-xl border border-slate-200 bg-white overflow-hidden" id="clinical-body-map">
    <div class="px-4 py-3 border-b bg-slate-50 flex items-center justify-between gap-3">
        <div>
            <div class="text-sm font-semibold text-slate-900">Localisation sur le corps</div>
            <div class="text-xs text-slate-500">Cliquez sur une zone pour renseigner automatiquement la région et le côté.</div>
        </div>
        <div class="inline-flex rounded-lg border bg-white p-1">
            <button type="button" class="body-view-btn is-active" data-body-view="front">Face</button>
            <button type="button" class="body-view-btn" data-body-view="back">Dos</button>
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
                    <ellipse class="body-zone" cx="406" cy="115" rx="70" ry="72" data-region="Tête / cou" data-side=""/>
                    <ellipse class="body-zone" cx="294" cy="252" rx="55" ry="42" data-region="Épaule" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="519" cy="252" rx="55" ry="42" data-region="Épaule" data-side="Droite"/>
                    <ellipse class="body-zone" cx="406" cy="330" rx="100" ry="88" data-region="Thorax" data-side=""/>
                    <ellipse class="body-zone" cx="406" cy="455" rx="90" ry="62" data-region="Bassin / hanche" data-side=""/>
                    <ellipse class="body-zone" cx="337" cy="548" rx="52" ry="72" data-region="Cuisse" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="476" cy="548" rx="52" ry="72" data-region="Cuisse" data-side="Droite"/>
                    <ellipse class="body-zone" cx="339" cy="706" rx="46" ry="45" data-region="Genou" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="474" cy="706" rx="46" ry="45" data-region="Genou" data-side="Droite"/>
                    <ellipse class="body-zone" cx="332" cy="795" rx="43" ry="70" data-region="Jambe" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="481" cy="795" rx="43" ry="70" data-region="Jambe" data-side="Droite"/>
                    <ellipse class="body-zone" cx="324" cy="882" rx="48" ry="42" data-region="Cheville" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="489" cy="882" rx="48" ry="42" data-region="Cheville" data-side="Droite"/>
                    <ellipse class="body-zone" cx="220" cy="390" rx="42" ry="88" data-region="Bras / coude" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="593" cy="390" rx="42" ry="88" data-region="Bras / coude" data-side="Droite"/>
                    <ellipse class="body-zone" cx="177" cy="510" rx="38" ry="70" data-region="Poignet / main" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="636" cy="510" rx="38" ry="70" data-region="Poignet / main" data-side="Droite"/>
                </g>
                <g data-body-zones="back" class="hidden">
                    <ellipse class="body-zone" cx="406" cy="115" rx="70" ry="72" data-region="Tête / cou" data-side=""/>
                    <ellipse class="body-zone" cx="294" cy="252" rx="55" ry="42" data-region="Épaule" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="519" cy="252" rx="55" ry="42" data-region="Épaule" data-side="Droite"/>
                    <ellipse class="body-zone" cx="406" cy="340" rx="105" ry="100" data-region="Dos / rachis" data-side=""/>
                    <ellipse class="body-zone" cx="406" cy="480" rx="92" ry="65" data-region="Bassin / hanche" data-side=""/>
                    <ellipse class="body-zone" cx="337" cy="548" rx="52" ry="72" data-region="Cuisse" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="476" cy="548" rx="52" ry="72" data-region="Cuisse" data-side="Droite"/>
                    <ellipse class="body-zone" cx="339" cy="706" rx="46" ry="45" data-region="Genou" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="474" cy="706" rx="46" ry="45" data-region="Genou" data-side="Droite"/>
                    <ellipse class="body-zone" cx="332" cy="795" rx="43" ry="70" data-region="Jambe" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="481" cy="795" rx="43" ry="70" data-region="Jambe" data-side="Droite"/>
                    <ellipse class="body-zone" cx="324" cy="882" rx="48" ry="42" data-region="Cheville" data-side="Gauche"/>
                    <ellipse class="body-zone" cx="489" cy="882" rx="48" ry="42" data-region="Cheville" data-side="Droite"/>
                </g>
            </svg>
        </div>
        <div class="p-4 border-t md:border-t-0 md:border-l border-slate-200">
            <div class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Zone sélectionnée</div>
            <div id="body-map-selection" class="mt-1 text-base font-semibold text-slate-900">Aucune</div>
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
#clinical-body-map .body-zone:hover{fill:rgba(37,99,235,.16);stroke:rgba(37,99,235,.7)}
#clinical-body-map .body-zone.is-selected{fill:rgba(239,68,68,.22);stroke:#ef4444;stroke-width:5}
</style>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const root=document.getElementById('clinical-body-map'); if(!root)return;
 const region=document.getElementById('complaint-region'), side=document.getElementById('complaint-side'), label=root.querySelector('#body-map-selection');
 const activate=view=>{
   root.querySelectorAll('[data-body-image]').forEach(x=>x.classList.toggle('hidden',x.dataset.bodyImage!==view));
   root.querySelectorAll('[data-body-zones]').forEach(x=>x.classList.toggle('hidden',x.dataset.bodyZones!==view));
   root.querySelectorAll('.body-view-btn').forEach(x=>x.classList.toggle('is-active',x.dataset.bodyView===view));
 };
 root.querySelectorAll('.body-view-btn').forEach(b=>b.addEventListener('click',()=>activate(b.dataset.bodyView)));
 root.querySelectorAll('.body-zone').forEach(z=>z.addEventListener('click',()=>{
   root.querySelectorAll('.body-zone').forEach(x=>x.classList.remove('is-selected')); z.classList.add('is-selected');
   if(region){region.value=z.dataset.region; region.dispatchEvent(new Event('change',{bubbles:true}));}
   if(side){side.value=z.dataset.side||''; side.dispatchEvent(new Event('change',{bubbles:true}));}
   label.textContent=z.dataset.region+(z.dataset.side?' · '+z.dataset.side:'');
 }));
 root.querySelector('#body-map-clear').addEventListener('click',()=>{
   root.querySelectorAll('.body-zone').forEach(x=>x.classList.remove('is-selected')); label.textContent='Aucune';
   if(region){region.value='';region.dispatchEvent(new Event('change',{bubbles:true}));}
   if(side){side.value='';side.dispatchEvent(new Event('change',{bubbles:true}));}
 });
 activate('front');
});
</script>
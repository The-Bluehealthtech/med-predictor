<div class="border border-slate-200 rounded-2xl bg-slate-50 p-4 md:p-5" id="postural-axis-map">
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4 mb-5">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex w-8 h-8 items-center justify-center rounded-lg bg-blue-50 text-blue-700">◎</span>
                <div>
                    <h4 class="font-semibold text-gray-900">Planche anatomique posturale</h4>
                    <p class="text-xs text-gray-500 mt-0.5">Repères anatomiques et axes de mesure — cliquez sur un axe pour préparer la mesure.</p>
                </div>
            </div>
        </div>
        <div class="inline-flex flex-wrap rounded-xl border border-slate-200 bg-white p-1 gap-1 shadow-sm">
            <button type="button" class="posture-view-btn" data-view="anterior">Antérieure</button>
            <button type="button" class="posture-view-btn" data-view="posterior">Postérieure</button>
            <button type="button" class="posture-view-btn" data-view="left_lateral">Profil G</button>
            <button type="button" class="posture-view-btn" data-view="right_lateral">Profil D</button>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(430px,620px)_minmax(280px,1fr)] gap-5">
        <div class="posture-stage">
            <div class="posture-stage-header">
                <div>
                    <span id="postural-view-label" class="text-sm font-semibold text-slate-800">Vue antérieure</span>
                    <span class="ml-2 text-xs text-slate-400">Repères normalisés</span>
                </div>
                <button type="button" id="posture-toggle-guides" class="text-xs px-2.5 py-1.5 rounded-lg border bg-white text-slate-600">Masquer les guides</button>
            </div>

            <div class="posture-canvas">
                <img id="posture-anatomy-image"
                     src="{{ asset('images/postural/anterior-view.svg') }}"
                     alt="Planche anatomique posturale"
                     class="posture-anatomy-image"
                     draggable="false">

                <svg viewBox="0 0 812.94 946.13" preserveAspectRatio="xMidYMid meet"
                     class="posture-overlay" id="postural-axis-svg" aria-label="Repères et axes posturaux">
                    <g class="posture-guides">
                        <line x1="406.47" y1="62" x2="406.47" y2="906" class="posture-plumb"/>
                        <line x1="170" y1="252" x2="643" y2="252" class="posture-reference"/>
                        <line x1="190" y1="493" x2="623" y2="493" class="posture-reference"/>
                        <line x1="230" y1="706" x2="583" y2="706" class="posture-reference"/>
                    </g>

                    <g class="posture-view-group" data-view-group="anterior">
                        <line x1="276" y1="252" x2="537" y2="252" class="posture-axis axis-blue" data-measurement="shoulder_line_angle" data-view="anterior"/>
                        <line x1="326" y1="493" x2="487" y2="493" class="posture-axis axis-violet" data-measurement="pelvic_line_angle" data-view="anterior"/>
                        <polyline points="326,493 339,706 327,866" class="posture-axis axis-red" data-measurement="knee_frontal_angle" data-view="anterior" data-side="left"/>
                        <polyline points="487,493 474,706 486,866" class="posture-axis axis-red" data-measurement="knee_frontal_angle" data-view="anterior" data-side="right"/>
                        <line x1="354" y1="159" x2="459" y2="159" class="posture-axis axis-green" data-measurement="head_tilt_angle" data-view="anterior"/>
                        <g class="posture-landmarks">
                            <circle cx="276" cy="252" r="8"/><circle cx="537" cy="252" r="8"/>
                            <circle cx="326" cy="493" r="8"/><circle cx="487" cy="493" r="8"/>
                            <circle cx="339" cy="706" r="8"/><circle cx="474" cy="706" r="8"/>
                            <circle cx="327" cy="866" r="8"/><circle cx="486" cy="866" r="8"/>
                        </g>
                    </g>

                    <g class="posture-view-group hidden" data-view-group="posterior">
                        <line x1="276" y1="252" x2="537" y2="252" class="posture-axis axis-blue" data-measurement="shoulder_line_angle" data-view="posterior"/>
                        <line x1="326" y1="493" x2="487" y2="493" class="posture-axis axis-violet" data-measurement="pelvic_line_angle" data-view="posterior"/>
                        <path d="M406 214 C398 300 416 356 402 425 C397 454 407 476 406 508" class="posture-axis axis-orange" data-measurement="spinal_lateral_offset" data-view="posterior" fill="none"/>
                        <line x1="328" y1="310" x2="406" y2="310" class="posture-axis axis-teal" data-measurement="scapular_distance" data-view="posterior" data-side="left"/>
                        <line x1="485" y1="310" x2="406" y2="310" class="posture-axis axis-teal" data-measurement="scapular_distance" data-view="posterior" data-side="right"/>
                        <g class="posture-landmarks">
                            <circle cx="276" cy="252" r="8"/><circle cx="537" cy="252" r="8"/>
                            <circle cx="328" cy="310" r="8"/><circle cx="485" cy="310" r="8"/>
                            <circle cx="326" cy="493" r="8"/><circle cx="487" cy="493" r="8"/>
                        </g>
                    </g>

                    <g class="posture-view-group hidden" data-view-group="left_lateral">
                        <line x1="385" y1="159" x2="373" y2="253" class="posture-axis axis-green" data-measurement="head_forward_offset" data-view="left_lateral" data-side="midline"/>
                        <polyline points="373,253 401,345 385,475" class="posture-axis axis-orange" data-measurement="thoracic_kyphosis_angle" data-view="left_lateral"/>
                        <polyline points="401,345 385,475 370,535" class="posture-axis axis-violet" data-measurement="lumbar_lordosis_angle" data-view="left_lateral"/>
                        <line x1="351" y1="493" x2="416" y2="476" class="posture-axis axis-violet" data-measurement="pelvic_tilt_angle" data-view="left_lateral" data-side="midline"/>
                        <polyline points="385,475 381,706 367,866" class="posture-axis axis-red" data-measurement="knee_sagittal_angle" data-view="left_lateral" data-side="left"/>
                        <g class="posture-landmarks">
                            <circle cx="385" cy="159" r="8"/><circle cx="373" cy="253" r="8"/>
                            <circle cx="385" cy="475" r="8"/><circle cx="381" cy="706" r="8"/><circle cx="367" cy="866" r="8"/>
                        </g>
                    </g>

                    <g class="posture-view-group hidden" data-view-group="right_lateral">
                        <line x1="428" y1="159" x2="440" y2="253" class="posture-axis axis-green" data-measurement="head_forward_offset" data-view="right_lateral" data-side="midline"/>
                        <polyline points="440,253 412,345 428,475" class="posture-axis axis-orange" data-measurement="thoracic_kyphosis_angle" data-view="right_lateral"/>
                        <polyline points="412,345 428,475 443,535" class="posture-axis axis-violet" data-measurement="lumbar_lordosis_angle" data-view="right_lateral"/>
                        <line x1="462" y1="493" x2="397" y2="476" class="posture-axis axis-violet" data-measurement="pelvic_tilt_angle" data-view="right_lateral" data-side="midline"/>
                        <polyline points="428,475 432,706 446,866" class="posture-axis axis-red" data-measurement="knee_sagittal_angle" data-view="right_lateral" data-side="right"/>
                        <g class="posture-landmarks">
                            <circle cx="428" cy="159" r="8"/><circle cx="440" cy="253" r="8"/>
                            <circle cx="428" cy="475" r="8"/><circle cx="432" cy="706" r="8"/><circle cx="446" cy="866" r="8"/>
                        </g>
                    </g>
                </svg>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <div class="text-[11px] uppercase tracking-wider text-slate-400 font-semibold">Sélection clinique</div>
                <div id="postural-axis-selected" class="font-semibold text-slate-900 mt-1">Aucun axe sélectionné</div>
                <div id="postural-axis-hint" class="text-xs text-slate-500 mt-2">Sélectionnez directement une ligne colorée sur la planche.</div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4">
                <h5 class="text-sm font-semibold text-slate-800 mb-3">Repères disponibles</h5>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-1 2xl:grid-cols-2 gap-2 text-xs text-slate-600">
                    <div class="posture-key"><span class="key-dot bg-blue-600"></span>Épaules</div>
                    <div class="posture-key"><span class="key-dot bg-violet-600"></span>Bassin</div>
                    <div class="posture-key"><span class="key-dot bg-red-600"></span>Genoux</div>
                    <div class="posture-key"><span class="key-dot bg-emerald-600"></span>Tête</div>
                    <div class="posture-key"><span class="key-dot bg-orange-600"></span>Rachis</div>
                    <div class="posture-key"><span class="key-dot bg-teal-600"></span>Scapulas</div>
                </div>
            </div>

            <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-4">
                <div class="text-xs font-semibold text-blue-900 mb-1">Lecture de la planche</div>
                <p class="text-xs leading-5 text-blue-800">
                    L’illustration anatomique sert de support de repérage. Les lignes sont des guides de mesure et ne produisent ni diagnostic automatique ni seuil de sévérité.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
#postural-axis-map .posture-view-btn{padding:.5rem .8rem;border-radius:.55rem;font-size:.75rem;font-weight:600;color:#475569;transition:.15s}
#postural-axis-map .posture-view-btn:hover{background:#f1f5f9;color:#0f172a}
#postural-axis-map .posture-view-btn.is-active{background:#0f172a;color:#fff;box-shadow:0 1px 2px rgba(15,23,42,.18)}
#postural-axis-map .posture-stage{overflow:hidden;border:1px solid #e2e8f0;border-radius:1rem;background:#fff;box-shadow:0 1px 2px rgba(15,23,42,.04)}
#postural-axis-map .posture-stage-header{display:flex;align-items:center;justify-content:space-between;padding:.75rem 1rem;border-bottom:1px solid #e2e8f0;background:#f8fafc}
#postural-axis-map .posture-canvas{position:relative;max-width:620px;margin:0 auto;background:linear-gradient(180deg,#fff,#f8fafc);aspect-ratio:812.94/946.13}
#postural-axis-map .posture-anatomy-image,#postural-axis-map .posture-overlay{position:absolute;inset:0;width:100%;height:100%;object-fit:contain}
#postural-axis-map .posture-anatomy-image{user-select:none;pointer-events:none}
#postural-axis-map .posture-overlay{z-index:2}
#postural-axis-map .posture-plumb{stroke:#0ea5e9;stroke-width:2.5;stroke-dasharray:10 8;opacity:.8}
#postural-axis-map .posture-reference{stroke:#ef4444;stroke-width:1.5;stroke-dasharray:7 7;opacity:.42}
#postural-axis-map .posture-axis{fill:none;stroke-width:6;stroke-linecap:round;stroke-linejoin:round;cursor:pointer;opacity:.86;transition:opacity .15s,stroke-width .15s,filter .15s}
#postural-axis-map .posture-axis:hover{opacity:1;stroke-width:10;filter:drop-shadow(0 1px 3px rgba(15,23,42,.28))}
#postural-axis-map .posture-axis.is-selected{opacity:1;stroke-width:11;filter:drop-shadow(0 0 5px rgba(15,23,42,.45))}
#postural-axis-map .axis-blue{stroke:#2563eb}.axis-violet{stroke:#7c3aed}.axis-red{stroke:#dc2626}.axis-green{stroke:#059669}.axis-orange{stroke:#ea580c}.axis-teal{stroke:#0f766e}
#postural-axis-map .posture-landmarks circle{fill:#fff;stroke:#2563eb;stroke-width:3;filter:drop-shadow(0 1px 2px rgba(15,23,42,.2));pointer-events:none}
#postural-axis-map .posture-key{display:flex;align-items:center;gap:.5rem;padding:.45rem .55rem;border-radius:.5rem;background:#f8fafc}
#postural-axis-map .key-dot{width:.65rem;height:.65rem;border-radius:999px;flex:none}
#postural-axis-map.guides-hidden .posture-guides{display:none}
@media(max-width:640px){#postural-axis-map .posture-canvas{min-height:520px}#postural-axis-map .posture-axis{stroke-width:8}}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('postural-axis-map');
    if (!root) return;

    const selected = root.querySelector('#postural-axis-selected');
    const hint = root.querySelector('#postural-axis-hint');
    const image = root.querySelector('#posture-anatomy-image');
    const viewLabel = root.querySelector('#postural-view-label');
    const groups = root.querySelectorAll('[data-view-group]');
    const buttons = root.querySelectorAll('.posture-view-btn');
    const guideToggle = root.querySelector('#posture-toggle-guides');

    const labels = {
        shoulder_line_angle: 'Ligne des épaules',
        pelvic_line_angle: 'Ligne du bassin',
        knee_frontal_angle: 'Axe frontal du genou',
        knee_sagittal_angle: 'Axe sagittal du genou',
        head_tilt_angle: 'Inclinaison de la tête',
        head_forward_offset: 'Projection antérieure de la tête',
        spinal_lateral_offset: 'Déviation latérale du rachis',
        scapular_distance: 'Repère scapulaire',
        thoracic_kyphosis_angle: 'Courbure thoracique',
        lumbar_lordosis_angle: 'Courbure lombaire',
        pelvic_tilt_angle: 'Inclinaison sagittale du bassin'
    };
    const views = {
        anterior: {label:'Vue antérieure',src:"{{ asset('images/postural/anterior-view.svg') }}"},
        posterior: {label:'Vue postérieure',src:"{{ asset('images/postural/posterior-view.svg') }}"},
        left_lateral: {label:'Profil gauche',src:"{{ asset('images/postural/lateral-view.svg') }}"},
        right_lateral: {label:'Profil droit',src:"{{ asset('images/postural/lateral-view.svg') }}"}
    };

    const activateView = view => {
        groups.forEach(group => group.classList.toggle('hidden', group.dataset.viewGroup !== view));
        buttons.forEach(button => button.classList.toggle('is-active', button.dataset.view === view));
        image.src = views[view].src;
        image.style.transform = view === 'right_lateral' ? 'scaleX(-1)' : '';
        viewLabel.textContent = views[view].label;
        selected.textContent = 'Aucun axe sélectionné';
        hint.textContent = 'Sélectionnez directement une ligne colorée sur la planche.';
        root.querySelectorAll('.posture-axis').forEach(el => el.classList.remove('is-selected'));
    };

    buttons.forEach(button => button.addEventListener('click', () => activateView(button.dataset.view)));

    root.querySelectorAll('.posture-axis').forEach(axis => {
        axis.addEventListener('click', event => {
            event.stopPropagation();
            root.querySelectorAll('.posture-axis').forEach(el => el.classList.remove('is-selected'));
            axis.classList.add('is-selected');
            const detail = {measurement_key:axis.dataset.measurement,view:axis.dataset.view,side:axis.dataset.side||null};
            selected.textContent=(labels[detail.measurement_key]||detail.measurement_key)+(detail.side&&detail.side!=='midline'?' · '+(detail.side==='left'?'gauche':'droite'):'');
            hint.textContent='Vue : '+views[detail.view].label+'. Cliquez à nouveau sur un autre axe pour changer de mesure.';
            root.dispatchEvent(new CustomEvent('postural-axis-selected',{bubbles:true,detail}));
        });
    });

    guideToggle.addEventListener('click', () => {
        root.classList.toggle('guides-hidden');
        guideToggle.textContent = root.classList.contains('guides-hidden') ? 'Afficher les guides' : 'Masquer les guides';
    });

    activateView('anterior');
});
</script>

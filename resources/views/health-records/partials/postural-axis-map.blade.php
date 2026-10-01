<div class="border border-gray-200 rounded-xl bg-gray-50 p-4" id="postural-axis-map">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h4 class="font-semibold text-gray-900">Schéma postural interactif</h4>
            <p class="text-xs text-gray-500">Cliquez sur un axe anatomique pour préparer la mesure correspondante.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" class="posture-view-btn px-3 py-1 text-xs rounded bg-gray-900 text-white" data-view="anterior">Antérieure</button>
            <button type="button" class="posture-view-btn px-3 py-1 text-xs rounded bg-white border" data-view="posterior">Postérieure</button>
            <button type="button" class="posture-view-btn px-3 py-1 text-xs rounded bg-white border" data-view="left_lateral">Profil G</button>
            <button type="button" class="posture-view-btn px-3 py-1 text-xs rounded bg-white border" data-view="right_lateral">Profil D</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(280px,380px)_1fr] gap-5">
        <div class="bg-white border rounded-xl p-3">
            <svg viewBox="0 0 300 560" class="w-full h-auto" id="postural-axis-svg" aria-label="Schéma des axes posturaux">
                <line x1="150" y1="25" x2="150" y2="530" stroke="#94a3b8" stroke-width="2" stroke-dasharray="6 6" class="posture-plumb"/>
                <circle cx="150" cy="55" r="30" fill="#f8fafc" stroke="#64748b" stroke-width="2"/>
                <rect x="136" y="86" width="28" height="24" rx="10" fill="#f8fafc" stroke="#64748b" stroke-width="2"/>
                <path d="M105 110 Q150 92 195 110 L207 235 Q190 270 172 296 L128 296 Q110 270 93 235 Z"
                      fill="#f8fafc" stroke="#64748b" stroke-width="2"/>
                <path d="M105 120 L67 184 L56 270" fill="none" stroke="#64748b" stroke-width="24" stroke-linecap="round"/>
                <path d="M195 120 L233 184 L244 270" fill="none" stroke="#64748b" stroke-width="24" stroke-linecap="round"/>
                <path d="M132 296 L117 405 L108 530" fill="none" stroke="#64748b" stroke-width="30" stroke-linecap="round"/>
                <path d="M168 296 L183 405 L192 530" fill="none" stroke="#64748b" stroke-width="30" stroke-linecap="round"/>

                <g class="posture-view-group" data-view-group="anterior">
                    <line x1="105" y1="126" x2="195" y2="126" class="posture-axis" data-measurement="shoulder_line_angle" data-view="anterior" stroke="#2563eb" stroke-width="5"/>
                    <line x1="120" y1="276" x2="180" y2="276" class="posture-axis" data-measurement="pelvic_line_angle" data-view="anterior" stroke="#7c3aed" stroke-width="5"/>
                    <polyline points="132,320 120,405 110,488" class="posture-axis" data-measurement="knee_frontal_angle" data-view="anterior" data-side="left" fill="none" stroke="#dc2626" stroke-width="5"/>
                    <polyline points="168,320 180,405 190,488" class="posture-axis" data-measurement="knee_frontal_angle" data-view="anterior" data-side="right" fill="none" stroke="#dc2626" stroke-width="5"/>
                    <line x1="120" y1="80" x2="180" y2="80" class="posture-axis" data-measurement="head_tilt_angle" data-view="anterior" stroke="#059669" stroke-width="5"/>
                </g>

                <g class="posture-view-group hidden" data-view-group="posterior">
                    <line x1="105" y1="126" x2="195" y2="126" class="posture-axis" data-measurement="shoulder_line_angle" data-view="posterior" stroke="#2563eb" stroke-width="5"/>
                    <line x1="120" y1="276" x2="180" y2="276" class="posture-axis" data-measurement="pelvic_line_angle" data-view="posterior" stroke="#7c3aed" stroke-width="5"/>
                    <line x1="150" y1="110" x2="150" y2="290" class="posture-axis" data-measurement="spinal_offset" data-view="posterior" stroke="#ea580c" stroke-width="5"/>
                    <polyline points="116,118 104,146 96,175" class="posture-axis" data-measurement="scapular_distance" data-view="posterior" data-side="left" fill="none" stroke="#0f766e" stroke-width="5"/>
                    <polyline points="184,118 196,146 204,175" class="posture-axis" data-measurement="scapular_distance" data-view="posterior" data-side="right" fill="none" stroke="#0f766e" stroke-width="5"/>
                </g>

                <g class="posture-view-group hidden" data-view-group="left_lateral">
                    <line x1="150" y1="52" x2="150" y2="128" class="posture-axis" data-measurement="head_forward_offset" data-view="left_lateral" data-side="midline" stroke="#059669" stroke-width="5"/>
                    <polyline points="146,115 160,190 150,272" class="posture-axis" data-measurement="thoracic_kyphosis_angle" data-view="left_lateral" fill="none" stroke="#ea580c" stroke-width="5"/>
                    <polyline points="150,220 137,258 150,298" class="posture-axis" data-measurement="lumbar_lordosis_angle" data-view="left_lateral" fill="none" stroke="#9333ea" stroke-width="5"/>
                    <line x1="132" y1="276" x2="171" y2="265" class="posture-axis" data-measurement="pelvic_tilt_angle" data-view="left_lateral" data-side="midline" stroke="#7c3aed" stroke-width="5"/>
                    <polyline points="150,320 120,405 110,488" class="posture-axis" data-measurement="knee_sagittal_angle" data-view="left_lateral" data-side="left" fill="none" stroke="#dc2626" stroke-width="5"/>
                </g>

                <g class="posture-view-group hidden" data-view-group="right_lateral">
                    <line x1="150" y1="52" x2="150" y2="128" class="posture-axis" data-measurement="head_forward_offset" data-view="right_lateral" data-side="midline" stroke="#059669" stroke-width="5"/>
                    <polyline points="146,115 160,190 150,272" class="posture-axis" data-measurement="thoracic_kyphosis_angle" data-view="right_lateral" fill="none" stroke="#ea580c" stroke-width="5"/>
                    <polyline points="150,220 137,258 150,298" class="posture-axis" data-measurement="lumbar_lordosis_angle" data-view="right_lateral" fill="none" stroke="#9333ea" stroke-width="5"/>
                    <line x1="132" y1="276" x2="171" y2="265" class="posture-axis" data-measurement="pelvic_tilt_angle" data-view="right_lateral" data-side="midline" stroke="#7c3aed" stroke-width="5"/>
                    <polyline points="150,320 180,405 190,488" class="posture-axis" data-measurement="knee_sagittal_angle" data-view="right_lateral" data-side="right" fill="none" stroke="#dc2626" stroke-width="5"/>
                </g>
            </svg>
        </div>

        <div class="space-y-3">
            <div class="border rounded-xl bg-white p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-medium mb-1">Axe sélectionné</div>
                <div id="postural-axis-selected" class="font-semibold text-gray-900">Aucun axe sélectionné</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-600"></span> Ligne des épaules</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-purple-600"></span> Axe du bassin</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-600"></span> Axe des genoux</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-600"></span> Tête / projection</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-orange-600"></span> Rachis</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-slate-400"></span> Fil à plomb</div>
            </div>
            <p class="text-xs text-gray-500">
                Le clic sélectionne un protocole de mesure ; l’interprétation clinique reste séparée et doit être saisie/validée par le clinicien.
            </p>
        </div>
    </div>
</div>

<style>
#postural-axis-map .posture-axis { cursor: pointer; transition: opacity .15s, stroke-width .15s; }
#postural-axis-map .posture-axis:hover { opacity: .65; stroke-width: 8; }
#postural-axis-map .posture-axis.is-selected { stroke-width: 9; filter: drop-shadow(0 0 3px rgba(15,23,42,.35)); }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('postural-axis-map');
    if (!root) return;

    const selected = document.getElementById('postural-axis-selected');
    const groups = root.querySelectorAll('[data-view-group]');
    const buttons = root.querySelectorAll('.posture-view-btn');

    const labels = {
        shoulder_line_angle: 'Ligne des épaules',
        pelvic_line_angle: 'Ligne du bassin',
        knee_frontal_angle: 'Axe frontal du genou',
        knee_sagittal_angle: 'Axe sagittal du genou',
        head_tilt_angle: 'Inclinaison de la tête',
        head_forward_offset: 'Projection antérieure de la tête',
        spinal_offset: 'Déviation de l’axe rachidien',
        scapular_distance: 'Position scapulaire',
        thoracic_kyphosis_angle: 'Courbure thoracique',
        lumbar_lordosis_angle: 'Courbure lombaire',
        pelvic_tilt_angle: 'Inclinaison sagittale du bassin'
    };

    buttons.forEach(button => button.addEventListener('click', () => {
        const view = button.dataset.view;
        groups.forEach(group => group.classList.toggle('hidden', group.dataset.viewGroup !== view));
        buttons.forEach(b => {
            const active = b === button;
            b.classList.toggle('bg-gray-900', active);
            b.classList.toggle('text-white', active);
            b.classList.toggle('bg-white', !active);
            b.classList.toggle('border', !active);
        });
    }));

    root.querySelectorAll('.posture-axis').forEach(axis => {
        axis.addEventListener('click', () => {
            root.querySelectorAll('.posture-axis').forEach(el => el.classList.remove('is-selected'));
            axis.classList.add('is-selected');
            const detail = {
                measurement_key: axis.dataset.measurement,
                view: axis.dataset.view,
                side: axis.dataset.side || null
            };
            selected.textContent = (labels[detail.measurement_key] || detail.measurement_key) +
                ' · ' + detail.view.replaceAll('_', ' ') +
                (detail.side ? ' · ' + detail.side : '');
            root.dispatchEvent(new CustomEvent('postural-axis-selected', { bubbles: true, detail }));
        });
    });
});
</script>

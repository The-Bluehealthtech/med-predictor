<div class="border border-gray-200 rounded-xl bg-gray-50 p-4" id="postural-axis-map">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-4">
        <div>
            <h4 class="font-semibold text-gray-900">Schéma postural interactif</h4>
            <p class="text-xs text-gray-500">Sélectionnez un axe ou un repère anatomique pour préparer une mesure guidée.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" class="posture-view-btn px-3 py-1 text-xs rounded bg-gray-900 text-white" data-view="anterior">Antérieure</button>
            <button type="button" class="posture-view-btn px-3 py-1 text-xs rounded bg-white border" data-view="posterior">Postérieure</button>
            <button type="button" class="posture-view-btn px-3 py-1 text-xs rounded bg-white border" data-view="left_lateral">Profil G</button>
            <button type="button" class="posture-view-btn px-3 py-1 text-xs rounded bg-white border" data-view="right_lateral">Profil D</button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(320px,440px)_1fr] gap-6">
        <div class="bg-white border rounded-xl p-3 shadow-sm">
            <svg viewBox="0 0 360 720" class="w-full h-auto" id="postural-axis-svg" aria-label="Schéma anatomique des axes posturaux">
                <defs>
                    <linearGradient id="postureBodyTone" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#f8fafc"/>
                        <stop offset="100%" stop-color="#e2e8f0"/>
                    </linearGradient>
                </defs>

                <g class="posture-body-view" data-body-view="anterior">
                    <path d="M180 34 C153 34 136 53 136 79 C136 101 145 119 160 128
                             L157 145 C137 149 116 157 103 172 C91 187 87 211 86 236
                             L80 328 C78 349 66 382 59 414 C55 432 58 446 68 452
                             C77 458 88 452 92 441 L109 366 L117 293
                             C119 312 119 334 116 356 L109 437 C107 467 109 496 115 526
                             L126 626 C128 647 137 669 149 678 C158 684 168 681 172 671
                             L176 646 L180 539 L184 646 L188 671 C192 681 202 684 211 678
                             C223 669 232 647 234 626 L245 526 C251 496 253 467 251 437
                             L244 356 C241 334 241 312 243 293 L251 366 L268 441
                             C272 452 283 458 292 452 C302 446 305 432 301 414
                             C294 382 282 349 280 328 L274 236 C273 211 269 187 257 172
                             C244 157 223 149 203 145 L200 128 C215 119 224 101 224 79
                             C224 53 207 34 180 34 Z"
                          fill="url(#postureBodyTone)" stroke="#475569" stroke-width="2.2"/>
                    <path d="M151 173 Q180 190 209 173" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M131 203 Q180 181 229 203" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M145 244 Q180 258 215 244" fill="none" stroke="#cbd5e1" stroke-width="1.5"/>
                    <path d="M139 303 Q180 324 221 303" fill="none" stroke="#cbd5e1" stroke-width="1.5"/>

                    <circle cx="125" cy="183" r="5" class="posture-landmark"/>
                    <circle cx="235" cy="183" r="5" class="posture-landmark"/>
                    <circle cx="145" cy="333" r="5" class="posture-landmark"/>
                    <circle cx="215" cy="333" r="5" class="posture-landmark"/>
                    <circle cx="151" cy="520" r="5" class="posture-landmark"/>
                    <circle cx="209" cy="520" r="5" class="posture-landmark"/>
                    <circle cx="138" cy="650" r="5" class="posture-landmark"/>
                    <circle cx="222" cy="650" r="5" class="posture-landmark"/>
                </g>

                <g class="posture-body-view hidden" data-body-view="posterior">
                    <path d="M180 34 C153 34 136 53 136 79 C136 101 145 119 160 128
                             L157 145 C137 149 116 157 103 172 C91 187 87 211 86 236
                             L80 328 C78 349 66 382 59 414 C55 432 58 446 68 452
                             C77 458 88 452 92 441 L109 366 L117 293
                             C119 312 119 334 116 356 L109 437 C107 467 109 496 115 526
                             L126 626 C128 647 137 669 149 678 C158 684 168 681 172 671
                             L176 646 L180 539 L184 646 L188 671 C192 681 202 684 211 678
                             C223 669 232 647 234 626 L245 526 C251 496 253 467 251 437
                             L244 356 C241 334 241 312 243 293 L251 366 L268 441
                             C272 452 283 458 292 452 C302 446 305 432 301 414
                             C294 382 282 349 280 328 L274 236 C273 211 269 187 257 172
                             C244 157 223 149 203 145 L200 128 C215 119 224 101 224 79
                             C224 53 207 34 180 34 Z"
                          fill="url(#postureBodyTone)" stroke="#475569" stroke-width="2.2"/>
                    <path d="M180 142 L180 336" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M123 183 Q150 159 177 179" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M237 183 Q210 159 183 179" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M135 310 Q180 332 225 310" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <circle cx="125" cy="183" r="5" class="posture-landmark"/>
                    <circle cx="235" cy="183" r="5" class="posture-landmark"/>
                    <circle cx="150" cy="210" r="5" class="posture-landmark"/>
                    <circle cx="210" cy="210" r="5" class="posture-landmark"/>
                </g>

                <g class="posture-body-view hidden" data-body-view="left_lateral">
                    <path d="M171 37
                             C148 41 136 60 139 84
                             C141 104 151 119 164 126
                             L162 145
                             C145 151 132 164 125 183
                             C115 211 116 241 123 266
                             C129 287 129 309 122 334
                             C115 360 116 391 121 421
                             L129 514
                             C131 539 131 562 128 588
                             L125 655
                             C124 675 134 690 149 693
                             C164 695 173 683 174 663
                             L179 541
                             L190 431
                             C193 400 194 373 189 347
                             C184 319 184 294 192 269
                             C201 240 204 211 196 184
                             C191 166 180 153 167 146
                             L169 128
                             C183 120 193 103 194 83
                             C195 59 187 38 171 37 Z"
                          fill="url(#postureBodyTone)" stroke="#475569" stroke-width="2.2"/>
                    <path d="M163 166 Q183 205 174 248 Q160 287 174 326" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M174 326 Q183 346 185 370" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <circle cx="160" cy="98" r="5" class="posture-landmark"/>
                    <circle cx="157" cy="181" r="5" class="posture-landmark"/>
                    <circle cx="166" cy="340" r="5" class="posture-landmark"/>
                    <circle cx="153" cy="516" r="5" class="posture-landmark"/>
                    <circle cx="143" cy="655" r="5" class="posture-landmark"/>
                </g>

                <g class="posture-body-view hidden" data-body-view="right_lateral">
                    <path d="M189 37
                             C212 41 224 60 221 84
                             C219 104 209 119 196 126
                             L198 145
                             C215 151 228 164 235 183
                             C245 211 244 241 237 266
                             C231 287 231 309 238 334
                             C245 360 244 391 239 421
                             L231 514
                             C229 539 229 562 232 588
                             L235 655
                             C236 675 226 690 211 693
                             C196 695 187 683 186 663
                             L181 541
                             L170 431
                             C167 400 166 373 171 347
                             C176 319 176 294 168 269
                             C159 240 156 211 164 184
                             C169 166 180 153 193 146
                             L191 128
                             C177 120 167 103 166 83
                             C165 59 173 38 189 37 Z"
                          fill="url(#postureBodyTone)" stroke="#475569" stroke-width="2.2"/>
                    <path d="M197 166 Q177 205 186 248 Q200 287 186 326" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M186 326 Q177 346 175 370" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <circle cx="200" cy="98" r="5" class="posture-landmark"/>
                    <circle cx="203" cy="181" r="5" class="posture-landmark"/>
                    <circle cx="194" cy="340" r="5" class="posture-landmark"/>
                    <circle cx="207" cy="516" r="5" class="posture-landmark"/>
                    <circle cx="217" cy="655" r="5" class="posture-landmark"/>
                </g>

                <line x1="180" y1="25" x2="180" y2="690" stroke="#94a3b8" stroke-width="2" stroke-dasharray="7 7" class="posture-plumb"/>

                <g class="posture-view-group" data-view-group="anterior">
                    <line x1="125" y1="183" x2="235" y2="183" class="posture-axis" data-measurement="shoulder_line_angle" data-view="anterior" stroke="#2563eb" stroke-width="5"/>
                    <line x1="145" y1="333" x2="215" y2="333" class="posture-axis" data-measurement="pelvic_line_angle" data-view="anterior" stroke="#7c3aed" stroke-width="5"/>
                    <polyline points="145,333 151,520 138,650" class="posture-axis" data-measurement="knee_frontal_angle" data-view="anterior" data-side="left" fill="none" stroke="#dc2626" stroke-width="5"/>
                    <polyline points="215,333 209,520 222,650" class="posture-axis" data-measurement="knee_frontal_angle" data-view="anterior" data-side="right" fill="none" stroke="#dc2626" stroke-width="5"/>
                    <line x1="150" y1="98" x2="210" y2="98" class="posture-axis" data-measurement="head_tilt_angle" data-view="anterior" stroke="#059669" stroke-width="5"/>
                </g>

                <g class="posture-view-group hidden" data-view-group="posterior">
                    <line x1="125" y1="183" x2="235" y2="183" class="posture-axis" data-measurement="shoulder_line_angle" data-view="posterior" stroke="#2563eb" stroke-width="5"/>
                    <line x1="145" y1="333" x2="215" y2="333" class="posture-axis" data-measurement="pelvic_line_angle" data-view="posterior" stroke="#7c3aed" stroke-width="5"/>
                    <path d="M180 145 C175 185 184 225 178 270 C174 298 180 322 180 336" class="posture-axis" data-measurement="spinal_offset" data-view="posterior" fill="none" stroke="#ea580c" stroke-width="5"/>
                    <line x1="150" y1="210" x2="180" y2="210" class="posture-axis" data-measurement="scapular_distance" data-view="posterior" data-side="left" stroke="#0f766e" stroke-width="5"/>
                    <line x1="210" y1="210" x2="180" y2="210" class="posture-axis" data-measurement="scapular_distance" data-view="posterior" data-side="right" stroke="#0f766e" stroke-width="5"/>
                </g>

                <g class="posture-view-group hidden" data-view-group="left_lateral">
                    <line x1="160" y1="98" x2="157" y2="181" class="posture-axis" data-measurement="head_forward_offset" data-view="left_lateral" data-side="midline" stroke="#059669" stroke-width="5"/>
                    <polyline points="157,181 174,248 166,340" class="posture-axis" data-measurement="thoracic_kyphosis_angle" data-view="left_lateral" fill="none" stroke="#ea580c" stroke-width="5"/>
                    <polyline points="174,248 166,340 153,390" class="posture-axis" data-measurement="lumbar_lordosis_angle" data-view="left_lateral" fill="none" stroke="#9333ea" stroke-width="5"/>
                    <line x1="148" y1="337" x2="184" y2="326" class="posture-axis" data-measurement="pelvic_tilt_angle" data-view="left_lateral" data-side="midline" stroke="#7c3aed" stroke-width="5"/>
                    <polyline points="166,340 153,516 143,655" class="posture-axis" data-measurement="knee_sagittal_angle" data-view="left_lateral" data-side="left" fill="none" stroke="#dc2626" stroke-width="5"/>
                </g>

                <g class="posture-view-group hidden" data-view-group="right_lateral">
                    <line x1="200" y1="98" x2="203" y2="181" class="posture-axis" data-measurement="head_forward_offset" data-view="right_lateral" data-side="midline" stroke="#059669" stroke-width="5"/>
                    <polyline points="203,181 186,248 194,340" class="posture-axis" data-measurement="thoracic_kyphosis_angle" data-view="right_lateral" fill="none" stroke="#ea580c" stroke-width="5"/>
                    <polyline points="186,248 194,340 207,390" class="posture-axis" data-measurement="lumbar_lordosis_angle" data-view="right_lateral" fill="none" stroke="#9333ea" stroke-width="5"/>
                    <line x1="212" y1="337" x2="176" y2="326" class="posture-axis" data-measurement="pelvic_tilt_angle" data-view="right_lateral" data-side="midline" stroke="#7c3aed" stroke-width="5"/>
                    <polyline points="194,340 207,516 217,655" class="posture-axis" data-measurement="knee_sagittal_angle" data-view="right_lateral" data-side="right" fill="none" stroke="#dc2626" stroke-width="5"/>
                </g>
            </svg>
        </div>

        <div class="space-y-4">
            <div class="border rounded-xl bg-white p-4">
                <div class="text-xs uppercase tracking-wide text-gray-500 font-medium mb-1">Axe sélectionné</div>
                <div id="postural-axis-selected" class="font-semibold text-gray-900">Aucun axe sélectionné</div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-600"></span> Ligne des épaules</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-purple-600"></span> Axe du bassin</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-600"></span> Axes des genoux</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-600"></span> Tête / projection</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-orange-600"></span> Rachis</div>
                <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-slate-400"></span> Fil à plomb</div>
            </div>
            <p class="text-xs text-gray-500 leading-5">
                Les repères représentent des landmarks de mesure. Ils ne constituent pas un diagnostic et ne remplacent pas un protocole instrumenté lorsqu’une mesure clinique standardisée est requise.
            </p>
        </div>
    </div>
</div>

<style>
#postural-axis-map .posture-axis {
    cursor: pointer;
    transition: opacity .15s, stroke-width .15s, filter .15s;
}
#postural-axis-map .posture-axis:hover {
    opacity: .72;
    stroke-width: 8;
}
#postural-axis-map .posture-axis.is-selected {
    stroke-width: 9;
    filter: drop-shadow(0 0 4px rgba(15,23,42,.4));
}
#postural-axis-map .posture-landmark {
    fill: #fff;
    stroke: #475569;
    stroke-width: 2;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('postural-axis-map');
    if (!root) return;

    const selected = document.getElementById('postural-axis-selected');
    const groups = root.querySelectorAll('[data-view-group]');
    const bodyViews = root.querySelectorAll('[data-body-view]');
    const buttons = root.querySelectorAll('.posture-view-btn');

    const labels = {
        shoulder_line_angle: 'Ligne des épaules',
        pelvic_line_angle: 'Ligne du bassin',
        knee_frontal_angle: 'Axe frontal du genou',
        knee_sagittal_angle: 'Axe sagittal du genou',
        head_tilt_angle: 'Inclinaison de la tête',
        head_forward_offset: 'Projection antérieure de la tête',
        spinal_offset: 'Déviation de l’axe rachidien',
        scapular_distance: 'Distance scapulaire',
        thoracic_kyphosis_angle: 'Courbure thoracique',
        lumbar_lordosis_angle: 'Courbure lombaire',
        pelvic_tilt_angle: 'Inclinaison sagittale du bassin'
    };

    const activateView = view => {
        groups.forEach(group => group.classList.toggle('hidden', group.dataset.viewGroup !== view));
        bodyViews.forEach(group => group.classList.toggle('hidden', group.dataset.bodyView !== view));
        buttons.forEach(button => {
            const active = button.dataset.view === view;
            button.classList.toggle('bg-gray-900', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('bg-white', !active);
            button.classList.toggle('border', !active);
        });
    };

    buttons.forEach(button => button.addEventListener('click', () => activateView(button.dataset.view)));

    root.querySelectorAll('.posture-axis').forEach(axis => {
        axis.addEventListener('click', () => {
            root.querySelectorAll('.posture-axis').forEach(el => el.classList.remove('is-selected'));
            axis.classList.add('is-selected');

            const detail = {
                measurement_key: axis.dataset.measurement,
                view: axis.dataset.view,
                side: axis.dataset.side || null
            };

            selected.textContent =
                (labels[detail.measurement_key] || detail.measurement_key) +
                ' · ' + detail.view.replaceAll('_', ' ') +
                (detail.side ? ' · ' + detail.side : '');

            root.dispatchEvent(new CustomEvent('postural-axis-selected', {
                bubbles: true,
                detail
            }));
        });
    });

    activateView('anterior');
});
</script>

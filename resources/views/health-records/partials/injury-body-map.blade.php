@php
    $mapInputId = $inputId ?? 'injury_location';
    $mapInputName = $inputName ?? 'injury_location';
    $mapValue = $value ?? '';
@endphp

<div class="injury-body-map border border-gray-200 rounded-xl p-4 bg-gray-50"
     data-injury-body-map
     data-input-id="{{ $mapInputId }}">
    <div class="flex items-center justify-between gap-3 mb-4">
        <div>
            <h4 class="font-semibold text-gray-900">Localisation anatomique</h4>
            <p class="text-xs text-gray-500">Sélectionnez directement la région sur la silhouette.</p>
        </div>
        <div class="flex gap-2">
            <button type="button" class="injury-view-btn px-3 py-1 text-xs rounded bg-gray-900 text-white" data-view="front">Antérieur</button>
            <button type="button" class="injury-view-btn px-3 py-1 text-xs rounded bg-white border" data-view="back">Postérieur</button>
        </div>
    </div>

    <input type="hidden" id="{{ $mapInputId }}" name="{{ $mapInputName }}" value="{{ $mapValue }}">

    <div class="grid grid-cols-1 lg:grid-cols-[minmax(300px,420px)_1fr] gap-6 items-start">
        <div class="bg-white border rounded-xl p-3 shadow-sm">
            <svg class="injury-body-svg w-full h-auto" viewBox="0 0 360 720" role="img" aria-label="Silhouette anatomique interactive">
                <defs>
                    <linearGradient id="bodyToneFront" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#f8fafc"/>
                        <stop offset="100%" stop-color="#e2e8f0"/>
                    </linearGradient>
                    <linearGradient id="bodyToneBack" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#f8fafc"/>
                        <stop offset="100%" stop-color="#e5e7eb"/>
                    </linearGradient>
                </defs>

                <g class="injury-body-front">
                    <!-- Anatomical silhouette -->
                    <path d="M180 34
                             C153 34 136 53 136 79
                             C136 101 145 119 160 128
                             L157 145
                             C137 149 116 157 103 172
                             C91 187 87 211 86 236
                             L80 328
                             C78 349 66 382 59 414
                             C55 432 58 446 68 452
                             C77 458 88 452 92 441
                             L109 366
                             L117 293
                             C119 312 119 334 116 356
                             L109 437
                             C107 467 109 496 115 526
                             L126 626
                             C128 647 137 669 149 678
                             C158 684 168 681 172 671
                             L176 646
                             L180 539
                             L184 646
                             L188 671
                             C192 681 202 684 211 678
                             C223 669 232 647 234 626
                             L245 526
                             C251 496 253 467 251 437
                             L244 356
                             C241 334 241 312 243 293
                             L251 366
                             L268 441
                             C272 452 283 458 292 452
                             C302 446 305 432 301 414
                             C294 382 282 349 280 328
                             L274 236
                             C273 211 269 187 257 172
                             C244 157 223 149 203 145
                             L200 128
                             C215 119 224 101 224 79
                             C224 53 207 34 180 34 Z"
                          fill="url(#bodyToneFront)" stroke="#475569" stroke-width="2.2"/>

                    <!-- anatomical landmarks -->
                    <path d="M151 173 Q180 190 209 173" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M131 203 Q180 181 229 203" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M180 188 L180 286" stroke="#cbd5e1" stroke-width="1.5"/>
                    <path d="M145 244 Q180 258 215 244" fill="none" stroke="#cbd5e1" stroke-width="1.5"/>
                    <path d="M139 303 Q180 324 221 303" fill="none" stroke="#cbd5e1" stroke-width="1.5"/>
                    <circle cx="129" cy="451" r="8" fill="none" stroke="#cbd5e1"/>
                    <circle cx="231" cy="451" r="8" fill="none" stroke="#cbd5e1"/>
                    <circle cx="112" cy="332" r="7" fill="none" stroke="#cbd5e1"/>
                    <circle cx="248" cy="332" r="7" fill="none" stroke="#cbd5e1"/>

                    <!-- zones -->
                    <ellipse class="injury-zone" data-zone="head" data-label="Tête" cx="180" cy="80" rx="42" ry="48"/>
                    <path class="injury-zone" data-zone="neck" data-label="Cou" d="M157 126 L203 126 L207 163 Q180 176 153 163 Z"/>
                    <path class="injury-zone" data-zone="shoulder_left" data-label="Épaule gauche" d="M151 154 C128 155 109 162 96 177 L102 213 Q122 198 149 194 Z"/>
                    <path class="injury-zone" data-zone="shoulder_right" data-label="Épaule droite" d="M209 154 C232 155 251 162 264 177 L258 213 Q238 198 211 194 Z"/>
                    <path class="injury-zone" data-zone="chest_left" data-label="Thorax gauche" d="M148 188 Q128 190 118 211 L122 261 Q145 271 177 263 L177 192 Z"/>
                    <path class="injury-zone" data-zone="chest_right" data-label="Thorax droit" d="M212 188 Q232 190 242 211 L238 261 Q215 271 183 263 L183 192 Z"/>
                    <path class="injury-zone" data-zone="abdomen" data-label="Abdomen" d="M132 263 Q180 276 228 263 L224 321 Q180 340 136 321 Z"/>
                    <path class="injury-zone" data-zone="groin_pelvis" data-label="Bassin / aine" d="M136 320 Q180 341 224 320 L235 369 Q180 389 125 369 Z"/>
                    <path class="injury-zone" data-zone="upper_arm_left" data-label="Bras gauche" d="M98 196 Q84 209 86 240 L80 309 L112 311 L119 224 Z"/>
                    <path class="injury-zone" data-zone="upper_arm_right" data-label="Bras droit" d="M262 196 Q276 209 274 240 L280 309 L248 311 L241 224 Z"/>
                    <path class="injury-zone" data-zone="forearm_left" data-label="Avant-bras gauche" d="M80 302 L111 306 L99 378 L66 373 Z"/>
                    <path class="injury-zone" data-zone="forearm_right" data-label="Avant-bras droit" d="M280 302 L249 306 L261 378 L294 373 Z"/>
                    <path class="injury-zone" data-zone="hand_left" data-label="Main gauche" d="M64 370 Q51 396 58 427 Q64 454 78 454 Q91 447 93 421 L97 376 Z"/>
                    <path class="injury-zone" data-zone="hand_right" data-label="Main droite" d="M296 370 Q309 396 302 427 Q296 454 282 454 Q269 447 267 421 L263 376 Z"/>
                    <path class="injury-zone" data-zone="thigh_left" data-label="Cuisse gauche" d="M126 366 L177 370 L173 455 L160 531 L116 526 L109 437 Z"/>
                    <path class="injury-zone" data-zone="thigh_right" data-label="Cuisse droite" d="M234 366 L183 370 L187 455 L200 531 L244 526 L251 437 Z"/>
                    <ellipse class="injury-zone" data-zone="knee_left" data-label="Genou gauche" cx="151" cy="520" rx="27" ry="31"/>
                    <ellipse class="injury-zone" data-zone="knee_right" data-label="Genou droit" cx="209" cy="520" rx="27" ry="31"/>
                    <path class="injury-zone" data-zone="lower_leg_left" data-label="Jambe gauche" d="M124 544 L169 544 L164 628 L151 672 L127 652 L119 601 Z"/>
                    <path class="injury-zone" data-zone="lower_leg_right" data-label="Jambe droite" d="M236 544 L191 544 L196 628 L209 672 L233 652 L241 601 Z"/>
                    <path class="injury-zone" data-zone="ankle_foot_left" data-label="Cheville / pied gauche" d="M126 642 Q114 669 117 690 L147 704 Q164 702 171 682 L163 648 Z"/>
                    <path class="injury-zone" data-zone="ankle_foot_right" data-label="Cheville / pied droit" d="M234 642 Q246 669 243 690 L213 704 Q196 702 189 682 L197 648 Z"/>
                </g>

                <g class="injury-body-back hidden">
                    <path d="M180 34
                             C153 34 136 53 136 79
                             C136 101 145 119 160 128
                             L157 145
                             C137 149 116 157 103 172
                             C91 187 87 211 86 236
                             L80 328
                             C78 349 66 382 59 414
                             C55 432 58 446 68 452
                             C77 458 88 452 92 441
                             L109 366
                             L117 293
                             C119 312 119 334 116 356
                             L109 437
                             C107 467 109 496 115 526
                             L126 626
                             C128 647 137 669 149 678
                             C158 684 168 681 172 671
                             L176 646
                             L180 539
                             L184 646
                             L188 671
                             C192 681 202 684 211 678
                             C223 669 232 647 234 626
                             L245 526
                             C251 496 253 467 251 437
                             L244 356
                             C241 334 241 312 243 293
                             L251 366
                             L268 441
                             C272 452 283 458 292 452
                             C302 446 305 432 301 414
                             C294 382 282 349 280 328
                             L274 236
                             C273 211 269 187 257 172
                             C244 157 223 149 203 145
                             L200 128
                             C215 119 224 101 224 79
                             C224 53 207 34 180 34 Z"
                          fill="url(#bodyToneBack)" stroke="#475569" stroke-width="2.2"/>

                    <path d="M180 142 L180 336" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M123 183 Q150 159 177 179" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M237 183 Q210 159 183 179" fill="none" stroke="#cbd5e1" stroke-width="2"/>
                    <path d="M135 310 Q180 332 225 310" fill="none" stroke="#cbd5e1" stroke-width="2"/>

                    <ellipse class="injury-zone" data-zone="head_back" data-label="Occiput / tête" cx="180" cy="80" rx="42" ry="48"/>
                    <path class="injury-zone" data-zone="cervical" data-label="Rachis cervical" d="M157 126 L203 126 L207 166 L153 166 Z"/>
                    <path class="injury-zone" data-zone="scapula_left" data-label="Scapula gauche" d="M102 174 Q122 154 158 168 L170 229 Q143 248 112 231 Z"/>
                    <path class="injury-zone" data-zone="scapula_right" data-label="Scapula droite" d="M258 174 Q238 154 202 168 L190 229 Q217 248 248 231 Z"/>
                    <path class="injury-zone" data-zone="thoracic_spine" data-label="Rachis thoracique" d="M169 158 L191 158 L195 275 L165 275 Z"/>
                    <path class="injury-zone" data-zone="lumbar_spine" data-label="Rachis lombaire" d="M163 273 L197 273 L202 333 L158 333 Z"/>
                    <path class="injury-zone" data-zone="glute_left" data-label="Fessier gauche" d="M125 323 Q152 312 178 336 L176 386 Q143 396 116 373 Z"/>
                    <path class="injury-zone" data-zone="glute_right" data-label="Fessier droit" d="M235 323 Q208 312 182 336 L184 386 Q217 396 244 373 Z"/>
                    <path class="injury-zone" data-zone="triceps_left" data-label="Bras postérieur gauche" d="M98 195 Q85 210 87 245 L80 310 L112 312 L120 224 Z"/>
                    <path class="injury-zone" data-zone="triceps_right" data-label="Bras postérieur droit" d="M262 195 Q275 210 273 245 L280 310 L248 312 L240 224 Z"/>
                    <path class="injury-zone" data-zone="forearm_back_left" data-label="Avant-bras postérieur gauche" d="M80 302 L111 306 L99 378 L66 373 Z"/>
                    <path class="injury-zone" data-zone="forearm_back_right" data-label="Avant-bras postérieur droit" d="M280 302 L249 306 L261 378 L294 373 Z"/>
                    <path class="injury-zone" data-zone="hamstring_left" data-label="Ischio-jambiers gauches" d="M126 375 L176 379 L171 513 L118 518 L109 437 Z"/>
                    <path class="injury-zone" data-zone="hamstring_right" data-label="Ischio-jambiers droits" d="M234 375 L184 379 L189 513 L242 518 L251 437 Z"/>
                    <ellipse class="injury-zone" data-zone="knee_back_left" data-label="Genou postérieur gauche" cx="151" cy="520" rx="27" ry="28"/>
                    <ellipse class="injury-zone" data-zone="knee_back_right" data-label="Genou postérieur droit" cx="209" cy="520" rx="27" ry="28"/>
                    <path class="injury-zone" data-zone="calf_left" data-label="Mollet gauche" d="M124 544 Q146 535 168 550 L161 625 Q147 652 128 633 L119 591 Z"/>
                    <path class="injury-zone" data-zone="calf_right" data-label="Mollet droit" d="M236 544 Q214 535 192 550 L199 625 Q213 652 232 633 L241 591 Z"/>
                    <path class="injury-zone" data-zone="achilles_left" data-label="Tendon d'Achille gauche" d="M134 616 L160 616 L160 674 L132 674 Z"/>
                    <path class="injury-zone" data-zone="achilles_right" data-label="Tendon d'Achille droit" d="M226 616 L200 616 L200 674 L228 674 Z"/>
                </g>
            </svg>
        </div>

        <div class="space-y-4">
            <div class="border rounded-xl px-4 py-4 bg-white">
                <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Zone sélectionnée</div>
                <div class="injury-zone-label text-lg font-semibold text-gray-900">{{ $mapValue ?: 'Aucune zone sélectionnée' }}</div>
            </div>
            <div class="text-sm text-gray-600 leading-6">
                La carte sert uniquement à localiser la région anatomique. Le diagnostic, le type de lésion, la gravité et le mécanisme restent des champs cliniques distincts.
            </div>
        </div>
    </div>
</div>

<style>
[data-injury-body-map] .injury-zone {
    fill: transparent;
    stroke: transparent;
    stroke-width: 2;
    cursor: pointer;
    transition: fill .15s ease, stroke .15s ease, filter .15s ease;
    outline: none;
}
[data-injury-body-map] .injury-zone:hover,
[data-injury-body-map] .injury-zone:focus {
    fill: rgba(59,130,246,.15);
    stroke: #2563eb;
    filter: drop-shadow(0 0 2px rgba(37,99,235,.25));
}
[data-injury-body-map] .injury-zone.is-selected {
    fill: rgba(220,38,38,.24);
    stroke: #dc2626;
    filter: drop-shadow(0 0 3px rgba(220,38,38,.35));
}
</style>

@once
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-injury-body-map]').forEach(map => {
        const input = document.getElementById(map.dataset.inputId);
        const label = map.querySelector('.injury-zone-label');
        const front = map.querySelector('.injury-body-front');
        const back = map.querySelector('.injury-body-back');
        const buttons = map.querySelectorAll('.injury-view-btn');

        const selectZone = zone => {
            const value = zone.dataset.label || zone.dataset.zone;
            input.value = value;
            label.textContent = value;
            map.querySelectorAll('.injury-zone').forEach(el => el.classList.remove('is-selected'));
            zone.classList.add('is-selected');
            input.dispatchEvent(new Event('change', { bubbles: true }));
        };

        map.querySelectorAll('.injury-zone').forEach(zone => {
            zone.setAttribute('tabindex', '0');
            zone.addEventListener('click', () => selectZone(zone));
            zone.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    selectZone(zone);
                }
            });
        });

        buttons.forEach(button => button.addEventListener('click', () => {
            const isFront = button.dataset.view === 'front';
            front.classList.toggle('hidden', !isFront);
            back.classList.toggle('hidden', isFront);
            buttons.forEach(b => {
                const active = b === button;
                b.classList.toggle('bg-gray-900', active);
                b.classList.toggle('text-white', active);
                b.classList.toggle('bg-white', !active);
                b.classList.toggle('border', !active);
            });
        }));
    });
});
</script>
@endonce

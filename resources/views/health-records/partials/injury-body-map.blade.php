@php
    $mapInputId = $inputId ?? 'injury_location';
    $mapInputName = $inputName ?? 'injury_location';
    $mapValue = $value ?? '';
@endphp

<div class="injury-body-map border border-gray-200 rounded-xl p-4 bg-gray-50"
     data-injury-body-map
     data-input-id="{{ $mapInputId }}">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h4 class="font-semibold text-gray-900">Localisation anatomique</h4>
            <p class="text-xs text-gray-500">Cliquez directement sur la zone blessée.</p>
        </div>
        <div class="flex gap-2">
            <button type="button" class="injury-view-btn px-3 py-1 text-xs rounded bg-gray-900 text-white" data-view="front">Avant</button>
            <button type="button" class="injury-view-btn px-3 py-1 text-xs rounded bg-white border" data-view="back">Arrière</button>
        </div>
    </div>

    <input type="hidden" id="{{ $mapInputId }}" name="{{ $mapInputName }}" value="{{ $mapValue }}">

    <div class="grid grid-cols-1 md:grid-cols-[minmax(220px,320px)_1fr] gap-5 items-start">
        <div class="bg-white border rounded-xl p-3">
            <svg class="injury-body-svg w-full h-auto" viewBox="0 0 260 520" role="img" aria-label="Carte anatomique cliquable">
                <g class="injury-body-front">
                    <circle cx="130" cy="45" r="28" class="fill-gray-100 stroke-gray-500" stroke-width="2"/>
                    <rect x="116" y="72" width="28" height="24" rx="10" class="fill-gray-100 stroke-gray-500" stroke-width="2"/>
                    <path d="M91 98 Q130 80 169 98 L184 205 Q171 244 158 270 L102 270 Q89 244 76 205 Z"
                          class="fill-gray-100 stroke-gray-500" stroke-width="2"/>
                    <path d="M85 105 L53 155 L42 245" class="fill-none stroke-gray-500" stroke-width="22" stroke-linecap="round"/>
                    <path d="M175 105 L207 155 L218 245" class="fill-none stroke-gray-500" stroke-width="22" stroke-linecap="round"/>
                    <path d="M112 270 L99 382 L92 492" class="fill-none stroke-gray-500" stroke-width="28" stroke-linecap="round"/>
                    <path d="M148 270 L161 382 L168 492" class="fill-none stroke-gray-500" stroke-width="28" stroke-linecap="round"/>

                    @foreach([
                        ['head','Tête',130,45,34,32],['neck','Cou',130,84,30,18],
                        ['shoulder_left','Épaule G',93,110,36,26],['shoulder_right','Épaule D',167,110,36,26],
                        ['chest','Thorax',130,145,72,50],['abdomen','Abdomen',130,202,62,45],
                        ['hip_left','Hanche G',107,258,34,28],['hip_right','Hanche D',153,258,34,28],
                        ['thigh_left','Cuisse G',103,322,34,70],['thigh_right','Cuisse D',157,322,34,70],
                        ['knee_left','Genou G',98,392,34,28],['knee_right','Genou D',162,392,34,28],
                        ['lower_leg_left','Jambe G',94,441,34,55],['lower_leg_right','Jambe D',166,441,34,55],
                        ['ankle_left','Cheville G',91,489,32,22],['ankle_right','Cheville D',169,489,32,22],
                        ['arm_left','Bras G',58,165,32,66],['arm_right','Bras D',202,165,32,66],
                        ['forearm_left','Avant-bras G',46,222,30,60],['forearm_right','Avant-bras D',214,222,30,60],
                    ] as [$zone,$label,$cx,$cy,$w,$h])
                        <rect x="{{ $cx-$w/2 }}" y="{{ $cy-$h/2 }}" width="{{ $w }}" height="{{ $h }}"
                              rx="10" fill="transparent" stroke="transparent"
                              class="injury-zone cursor-pointer"
                              data-zone="{{ $zone }}" data-label="{{ $label }}" tabindex="0"/>
                    @endforeach
                </g>

                <g class="injury-body-back hidden">
                    <circle cx="130" cy="45" r="28" class="fill-gray-100 stroke-gray-500" stroke-width="2"/>
                    <rect x="116" y="72" width="28" height="24" rx="10" class="fill-gray-100 stroke-gray-500" stroke-width="2"/>
                    <path d="M91 98 Q130 80 169 98 L184 205 Q171 244 158 270 L102 270 Q89 244 76 205 Z"
                          class="fill-gray-100 stroke-gray-500" stroke-width="2"/>
                    <path d="M85 105 L53 155 L42 245" class="fill-none stroke-gray-500" stroke-width="22" stroke-linecap="round"/>
                    <path d="M175 105 L207 155 L218 245" class="fill-none stroke-gray-500" stroke-width="22" stroke-linecap="round"/>
                    <path d="M112 270 L99 382 L92 492" class="fill-none stroke-gray-500" stroke-width="28" stroke-linecap="round"/>
                    <path d="M148 270 L161 382 L168 492" class="fill-none stroke-gray-500" stroke-width="28" stroke-linecap="round"/>

                    @foreach([
                        ['head_back','Occiput',130,45,34,32],['cervical','Rachis cervical',130,92,42,25],
                        ['scapula_left','Scapula G',105,135,45,52],['scapula_right','Scapula D',155,135,45,52],
                        ['thoracic_spine','Rachis thoracique',130,158,30,90],['lumbar_spine','Rachis lombaire',130,225,34,55],
                        ['glute_left','Fessier G',108,265,42,38],['glute_right','Fessier D',152,265,42,38],
                        ['hamstring_left','Ischio G',103,329,34,72],['hamstring_right','Ischio D',157,329,34,72],
                        ['knee_back_left','Genou G arrière',98,392,34,28],['knee_back_right','Genou D arrière',162,392,34,28],
                        ['calf_left','Mollet G',94,441,34,55],['calf_right','Mollet D',166,441,34,55],
                        ['achilles_left','Achille G',91,486,28,28],['achilles_right','Achille D',169,486,28,28],
                    ] as [$zone,$label,$cx,$cy,$w,$h])
                        <rect x="{{ $cx-$w/2 }}" y="{{ $cy-$h/2 }}" width="{{ $w }}" height="{{ $h }}"
                              rx="10" fill="transparent" stroke="transparent"
                              class="injury-zone cursor-pointer"
                              data-zone="{{ $zone }}" data-label="{{ $label }}" tabindex="0"/>
                    @endforeach
                </g>
            </svg>
        </div>

        <div>
            <div class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Zone sélectionnée</div>
            <div class="injury-zone-label border rounded-lg px-4 py-3 bg-white font-semibold text-gray-900">
                {{ $mapValue ?: 'Aucune zone sélectionnée' }}
            </div>
            <p class="text-xs text-gray-500 mt-3">
                La carte renseigne la localisation anatomique. Le type, la gravité et le mécanisme restent des données séparées.
            </p>
        </div>
    </div>
</div>

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
            map.querySelectorAll('.injury-zone').forEach(el => {
                el.setAttribute('stroke', 'transparent');
                el.setAttribute('fill', 'transparent');
            });
            zone.setAttribute('fill', 'rgba(239,68,68,.20)');
            zone.setAttribute('stroke', '#dc2626');
            zone.setAttribute('stroke-width', '2');
            input.dispatchEvent(new Event('change', { bubbles: true }));
        };

        map.querySelectorAll('.injury-zone').forEach(zone => {
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
                b.classList.toggle('bg-gray-900', b === button);
                b.classList.toggle('text-white', b === button);
                b.classList.toggle('bg-white', b !== button);
                b.classList.toggle('border', b !== button);
            });
        }));
    });
});
</script>
@endonce

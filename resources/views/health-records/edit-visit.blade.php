@extends('layouts.app')

@section('title', 'Modifier la visite médicale - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-6 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <a href="{{ route('health-records.show', $healthRecord) }}" class="text-sm text-blue-600 hover:text-blue-800">← Dossier médical</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">Modifier la visite</h1>
            <p class="text-gray-600 mt-1">
                {{ $healthRecord->visit_date?->format('d/m/Y') ?? $healthRecord->record_date?->format('d/m/Y') }}
                @if($healthRecord->doctor_name) · {{ $healthRecord->doctor_name }} @endif
            </p>
        </div>
        <a href="{{ route('health-records.edit', ['healthRecord'=>$healthRecord,'advanced'=>1]) }}"
           class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
            Mode avancé / examens spécialisés
        </a>
    </div>

    <form method="POST" action="{{ route('health-records.update', $healthRecord) }}" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="player_id" value="{{ $healthRecord->player_id }}">
        <input type="hidden" name="workflow" value="visit">

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b bg-slate-50">
                <h2 class="font-semibold text-gray-900">Contexte de la visite</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="visit_date" value="{{ old('visit_date', $healthRecord->visit_date?->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                    <input type="text" name="doctor_name" value="{{ old('doctor_name', $healthRecord->doctor_name) }}" class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="visit_type" class="w-full border-gray-300 rounded-lg">
                        @foreach([
                            'consultation'=>'Consultation',
                            'follow_up'=>'Suivi',
                            'emergency'=>'Urgence',
                            'pre_season'=>'Pré-saison',
                            'post_match'=>'Post-match',
                            'rehabilitation'=>'Rééducation'
                        ] as $value=>$label)
                            <option value="{{ $value }}" @selected(old('visit_type', $healthRecord->visit_type)===$value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div></div>
                <div class="md:col-span-2 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motif principal</label>
                            <select id="complaint-type" class="w-full border-gray-300 rounded-lg">
                                <option value="">Sélectionner</option>
                                @foreach(['Douleur','Blessure aiguë','Suivi de blessure','Fatigue','Symptômes respiratoires','Symptômes digestifs','Symptômes neurologiques','Contrôle médical','Pré-saison / aptitude','Autre'] as $option)
                                    <option>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Région</label>
                            <select id="complaint-region" class="w-full border-gray-300 rounded-lg">
                                <option value="">Non précisée</option>
                                @foreach(['Tête / cou','Épaule','Bras / coude','Poignet / main','Thorax','Dos / rachis','Bassin / hanche','Cuisse','Genou','Jambe','Cheville','Pied','Général'] as $option)
                                    <option>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Côté</label>
                            <select id="complaint-side" class="w-full border-gray-300 rounded-lg">
                                <option value="">Sans latéralité</option>
                                @foreach(['Gauche','Droite','Bilatéral','Médian'] as $option)<option>{{ $option }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contexte</label>
                            <select id="complaint-context" class="w-full border-gray-300 rounded-lg">
                                <option value="">Non précisé</option>
                                @foreach(['Match','Entraînement','Traumatisme direct','Sans traumatisme','Effort progressif','Repos'] as $option)<option>{{ $option }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Précision / motif existant</label>
                        <input id="complaint-detail" type="text" value="{{ old('chief_complaint', $healthRecord->chief_complaint) }}" class="w-full border-gray-300 rounded-lg">
                    </div>
                    <input type="hidden" name="chief_complaint" id="chief-complaint-value" value="{{ old('chief_complaint', $healthRecord->chief_complaint) }}">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-semibold text-gray-900">Histoire et examen</h2>
            </div>
            <div class="p-6 space-y-5">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Début</label><select id="history-onset" class="w-full border-gray-300 rounded-lg"><option value="">Non précisé</option>@foreach(['Aujourd’hui','1–3 jours','4–7 jours','1–4 semaines','Plus d’un mois'] as $option)<option>{{ $option }}</option>@endforeach</select></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Installation</label><select id="history-mode" class="w-full border-gray-300 rounded-lg"><option value="">Non précisée</option>@foreach(['Brutale','Progressive','Récidivante','Post-traumatique'] as $option)<option>{{ $option }}</option>@endforeach</select></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Évolution</label><select id="history-evolution" class="w-full border-gray-300 rounded-lg"><option value="">Non précisée</option>@foreach(['Amélioration','Stable','Aggravation','Fluctuante'] as $option)<option>{{ $option }}</option>@endforeach</select></div>
                    </div>
                    <div><div class="text-sm font-medium text-gray-700 mb-2">Symptômes associés</div><div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">@foreach(['Gonflement','Instabilité','Blocage','Craquement','Raideur','Faiblesse','Engourdissement','Douleur nocturne'] as $symptom)<label class="flex items-center gap-2 border rounded-lg px-3 py-2 bg-white"><input type="checkbox" class="history-symptom" value="{{ $symptom }}"><span>{{ $symptom }}</span></label>@endforeach</div></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Détails existants / complémentaires</label><textarea id="history-detail" rows="2" class="w-full border-gray-300 rounded-lg">{{ old('visit_notes', $healthRecord->visit_notes) }}</textarea></div>
                    <input type="hidden" name="visit_notes" id="visit-notes-value" value="{{ old('visit_notes', $healthRecord->visit_notes) }}">
                </div>

                <details class="border rounded-lg" @if($healthRecord->blood_pressure_systolic || $healthRecord->heart_rate || $healthRecord->temperature) open @endif>
                    <summary class="cursor-pointer px-4 py-3 font-medium text-gray-800 bg-gray-50 rounded-lg">Constantes</summary>
                    <div class="p-4 grid grid-cols-2 md:grid-cols-6 gap-3">
                        <div><label class="text-xs text-gray-600">TA syst.</label><input name="blood_pressure_systolic" type="number" value="{{ old('blood_pressure_systolic',$healthRecord->blood_pressure_systolic) }}" class="w-full border-gray-300 rounded-md"></div>
                        <div><label class="text-xs text-gray-600">TA diast.</label><input name="blood_pressure_diastolic" type="number" value="{{ old('blood_pressure_diastolic',$healthRecord->blood_pressure_diastolic) }}" class="w-full border-gray-300 rounded-md"></div>
                        <div><label class="text-xs text-gray-600">FC</label><input name="heart_rate" type="number" value="{{ old('heart_rate',$healthRecord->heart_rate) }}" class="w-full border-gray-300 rounded-md"></div>
                        <div><label class="text-xs text-gray-600">Temp. °C</label><input name="temperature" type="number" step="0.1" value="{{ old('temperature',$healthRecord->temperature) }}" class="w-full border-gray-300 rounded-md"></div>
                        <div><label class="text-xs text-gray-600">Poids kg</label><input name="weight" type="number" step="0.1" value="{{ old('weight',$healthRecord->weight) }}" class="w-full border-gray-300 rounded-md"></div>
                        <div><label class="text-xs text-gray-600">Taille cm</label><input name="height" type="number" step="0.1" value="{{ old('height',$healthRecord->height) }}" class="w-full border-gray-300 rounded-md"></div>
                    </div>
                </details>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Examen ciblé</label><select id="exam-system" class="w-full border-gray-300 rounded-lg"><option value="">Sélectionner</option>@foreach(['Musculosquelettique','Neurologique','Cardiovasculaire','Respiratoire','Abdominal','ORL','Général'] as $option)<option>{{ $option }}</option>@endforeach</select></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Mobilité</label><select id="exam-mobility" class="w-full border-gray-300 rounded-lg"><option value="">Non évaluée</option>@foreach(['Normale','Limitée','Douloureuse','Hypermobile'] as $option)<option>{{ $option }}</option>@endforeach</select></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Force</label><select id="exam-strength" class="w-full border-gray-300 rounded-lg"><option value="">Non évaluée</option>@foreach(['Normale','Légèrement diminuée','Modérément diminuée','Très diminuée'] as $option)<option>{{ $option }}</option>@endforeach</select></div>
                    </div>
                    <div><div class="text-sm font-medium text-gray-700 mb-2">Constats cliniques</div><div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">@foreach(['Douleur à la palpation','Œdème','Ecchymose','Déformation','Instabilité','Déficit neurologique','Test spécifique positif','Examen normal'] as $finding)<label class="flex items-center gap-2 border rounded-lg px-3 py-2 bg-white"><input type="checkbox" class="exam-finding" value="{{ $finding }}"><span>{{ $finding }}</span></label>@endforeach</div></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Détail / examen existant</label><textarea id="exam-detail" rows="2" class="w-full border-gray-300 rounded-lg">{{ old('physical_examination', $healthRecord->physical_examination) }}</textarea></div>
                    <input type="hidden" name="physical_examination" id="physical-examination-value" value="{{ old('physical_examination', $healthRecord->physical_examination) }}">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-semibold text-gray-900">Évaluation et plan</h2>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Impression clinique</label><select id="diagnosis-category" class="w-full border-gray-300 rounded-lg"><option value="">Non concluant / à préciser</option>@foreach(['Traumatisme musculosquelettique','Lésion musculaire probable','Entorse probable','Tendinopathie probable','Contusion','Surcharge / surmenage','Symptômes infectieux','Évaluation normale','Autre'] as $option)<option>{{ $option }}</option>@endforeach</select></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Niveau de certitude</label><select id="diagnosis-certainty" class="w-full border-gray-300 rounded-lg"><option value="">Non précisé</option>@foreach(['Hypothèse','Probable','Confirmé'] as $option)<option>{{ $option }}</option>@endforeach</select></div>
                </div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Diagnostic existant / précision</label><input id="diagnosis-detail" type="text" value="{{ old('diagnosis', $healthRecord->diagnosis) }}" class="w-full border-gray-300 rounded-lg"><input type="hidden" name="diagnosis" id="diagnosis-value" value="{{ old('diagnosis', $healthRecord->diagnosis) }}"></div>
                <div><div class="text-sm font-medium text-gray-700 mb-2">Conduite à tenir</div><div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">@foreach(['Repos relatif','Glace','Compression','Élévation','Antalgique','Anti-inflammatoire','Kinésithérapie','Imagerie','Avis spécialiste','Réévaluation'] as $plan)<label class="flex items-center gap-2 border rounded-lg px-3 py-2 bg-white"><input type="checkbox" class="plan-action" value="{{ $plan }}"><span>{{ $plan }}</span></label>@endforeach</div></div>
                <div><div class="text-sm font-medium text-gray-700 mb-2">Restriction sportive</div><div class="grid grid-cols-2 md:grid-cols-4 gap-2">@foreach(['Aucune','Entraînement adapté','Sans contact','Pas de compétition','Arrêt sportif'] as $restriction)<label class="border rounded-lg px-3 py-2 text-sm flex items-center gap-2 bg-white"><input type="radio" name="restriction_ui" class="restriction-option" value="{{ $restriction }}"><span>{{ $restriction }}</span></label>@endforeach</div></div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Délai de suivi</label><select id="followup-delay" class="w-full border-gray-300 rounded-lg"><option value="">Selon évolution</option><option value="1">24 h</option><option value="2">48 h</option><option value="3">72 h</option><option value="7">7 jours</option><option value="14">14 jours</option><option value="30">1 mois</option></select></div>
                    <div class="md:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">Précision de suivi</label><input id="followup-detail" type="text" value="{{ old('follow_up_instructions', $healthRecord->follow_up_instructions) }}" class="w-full border-gray-300 rounded-lg"></div>
                </div>
                <input type="hidden" name="treatment_plan" id="treatment-plan-value" value="{{ old('treatment_plan', $healthRecord->treatment_plan) }}">
                <input type="hidden" name="prescriptions" id="prescriptions-value" value="{{ old('prescriptions', $healthRecord->prescriptions) }}">
                <input type="hidden" name="follow_up_instructions" id="follow-up-value" value="{{ old('follow_up_instructions', $healthRecord->follow_up_instructions) }}">
                <input type="hidden" name="next_checkup_date" id="next-checkup-value" value="{{ old('next_checkup_date', $healthRecord->next_checkup_date?->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="sticky bottom-4 bg-white/95 backdrop-blur border border-gray-200 rounded-xl shadow-lg p-4 flex justify-end gap-3">
            <a href="{{ route('health-records.show', $healthRecord) }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">Annuler</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Enregistrer</button>
        </div>
    </form>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[action*="health-records"]');
    if (!form) return;
    const value = id => document.getElementById(id)?.value?.trim() || '';
    const checked = selector => Array.from(document.querySelectorAll(selector + ':checked')).map(el => el.value);
    const compose = () => {
        document.getElementById('chief-complaint-value').value = [value('complaint-type'),value('complaint-region'),value('complaint-side'),value('complaint-context') ? 'Contexte: '+value('complaint-context') : '',value('complaint-detail')].filter(Boolean).join(' · ');
        const symptoms=checked('.history-symptom');
        document.getElementById('visit-notes-value').value=[value('history-onset')?'Début: '+value('history-onset'):'',value('history-mode')?'Installation: '+value('history-mode'):'',value('history-evolution')?'Évolution: '+value('history-evolution'):'',symptoms.length?'Associés: '+symptoms.join(', '):'',value('history-detail')].filter(Boolean).join(' | ');
        const findings=checked('.exam-finding');
        document.getElementById('physical-examination-value').value=[value('exam-system')?'Examen: '+value('exam-system'):'',value('exam-mobility')?'Mobilité: '+value('exam-mobility'):'',value('exam-strength')?'Force: '+value('exam-strength'):'',findings.length?'Constats: '+findings.join(', '):'',value('exam-detail')].filter(Boolean).join(' | ');
        document.getElementById('diagnosis-value').value=[value('diagnosis-category'),value('diagnosis-certainty')?'('+value('diagnosis-certainty')+')':'',value('diagnosis-detail')].filter(Boolean).join(' ');
        const actions=checked('.plan-action');
        const restriction=document.querySelector('.restriction-option:checked')?.value||'';
        document.getElementById('treatment-plan-value').value=[actions.length?actions.join(', '):'',restriction?'Restriction: '+restriction:''].filter(Boolean).join(' | ');
        document.getElementById('prescriptions-value').value=actions.filter(x=>['Antalgique','Anti-inflammatoire'].includes(x)).join(', ');
        const delay=Number(value('followup-delay'));
        document.getElementById('follow-up-value').value=[delay?'Contrôle dans '+delay+' jour(s)':'Suivi selon évolution',value('followup-detail')].filter(Boolean).join(' · ');
        if(delay){ const d=new Date(); d.setDate(d.getDate()+delay); document.getElementById('next-checkup-value').value=d.toISOString().slice(0,10); }
    };
    form.addEventListener('change',compose); form.addEventListener('input',compose); form.addEventListener('submit',compose); compose();
});
</script>
@endsection

@extends('layouts.app')

@section('title', 'Nouvelle visite médicale - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-6 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <a href="{{ route('health-records.index') }}" class="text-sm text-blue-600 hover:text-blue-800">← Poste de travail médical</a>
            <h1 class="text-3xl font-bold text-gray-900 mt-2">Nouvelle visite médicale</h1>
            <p class="text-gray-600 mt-1">Documentez la consultation du jour. Les examens spécialisés peuvent être ajoutés ensuite si nécessaire.</p>
        </div>
        <a href="{{ route('health-records.create', array_merge(request()->query(), ['advanced' => 1])) }}"
           class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
            Mode avancé / ancien formulaire
        </a>
    </div>

    <form method="POST" action="{{ route('health-records.store') }}" class="space-y-6" id="clinical-visit-form">
        @csrf
        <input type="hidden" name="workflow" value="visit">
        @if(request('visit_id'))<input type="hidden" name="visit_id" value="{{ request('visit_id') }}">@endif

        @if($visit)
            @php
                $preIntake = data_get($visit->administrative_data, 'pre_intake', []);
            @endphp
            <section class="bg-amber-50/60 border border-amber-200 rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-amber-200 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
                    <div>
                        <div class="text-xs uppercase tracking-wide font-semibold text-amber-700">Transmission du secrétariat</div>
                        <h2 class="font-semibold text-slate-900 mt-1">Pré-accueil terminé</h2>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-white text-amber-800 border border-amber-200">
                        À confirmer médicalement
                    </span>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Motif confirmé</div>
                        <div class="mt-1 text-slate-800">{{ data_get($preIntake,'reason_confirmed') ?: $visit->appointment?->reason ?: 'Non précisé' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Symptômes rapportés</div>
                        <div class="mt-1 text-slate-800">{{ data_get($preIntake,'symptoms_summary') ?: 'Non renseignés' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Allergies déclarées</div>
                        <div class="mt-1 text-slate-800">{{ data_get($preIntake,'patient_reported_allergies') ?: 'Non renseignées' }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Traitements déclarés</div>
                        <div class="mt-1 text-slate-800">{{ data_get($preIntake,'patient_reported_medications') ?: 'Non renseignés' }}</div>
                    </div>
                </div>
                @if($visit->documents->count())
                    <div class="px-5 py-4 border-t border-amber-200 bg-white/60">
                        <div class="text-xs uppercase tracking-wide font-semibold text-slate-400 mb-2">Documents reçus à l’accueil</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($visit->documents as $document)
                                <a href="{{ route('medical-files.document', $document) }}" class="inline-flex px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-xs text-blue-700 hover:bg-blue-50">
                                    {{ $document->file_name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b bg-slate-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-gray-900">1. Contexte de la visite</h2>
                        <p class="text-sm text-gray-500">Patient, date, type et motif principal.</p>
                    </div>
                    <span class="text-xs px-2 py-1 rounded-full bg-blue-100 text-blue-700">Étape essentielle</span>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Joueur</label>
                    <select name="player_id" required class="w-full border-gray-300 rounded-lg">
                        <option value="">Sélectionner un joueur</option>
                        @foreach($players as $player)
                            <option value="{{ $player->id }}" @selected(old('player_id', $defaultValues['player_id'] ?? null)==$player->id)>
                                {{ $player->full_name ?? $player->name }}@if($player->club) · {{ $player->club->name }}@endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="visit_date" required value="{{ old('visit_date', $defaultValues['visit_date'] ?? now()->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Médecin</label>
                    <input type="text" name="doctor_name" required value="{{ old('doctor_name', $defaultValues['doctor_name'] ?? auth()->user()->name ?? '') }}" class="w-full border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="visit_type" required class="w-full border-gray-300 rounded-lg">
                        @foreach([
                            'consultation'=>'Consultation',
                            'follow_up'=>'Suivi',
                            'emergency'=>'Urgence',
                            'pre_season'=>'Pré-saison',
                            'post_match'=>'Post-match',
                            'rehabilitation'=>'Rééducation'
                        ] as $value=>$label)
                            <option value="{{ $value }}" @selected(old('visit_type', $defaultValues['visit_type'] ?? 'consultation')===$value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div></div>
                <div class="md:col-span-2 space-y-4">
                    @include('health-records.partials.clinical-body-map')
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motif principal</label>
                            <select id="complaint-type" class="w-full border-gray-300 rounded-lg">
                                <option value="">Sélectionner</option>
                                <option>Douleur</option>
                                <option>Blessure aiguë</option>
                                <option>Suivi de blessure</option>
                                <option>Fatigue</option>
                                <option>Symptômes respiratoires</option>
                                <option>Symptômes digestifs</option>
                                <option>Symptômes neurologiques</option>
                                <option>Contrôle médical</option>
                                <option>Pré-saison / aptitude</option>
                                <option>Autre</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Région</label>
                            <select id="complaint-region" class="w-full border-gray-300 rounded-lg">
                                <option value="">Non précisée</option>
                                <option>Tête / cou</option>
                                <option>Épaule</option>
                                <option>Bras / coude</option>
                                <option>Avant-bras</option>
                                <option>Poignet / main</option>
                                <option>Thorax</option>
                                <option>Abdomen</option>
                                <option>Dos / rachis</option>
                                <option>Bassin / hanche</option>
                                <option>Cuisse</option>
                                <option>Genou</option>
                                <option>Jambe</option>
                                <option>Cheville</option>
                                <option>Pied</option>
                                <option>Général</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Côté</label>
                            <select id="complaint-side" class="w-full border-gray-300 rounded-lg">
                                <option value="">Sans latéralité</option>
                                <option>Gauche</option>
                                <option>Droite</option>
                                <option>Bilatéral</option>
                                <option>Médian</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contexte</label>
                            <select id="complaint-context" class="w-full border-gray-300 rounded-lg">
                                <option value="">Non précisé</option>
                                <option>Match</option>
                                <option>Entraînement</option>
                                <option>Traumatisme direct</option>
                                <option>Sans traumatisme</option>
                                <option>Effort progressif</option>
                                <option>Repos</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Précision facultative</label>
                        <input id="complaint-detail" type="text" class="w-full border-gray-300 rounded-lg"
                               placeholder="Ex. depuis 3 jours, après changement de direction">
                    </div>
                    <input type="hidden" name="chief_complaint" id="chief-complaint-value" value="{{ old('chief_complaint') }}">
                </div>
            </div>
        </div>

        @if($selectedPlayer)
        @php
            $latest = $selectedPlayer->healthRecords()->orderByDesc('record_date')->first();
        @endphp
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-semibold text-gray-900">Contexte médical connu</h2>
                <p class="text-sm text-gray-500">Lecture seule pendant la consultation. Modifiez uniquement si une information a réellement changé.</p>
            </div>
            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-4 text-sm">
                <div class="rounded-lg bg-red-50 border border-red-100 p-4">
                    <div class="font-semibold text-red-800 mb-2">Allergies</div>
                    <div class="text-red-900">{{ $latest && $latest->allergies ? implode(', ', array_map(fn($v)=>is_scalar($v)?$v:json_encode($v), $latest->allergies)) : 'Aucune renseignée' }}</div>
                </div>
                <div class="rounded-lg bg-blue-50 border border-blue-100 p-4">
                    <div class="font-semibold text-blue-800 mb-2">Traitements</div>
                    <div class="text-blue-900">{{ $latest && $latest->medications ? implode(', ', array_map(fn($v)=>is_scalar($v)?$v:json_encode($v), $latest->medications)) : 'Aucun renseigné' }}</div>
                </div>
                <div class="rounded-lg bg-amber-50 border border-amber-100 p-4">
                    <div class="font-semibold text-amber-800 mb-2">Dernier diagnostic</div>
                    <div class="text-amber-900">{{ $latest?->diagnosis ?: 'Aucun renseigné' }}</div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-semibold text-gray-900">2. Histoire et examen du jour</h2>
                <p class="text-sm text-gray-500">Renseignez uniquement ce qui est pertinent pour cette visite.</p>
            </div>
            <div class="p-6 space-y-5">
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Début</label>
                            <select id="history-onset" class="w-full border-gray-300 rounded-lg">
                                <option value="">Non précisé</option>
                                <option>Aujourd’hui</option>
                                <option>1–3 jours</option>
                                <option>4–7 jours</option>
                                <option>1–4 semaines</option>
                                <option>Plus d’un mois</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Installation</label>
                            <select id="history-mode" class="w-full border-gray-300 rounded-lg">
                                <option value="">Non précisée</option>
                                <option>Brutale</option>
                                <option>Progressive</option>
                                <option>Récidivante</option>
                                <option>Post-traumatique</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Évolution</label>
                            <select id="history-evolution" class="w-full border-gray-300 rounded-lg">
                                <option value="">Non précisée</option>
                                <option>Amélioration</option>
                                <option>Stable</option>
                                <option>Aggravation</option>
                                <option>Fluctuante</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-700 mb-2">Symptômes associés</div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                            @foreach(['Gonflement','Instabilité','Blocage','Craquement','Raideur','Faiblesse','Engourdissement','Douleur nocturne'] as $symptom)
                                <label class="flex items-center gap-2 border rounded-lg px-3 py-2 bg-white">
                                    <input type="checkbox" class="history-symptom" value="{{ $symptom }}">
                                    <span>{{ $symptom }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes complémentaires</label>
                        <textarea id="history-detail" rows="2" class="w-full border-gray-300 rounded-lg"
                                  placeholder="Uniquement ce qui n’est pas couvert par les choix ci-dessus"></textarea>
                    </div>
                    <input type="hidden" name="visit_notes" id="visit-notes-value" value="{{ old('visit_notes') }}">
                </div>

                <details class="border rounded-lg">
                    <summary class="cursor-pointer px-4 py-3 font-medium text-gray-800 bg-gray-50 rounded-lg">Constantes <span class="text-sm font-normal text-gray-500">— facultatif</span></summary>
                    <div class="p-4 grid grid-cols-2 md:grid-cols-6 gap-3">
                        <div><label class="text-xs text-gray-600">TA syst.</label><input name="blood_pressure_systolic" type="number" class="w-full border-gray-300 rounded-md"></div>
                        <div><label class="text-xs text-gray-600">TA diast.</label><input name="blood_pressure_diastolic" type="number" class="w-full border-gray-300 rounded-md"></div>
                        <div><label class="text-xs text-gray-600">FC</label><input name="heart_rate" type="number" class="w-full border-gray-300 rounded-md"></div>
                        <div><label class="text-xs text-gray-600">Temp. °C</label><input name="temperature" type="number" step="0.1" class="w-full border-gray-300 rounded-md"></div>
                        <div><label class="text-xs text-gray-600">Poids kg</label><input name="weight" type="number" step="0.1" class="w-full border-gray-300 rounded-md"></div>
                        <div><label class="text-xs text-gray-600">Taille cm</label><input name="height" type="number" step="0.1" class="w-full border-gray-300 rounded-md"></div>
                    </div>
                </details>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Examen ciblé</label>
                            <select id="exam-system" class="w-full border-gray-300 rounded-lg">
                                <option value="">Sélectionner</option>
                                <option>Musculosquelettique</option>
                                <option>Neurologique</option>
                                <option>Cardiovasculaire</option>
                                <option>Respiratoire</option>
                                <option>Abdominal</option>
                                <option>ORL</option>
                                <option>Général</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mobilité</label>
                            <select id="exam-mobility" class="w-full border-gray-300 rounded-lg">
                                <option value="">Non évaluée</option>
                                <option>Normale</option>
                                <option>Limitée</option>
                                <option>Douloureuse</option>
                                <option>Hypermobile</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Force</label>
                            <select id="exam-strength" class="w-full border-gray-300 rounded-lg">
                                <option value="">Non évaluée</option>
                                <option>Normale</option>
                                <option>Légèrement diminuée</option>
                                <option>Modérément diminuée</option>
                                <option>Très diminuée</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-700 mb-2">Constats cliniques</div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                            @foreach(['Douleur à la palpation','Œdème','Ecchymose','Déformation','Instabilité','Déficit neurologique','Test spécifique positif','Examen normal'] as $finding)
                                <label class="flex items-center gap-2 border rounded-lg px-3 py-2 bg-white">
                                    <input type="checkbox" class="exam-finding" value="{{ $finding }}">
                                    <span>{{ $finding }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Détail de l’examen</label>
                        <textarea id="exam-detail" rows="2" class="w-full border-gray-300 rounded-lg"
                                  placeholder="Tests spécifiques, valeur chiffrée ou précision utile"></textarea>
                    </div>
                    <input type="hidden" name="physical_examination" id="physical-examination-value" value="{{ old('physical_examination') }}">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-semibold text-gray-900">3. Évaluation et plan</h2>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Impression clinique</label>
                        <select id="diagnosis-category" class="w-full border-gray-300 rounded-lg">
                            <option value="">Non concluant / à préciser</option>
                            <option>Traumatisme musculosquelettique</option>
                            <option>Lésion musculaire probable</option>
                            <option>Entorse probable</option>
                            <option>Tendinopathie probable</option>
                            <option>Contusion</option>
                            <option>Surcharge / surmenage</option>
                            <option>Symptômes infectieux</option>
                            <option>Évaluation normale</option>
                            <option>Autre</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Niveau de certitude</label>
                        <select id="diagnosis-certainty" class="w-full border-gray-300 rounded-lg">
                            <option value="">Non précisé</option>
                            <option>Hypothèse</option>
                            <option>Probable</option>
                            <option>Confirmé</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Précision diagnostique facultative</label>
                    <input id="diagnosis-detail" type="text" class="w-full border-gray-300 rounded-lg"
                           placeholder="Ex. entorse LLE genou droit">
                    <input type="hidden" name="diagnosis" id="diagnosis-value" value="{{ old('diagnosis') }}">
                </div>

                <div>
                    <div class="text-sm font-medium text-gray-700 mb-2">Conduite à tenir</div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                        @foreach(['Repos relatif','Glace','Compression','Élévation','Antalgique','Anti-inflammatoire','Kinésithérapie','Imagerie','Avis spécialiste','Réévaluation'] as $plan)
                            <label class="flex items-center gap-2 border rounded-lg px-3 py-2 bg-white">
                                <input type="checkbox" class="plan-action" value="{{ $plan }}">
                                <span>{{ $plan }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="border border-slate-200 rounded-xl p-4 bg-slate-50/60">
                    <div class="text-sm font-semibold text-slate-800">Actes / modules à programmer</div>
                    <p class="text-xs text-slate-500 mt-1 mb-3">Sélectionnez uniquement les actes décidés pendant cette consultation. Ils seront transmis au secrétariat.</p>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 text-sm">
                        @foreach([
                            'pcma'=>'PCMA',
                            'fmarc'=>'F-MARC / blessure',
                            'scat'=>'SCAT / commotion',
                            'imaging'=>'Imagerie',
                            'mri'=>'IRM',
                            'mapa'=>'MAPA',
                            'ecg_effort'=>'ECG d’effort',
                            'laboratory'=>'Laboratoire',
                            'dental'=>'Dentaire',
                            'postural'=>'Posture',
                            'specialist'=>'Avis spécialiste',
                            'physiotherapy'=>'Kinésithérapie',
                        ] as $value=>$label)
                            <label class="flex items-center gap-2 bg-white border rounded-lg px-3 py-2">
                                <input type="checkbox" name="prescribed_modules[]" class="prescribed-module" value="{{ $value }}" @checked(in_array($value, old('prescribed_modules', []), true))>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordonnance / prescription</label>
                    <textarea id="prescription-detail" rows="2" class="w-full border-gray-300 rounded-lg"
                              placeholder="Médicaments, posologie ou autres prescriptions décidées par le médecin">{{ old('prescriptions') }}</textarea>
                </div>

                <div>
                    <div class="text-sm font-medium text-gray-700 mb-2">Restriction sportive</div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        @foreach(['Aucune','Entraînement adapté','Sans contact','Pas de compétition','Arrêt sportif'] as $restriction)
                            <label class="border rounded-lg px-3 py-2 text-sm flex items-center gap-2 bg-white">
                                <input type="radio" name="restriction_ui" class="restriction-option" value="{{ $restriction }}">
                                <span>{{ $restriction }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Délai de suivi</label>
                        <select id="followup-delay" class="w-full border-gray-300 rounded-lg">
                            <option value="">Selon évolution</option>
                            <option value="1">24 h</option>
                            <option value="2">48 h</option>
                            <option value="3">72 h</option>
                            <option value="7">7 jours</option>
                            <option value="14">14 jours</option>
                            <option value="30">1 mois</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Précision de suivi</label>
                        <input id="followup-detail" type="text" class="w-full border-gray-300 rounded-lg"
                               placeholder="Ex. contrôle après IRM">
                    </div>
                </div>

                <input type="hidden" name="treatment_plan" id="treatment-plan-value" value="{{ old('treatment_plan') }}">
                <input type="hidden" name="prescriptions" id="prescriptions-value" value="{{ old('prescriptions') }}">
                <input type="hidden" name="follow_up_instructions" id="follow-up-value" value="{{ old('follow_up_instructions') }}">
                <input type="hidden" name="next_checkup_date" id="next-checkup-value">
            </div>
        </div>

        <div class="sticky bottom-4 bg-white/95 backdrop-blur border border-gray-200 rounded-xl shadow-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-sm text-gray-600">À la fin de la visite : clôture simple ou transmission des actes prescrits au secrétariat.</div>
            <div class="flex gap-3">
                <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">Annuler</a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Terminer la visite</button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('clinical-visit-form');
    if (!form) return;

    const value = id => document.getElementById(id)?.value?.trim() || '';
    const checked = selector => Array.from(document.querySelectorAll(selector + ':checked')).map(el => el.value);

    const compose = () => {
        const complaint = [
            value('complaint-type'),
            value('complaint-region'),
            value('complaint-anatomical-detail') ? 'Zone: ' + value('complaint-anatomical-detail') : '',
            value('complaint-side'),
            value('complaint-context') ? 'Contexte: ' + value('complaint-context') : '',
            value('complaint-detail')
        ].filter(Boolean).join(' · ');
        document.getElementById('chief-complaint-value').value = complaint;

        const symptoms = checked('.history-symptom');
        const history = [
            value('history-onset') ? 'Début: ' + value('history-onset') : '',
            value('history-mode') ? 'Installation: ' + value('history-mode') : '',
            value('history-evolution') ? 'Évolution: ' + value('history-evolution') : '',
            symptoms.length ? 'Associés: ' + symptoms.join(', ') : '',
            value('history-detail')
        ].filter(Boolean).join(' | ');
        document.getElementById('visit-notes-value').value = history;

        const findings = checked('.exam-finding');
        const exam = [
            value('exam-system') ? 'Examen: ' + value('exam-system') : '',
            value('exam-mobility') ? 'Mobilité: ' + value('exam-mobility') : '',
            value('exam-strength') ? 'Force: ' + value('exam-strength') : '',
            findings.length ? 'Constats: ' + findings.join(', ') : '',
            value('exam-detail')
        ].filter(Boolean).join(' | ');
        document.getElementById('physical-examination-value').value = exam;

        const diagnosis = [
            value('diagnosis-category'),
            value('diagnosis-certainty') ? '(' + value('diagnosis-certainty') + ')' : '',
            value('diagnosis-detail')
        ].filter(Boolean).join(' ');
        document.getElementById('diagnosis-value').value = diagnosis;

        const actions = checked('.plan-action');
        const restriction = document.querySelector('.restriction-option:checked')?.value || '';
        document.getElementById('treatment-plan-value').value = [
            actions.length ? actions.join(', ') : '',
            restriction ? 'Restriction: ' + restriction : ''
        ].filter(Boolean).join(' | ');

        const medicationActions = actions.filter(x => ['Antalgique','Anti-inflammatoire'].includes(x));
        document.getElementById('prescriptions-value').value = [
            medicationActions.length ? medicationActions.join(', ') : '',
            value('prescription-detail')
        ].filter(Boolean).join(' | ');

        const prescribedModules = checked('.prescribed-module');
        if (prescribedModules.length) {
            const currentPlan = document.getElementById('treatment-plan-value').value;
            document.getElementById('treatment-plan-value').value = [
                currentPlan,
                'Actes prescrits: ' + prescribedModules.join(', ')
            ].filter(Boolean).join(' | ');
        }

        const delay = Number(value('followup-delay'));
        const followupDetail = value('followup-detail');
        document.getElementById('follow-up-value').value = [
            delay ? 'Contrôle dans ' + delay + ' jour(s)' : 'Suivi selon évolution',
            followupDetail
        ].filter(Boolean).join(' · ');

        const next = document.getElementById('next-checkup-value');
        if (delay) {
            const d = new Date();
            d.setDate(d.getDate() + delay);
            next.value = d.toISOString().slice(0,10);
        } else {
            next.value = '';
        }
    };

    form.addEventListener('change', compose);
    form.addEventListener('input', compose);
    form.addEventListener('submit', compose);
    compose();
});
</script>
@endsection

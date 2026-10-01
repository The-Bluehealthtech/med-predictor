@extends('layouts.app')

@section('title', 'Tableau de bord santé - Med Predictor')

@section('content')
@php
    $records = $dopingRecords;
    $player = $healthRecord->player;
    $latestWithAllergies = $records->first(fn($record) => !empty($record->allergies));
    $latestWithMedications = $records->first(fn($record) => !empty($record->medications));
    $latestWithDiagnosis = $records->first(fn($record) => !empty($record->diagnosis));
    $nextFollowUp = $records->filter(fn($record) => $record->next_checkup_date && $record->next_checkup_date->isFuture())
        ->sortBy('next_checkup_date')->first();
@endphp

<div class="container mx-auto px-4 py-8 max-w-7xl" id="medical-workspace">
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-slate-100 border flex items-center justify-center overflow-hidden">
                    @if($player?->player_picture_url)
                        <img src="{{ $player->player_picture_url }}" alt="" class="w-full h-full object-cover">
                    @else
                        <span class="text-xl font-semibold text-slate-600">{{ mb_substr($player?->full_name ?? 'P',0,1) }}</span>
                    @endif
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $player?->full_name ?? 'Joueur' }}</h1>
                        @if($player?->age)
                            <span class="text-sm text-gray-500">{{ $player->age }} ans</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $player?->club?->name ?? 'Club non renseigné' }}
                        @if($player?->position) · {{ $player->position }} @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button" data-medical-workspace-tab-target="exams"
                   class="px-4 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">
                    + Ajouter un module
                </button>
                <a href="{{ route('health-records.show', ['healthRecord'=>$healthRecord,'legacy'=>1]) }}"
                   class="px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">
                    Mode détaillé
                </a>
            </div>
        </div>

        <div class="border-t bg-slate-50 px-4 overflow-x-auto">
            <nav class="flex gap-1 min-w-max" aria-label="Dossier médical">
                @foreach([
                    'summary'=>'Tableau de bord',
                    'visits'=>'Parcours de soins',
                    'exams'=>'Modules spécialisés',
                    'documents'=>'Documents'
                ] as $tab=>$label)
                    <button type="button" data-medical-workspace-tab="{{ $tab }}"
                            class="workspace-tab px-4 py-3 text-sm font-medium border-b-2 {{ $tab==='summary' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-600' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    <section data-medical-workspace-panel="summary" class="space-y-6">
        @if($vigilance)
        @php
            $vigilanceStyles = [
                'clear' => ['bg'=>'bg-emerald-50','border'=>'border-emerald-200','text'=>'text-emerald-800','badge'=>'bg-emerald-100 text-emerald-800','label'=>'Cohérent'],
                'incomplete' => ['bg'=>'bg-sky-50','border'=>'border-sky-200','text'=>'text-sky-900','badge'=>'bg-sky-100 text-sky-900','label'=>'Données insuffisantes'],
                'review' => ['bg'=>'bg-amber-50','border'=>'border-amber-200','text'=>'text-amber-900','badge'=>'bg-amber-100 text-amber-900','label'=>'À vérifier'],
                'attention' => ['bg'=>'bg-red-50','border'=>'border-red-200','text'=>'text-red-900','badge'=>'bg-red-100 text-red-900','label'=>'Attention'],
            ];
        @endphp
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-200 bg-slate-50/70 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <div class="text-xs uppercase tracking-[0.14em] font-semibold text-blue-700">Vigilance joueur</div>
                    <h2 class="font-semibold text-slate-950 mt-1">Contrôles explicables</h2>
                    <p class="text-sm text-slate-500 mt-1">Signaux de cohérence et de surveillance issus des données présentes dans FIT. Aucun verdict de fraude ni pronostic médical automatique.</p>
                </div>
                <span class="text-xs text-slate-400">Règles {{ $vigilance['method'] }}</span>
            </div>
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 p-5">
                @foreach(['identity'=>'Identité / âge','injury'=>'Blessure','cardiac'=>'Cardiovasculaire'] as $axisKey=>$axisTitle)
                    @php
                        $axis = $vigilance[$axisKey];
                        $style = $vigilanceStyles[$axis['status']];
                    @endphp
                    <details class="rounded-xl border {{ $style['border'] }} {{ $style['bg'] }} p-4 group">
                        <summary class="list-none cursor-pointer">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-semibold {{ $style['text'] }}">{{ $axisTitle }}</div>
                                    <div class="text-xs text-slate-500 mt-1">{{ count($axis['flags']) }} point(s) signalé(s)</div>
                                </div>
                                <span class="shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold {{ $style['badge'] }}">{{ $style['label'] }}</span>
                            </div>
                        </summary>
                        <div class="mt-4 pt-4 border-t border-black/5 space-y-3">
                            @foreach($axis['flags'] as $flag)
                                @php
                                    $flagClass = match($flag['severity']) {
                                        'attention' => 'text-red-800',
                                        'review' => 'text-amber-900',
                                        'incomplete' => 'text-sky-900',
                                        default => 'text-slate-600',
                                    };
                                @endphp
                                <div class="flex gap-2 text-sm {{ $flagClass }}">
                                    <span class="mt-0.5">{{ $flag['severity']==='attention' ? '!' : ($flag['severity']==='review' ? '•' : ($flag['severity']==='incomplete' ? '○' : 'i')) }}</span>
                                    <span>
                                        {{ $flag['label'] }}
                                        @if(!empty($flag['source']))
                                            <span class="block mt-0.5 text-[11px] text-slate-500">Source de règle : {{ $flag['source'] }}</span>
                                        @endif
                                    </span>
                                </div>
                            @endforeach
                            @foreach($axis['confirmed'] as $item)
                                <div class="flex gap-2 text-sm text-emerald-800"><span>✓</span><span>{{ $item }}</span></div>
                            @endforeach
                            <p class="text-xs text-slate-500 leading-5 pt-2">{{ $axis['notice'] }}</p>
                            @if(!empty($axis['references']))
                                <div class="pt-3 border-t border-black/5">
                                    <div class="text-[11px] uppercase tracking-wide font-semibold text-slate-500 mb-2">Référentiels externes</div>
                                    <div class="space-y-1.5">
                                        @foreach($axis['references'] as $reference)
                                            <a href="{{ $reference['url'] }}" target="_blank" rel="noopener noreferrer"
                                               class="block text-xs text-blue-700 hover:text-blue-900 hover:underline">
                                                {{ $reference['title'] }} ↗
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden" id="clinical-assistant-panel"
             data-assistant-url="{{ route('health-records.assistant', $healthRecord) }}">
            <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-950 to-blue-950 text-white flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div>
                    <div class="text-xs uppercase tracking-[0.14em] font-semibold text-blue-200">Assistant clinique</div>
                    <h2 class="font-semibold text-lg mt-1">Aide à la revue du dossier</h2>
                    <p class="text-sm text-slate-300 mt-1">Lecture seule · aucune suggestion n’est enregistrée automatiquement dans le dossier.</p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full bg-white/10 text-blue-100 border border-white/10">Mode explicable local</span>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-[320px_1fr]">
                <div class="p-5 border-b xl:border-b-0 xl:border-r border-slate-200 space-y-3">
                    <button type="button" data-clinical-assistant-action="analyze"
                            class="w-full text-left rounded-xl border border-blue-200 bg-blue-50 hover:bg-blue-100 p-4 transition">
                        <div class="font-semibold text-blue-950">Analyser le dossier</div>
                        <div class="text-xs text-blue-700 mt-1">Synthèse des éléments de vigilance et de l’historique récent.</div>
                    </button>
                    <button type="button" data-clinical-assistant-action="verify"
                            class="w-full text-left rounded-xl border border-slate-200 hover:border-amber-300 hover:bg-amber-50 p-4 transition">
                        <div class="font-semibold text-slate-900">Vérifier avant validation</div>
                        <div class="text-xs text-slate-500 mt-1">Repère les données absentes, anciennes ou à confirmer.</div>
                    </button>
                    <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-600 leading-5">
                        L’assistant actuel n’établit ni diagnostic, ni aptitude, ni fraude, ni probabilité de mort subite ou de blessure.
                    </div>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label for="clinical-assistant-question" class="text-sm font-semibold text-slate-900">Poser une question sur ce dossier</label>
                        <textarea id="clinical-assistant-question" rows="3" maxlength="1200"
                                  class="mt-2 w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm"
                                  placeholder="Ex. Quels éléments cardiovasculaires sont à vérifier ?"></textarea>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <button type="button" data-clinical-assistant-action="question"
                                    class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Envoyer</button>
                            <button type="button" data-clinical-assistant-example="Quels éléments cardiovasculaires sont à vérifier ?"
                                    class="px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-600 hover:bg-slate-50">Cardiovasculaire</button>
                            <button type="button" data-clinical-assistant-example="Y a-t-il des signaux de récidive de blessure ?"
                                    class="px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-600 hover:bg-slate-50">Blessures</button>
                            <button type="button" data-clinical-assistant-example="Quelles données d’identité ou d’âge sont à vérifier ?"
                                    class="px-3 py-2 rounded-lg border border-slate-200 text-xs text-slate-600 hover:bg-slate-50">Identité / âge</button>
                        </div>
                    </div>

                    <div id="clinical-assistant-result" class="hidden rounded-xl border border-slate-200 bg-slate-50 p-4" aria-live="polite">
                        <div class="flex items-center justify-between gap-3">
                            <h3 id="clinical-assistant-title" class="font-semibold text-slate-900">Résultat</h3>
                            <span id="clinical-assistant-mode" class="text-[11px] text-slate-400"></span>
                        </div>
                        <p id="clinical-assistant-summary" class="text-sm text-slate-700 mt-2 leading-6"></p>
                        <ul id="clinical-assistant-items" class="mt-3 space-y-2 text-sm text-slate-700"></ul>
                        <div id="clinical-assistant-sources" class="mt-4 pt-3 border-t border-slate-200 text-xs text-slate-500"></div>
                    </div>

                    <div id="clinical-assistant-loading" class="hidden text-sm text-slate-500">Analyse du dossier en cours…</div>
                    <div id="clinical-assistant-error" class="hidden rounded-lg bg-red-50 border border-red-200 text-red-800 p-3 text-sm"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <div class="rounded-xl border border-red-200 bg-red-50 p-5">
                <div class="text-xs uppercase tracking-wide font-semibold text-red-700 mb-2">Allergies</div>
                <div class="text-sm text-red-950">
                    @if($latestWithAllergies)
                        {{ implode(', ', array_map(fn($v)=>is_scalar($v)?$v:json_encode($v), $latestWithAllergies->allergies)) }}
                    @else
                        Aucune allergie renseignée
                    @endif
                </div>
            </div>
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-5">
                <div class="text-xs uppercase tracking-wide font-semibold text-blue-700 mb-2">Traitements actuels</div>
                <div class="text-sm text-blue-950">
                    @if($latestWithMedications)
                        {{ implode(', ', array_map(fn($v)=>is_scalar($v)?$v:json_encode($v), $latestWithMedications->medications)) }}
                    @else
                        Aucun traitement renseigné
                    @endif
                </div>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5">
                <div class="text-xs uppercase tracking-wide font-semibold text-amber-700 mb-2">Prochain suivi</div>
                <div class="text-sm text-amber-950">
                    {{ $nextFollowUp?->next_checkup_date?->format('d/m/Y') ?? 'Non planifié' }}
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h2 class="font-semibold text-slate-900">Modules du dossier santé</h2>
                    <p class="text-sm text-slate-500 mt-1">Les modules spécialisés enrichissent ce dossier longitudinal sans créer de dossier parallèle.</p>
                </div>
                <button type="button" data-medical-workspace-tab-target="exams" class="text-sm font-semibold text-blue-600">Tous les modules →</button>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-6 gap-3">
                @foreach([
                    ['PCMA',$pcmaRecords->count(),'🩺'],
                    ['F-MARC',count($sectionHistory['fmarc'] ?? []),'🦵'],
                    ['SCAT',count($sectionHistory['scat'] ?? []),'🧠'],
                    ['Imagerie',count($sectionHistory['imaging'] ?? []),'🩻'],
                    ['Dentaire',count($sectionHistory['dental'] ?? []),'🦷'],
                    ['Biologie',count($sectionHistory['biological'] ?? []),'🧬'],
                ] as [$label,$count,$icon])
                    <button type="button" data-medical-workspace-tab-target="exams"
                            class="text-left border border-slate-200 rounded-xl p-3 hover:border-blue-300 hover:bg-blue-50/30">
                        <div class="text-lg">{{ $icon }}</div>
                        <div class="mt-2 text-sm font-semibold text-slate-800">{{ $label }}</div>
                        <div class="text-xs text-slate-500 mt-1">{{ $count }} entrée(s)</div>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm">
                <div class="px-5 py-4 border-b flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-gray-900">Chronologie récente</h2>
                        <p class="text-sm text-gray-500">Historique des prises en charge et mises à jour du dossier.</p>
                    </div>
                    <button type="button" data-medical-workspace-tab-target="visits" class="text-sm text-blue-600 hover:text-blue-800">Tout voir</button>
                </div>
                <div class="divide-y">
                    @forelse($records->take(6) as $record)
                        <div class="p-5 flex gap-4">
                            <div class="w-20 shrink-0 text-sm text-gray-500">
                                {{ $record->visit_date?->format('d/m/Y') ?? $record->record_date?->format('d/m/Y') }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-900">
                                    {{ ucfirst(str_replace('_',' ', $record->visit_type ?: 'consultation')) }}
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    {{ $record->chief_complaint ?: $record->diagnosis ?: 'Visite médicale' }}
                                </div>
                                @if($record->doctor_name)
                                    <div class="text-xs text-gray-400 mt-1">{{ $record->doctor_name }}</div>
                                @endif
                            </div>
                            <a href="{{ route('health-records.show', $record) }}" class="text-sm text-blue-600">Ouvrir</a>
                        </div>
                    @empty
                        <div class="p-6 text-sm text-gray-500">Aucune visite enregistrée.</div>
                    @endforelse
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h2 class="font-semibold text-gray-900 mb-3">Dernière impression clinique</h2>
                    <div class="text-sm text-gray-700 whitespace-pre-line">{{ $latestWithDiagnosis?->diagnosis ?: 'Aucune renseignée.' }}</div>
                </div>
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <h2 class="font-semibold text-gray-900 mb-3">Examens récents</h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span>Posture</span><strong>{{ $posturalAssessments->count() }}</strong></div>
                        <div class="flex justify-between"><span>PCMA</span><strong>{{ $pcmaRecords->count() }}</strong></div>
                        <div class="flex justify-between"><span>SCAT</span><strong>{{ count($sectionHistory['scat'] ?? []) }}</strong></div>
                        <div class="flex justify-between"><span>Imagerie</span><strong>{{ count($sectionHistory['imaging'] ?? []) }}</strong></div>
                    </div>
                    <button type="button" data-medical-workspace-tab-target="exams" class="mt-4 text-sm text-blue-600">Voir les examens</button>
                </div>
            </div>
        </div>
    </section>

    <section data-medical-workspace-panel="visits" class="hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-gray-900">Parcours de soins</h2>
                    <p class="text-sm text-gray-500">Historique des consultations ayant contribué au dossier longitudinal.</p>
                </div>
                
            </div>
            <div class="divide-y">
                @forelse($records as $record)
                    <div class="p-5 grid grid-cols-1 md:grid-cols-[130px_1fr_auto] gap-4 items-start">
                        <div class="text-sm text-gray-500">{{ $record->visit_date?->format('d/m/Y') ?? $record->record_date?->format('d/m/Y') }}</div>
                        <div>
                            <div class="font-semibold text-gray-900">{{ ucfirst(str_replace('_',' ', $record->visit_type ?: 'consultation')) }}</div>
                            <div class="text-sm text-gray-700 mt-1">{{ $record->chief_complaint ?: 'Motif non renseigné' }}</div>
                            @if($record->diagnosis)<div class="text-sm text-gray-500 mt-2"><strong>Évaluation :</strong> {{ $record->diagnosis }}</div>@endif
                            @if($record->doctor_name)<div class="text-xs text-gray-400 mt-2">{{ $record->doctor_name }}</div>@endif
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('health-records.show', $record) }}" class="px-3 py-1.5 rounded border text-sm">Ouvrir</a>
                            <a href="{{ route('health-records.edit', $record) }}" class="px-3 py-1.5 rounded border text-sm">Modifier</a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-sm text-gray-500">Aucune visite enregistrée.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section data-medical-workspace-panel="exams" class="hidden space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-gray-900">Modules spécialisés du dossier</h2>
                    <p class="text-sm text-gray-500 mt-1">Chaque module ajoute son historique au même dossier santé du joueur.</p>
                </div>
                <div class="text-xs px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 font-semibold">Dossier de base #{{ $healthRecord->id }}</div>
            </div>
        </div>

        <div>
            <div class="mb-3">
                <div class="text-xs uppercase tracking-[0.14em] font-semibold text-slate-400">Prévention médicale</div>
                <p class="text-sm text-slate-500 mt-1">Évaluations préventives et aptitude.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <a href="{{ route('pcma.create', ['player_id'=>$healthRecord->player_id]) }}" class="module-card">
                    <div class="module-icon">🩺</div>
                    <div>
                        <div class="font-semibold text-slate-900">PCMA</div>
                        <div class="text-sm text-slate-500 mt-1">{{ $pcmaRecords->count() }} évaluation(s)</div>
                    </div>
                    <span class="module-arrow">→</span>
                </a>
                <a href="{{ route('health-records.modules.show', ['healthRecord'=>$healthRecord,'module'=>'dental']) }}" class="module-card">
                    <div class="module-icon">🦷</div>
                    <div>
                        <div class="font-semibold text-slate-900">Dentaire</div>
                        <div class="text-sm text-slate-500 mt-1">{{ count($sectionHistory['dental'] ?? []) }} entrée(s)</div>
                    </div>
                    <span class="module-arrow">→</span>
                </a>
                <a href="{{ route('health-records.show', ['healthRecord'=>$healthRecord,'legacy'=>1,'tab'=>'postural']) }}" class="module-card">
                    <div class="module-icon">🧍</div>
                    <div>
                        <div class="font-semibold text-slate-900">Posture</div>
                        <div class="text-sm text-slate-500 mt-1">{{ $posturalAssessments->count() }} évaluation(s)</div>
                    </div>
                    <span class="module-arrow">→</span>
                </a>
            </div>
        </div>

        <div>
            <div class="mb-3">
                <div class="text-xs uppercase tracking-[0.14em] font-semibold text-slate-400">Suivi médical</div>
                <p class="text-sm text-slate-500 mt-1">Blessures, commotions, pathologies, cardiologie et imagerie.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach([
                    ['fmarc','🦵','Blessures / F-MARC'],
                    ['scat','🧠','SCAT / commotion'],
                    ['illness','🩹','Pathologies / maladie'],
                    ['mapa','❤️','MAPA'],
                    ['ecg_effort','📈','ECG d’effort'],
                    ['imaging','🩻','Imagerie'],
                    ['mri','🧲','IRM'],
                    ['scintigraphy','☢️','Scintigraphie'],
                ] as [$key,$icon,$label])
                    <a href="{{ route('health-records.modules.show', ['healthRecord'=>$healthRecord,'module'=>$key]) }}" class="module-card">
                        <div class="module-icon">{{ $icon }}</div>
                        <div>
                            <div class="font-semibold text-slate-900">{{ $label }}</div>
                            <div class="text-sm text-slate-500 mt-1">{{ count($sectionHistory[$key] ?? []) }} entrée(s)</div>
                        </div>
                        <span class="module-arrow">→</span>
                    </a>
                @endforeach
            </div>
        </div>

        <div>
            <div class="mb-3">
                <div class="text-xs uppercase tracking-[0.14em] font-semibold text-slate-400">Anti-dopage & biologie</div>
                <p class="text-sm text-slate-500 mt-1">Démarches réglementaires et données biologiques.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                <a href="{{ route('medical-aut.choose', ['player_id'=>$healthRecord->player_id]) }}" class="module-card">
                    <div class="module-icon">📄</div>
                    <div>
                        <div class="font-semibold text-slate-900">AUT</div>
                        <div class="text-sm text-slate-500 mt-1">{{ $autRequests->count() }} demande(s)</div>
                    </div>
                    <span class="module-arrow">→</span>
                </a>
                <a href="{{ route('health-records.modules.show', ['healthRecord'=>$healthRecord,'module'=>'biological']) }}" class="module-card">
                    <div class="module-icon">🧬</div>
                    <div>
                        <div class="font-semibold text-slate-900">Profil biologique</div>
                        <div class="text-sm text-slate-500 mt-1">{{ count($sectionHistory['biological'] ?? []) }} entrée(s)</div>
                    </div>
                    <span class="module-arrow">→</span>
                </a>
                <a href="{{ route('health-records.modules.show', ['healthRecord'=>$healthRecord,'module'=>'laboratory']) }}" class="module-card">
                    <div class="module-icon">🧪</div>
                    <div>
                        <div class="font-semibold text-slate-900">Laboratoire</div>
                        <div class="text-sm text-slate-500 mt-1">{{ count($sectionHistory['laboratory'] ?? []) }} entrée(s)</div>
                    </div>
                    <span class="module-arrow">→</span>
                </a>
            </div>
        </div>
    </section>

    <section data-medical-workspace-panel="documents" class="hidden">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-semibold text-gray-900">Documents médicaux</h2>
                <p class="text-sm text-gray-500">Documents associés aux examens du joueur.</p>
            </div>
            <div class="divide-y">
                @foreach($intakeDocuments as $document)
                    <div class="p-5 grid grid-cols-1 md:grid-cols-[140px_1fr_180px] gap-4">
                        <div class="text-sm text-gray-500">{{ $document->created_at?->format('d/m/Y') ?? '—' }}</div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $document->file_name }}</div>
                            <div class="text-sm text-gray-500">Secrétariat · {{ $document->document_type_label }}</div>
                        </div>
                        <div class="text-xs text-gray-400 md:text-right">Pièce de pré-accueil</div>
                    </div>
                @endforeach

                @foreach($sectionDocuments as $document)
                    <div class="p-5 grid grid-cols-1 md:grid-cols-[140px_1fr_180px] gap-4">
                        <div class="text-sm text-gray-500">{{ $document->exam_date?->format('d/m/Y') ?? '—' }}</div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $document->original_name }}</div>
                            <div class="text-sm text-gray-500">Module · {{ ucfirst($document->section) }}</div>
                        </div>
                        <div class="text-xs text-gray-400 md:text-right">{{ $document->mime_type }}</div>
                    </div>
                @endforeach

                @if($intakeDocuments->isEmpty() && $sectionDocuments->isEmpty())
                    <div class="p-6 text-sm text-gray-500">Aucun document médical enregistré.</div>
                @endif
            </div>
        </div>
    </section>
</div>

<style>
#medical-workspace .module-card{display:grid;grid-template-columns:auto 1fr auto;align-items:center;gap:.9rem;background:#fff;border:1px solid #e2e8f0;border-radius:.85rem;padding:1rem;transition:.15s}
#medical-workspace .module-card:hover{border-color:#93c5fd;box-shadow:0 2px 8px rgba(15,23,42,.06);transform:translateY(-1px)}
#medical-workspace .module-icon{width:2.5rem;height:2.5rem;border-radius:.7rem;background:#f8fafc;display:flex;align-items:center;justify-content:center;font-size:1.15rem}
#medical-workspace .module-arrow{color:#94a3b8;font-weight:700}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const root = document.getElementById('medical-workspace');
    if (!root) return;

    const showTab = tab => {
        root.querySelectorAll('[data-medical-workspace-panel]').forEach(panel => {
            panel.classList.toggle('hidden', panel.dataset.medicalWorkspacePanel !== tab);
        });
        root.querySelectorAll('[data-medical-workspace-tab]').forEach(button => {
            const active = button.dataset.medicalWorkspaceTab === tab;
            button.classList.toggle('border-blue-600', active);
            button.classList.toggle('text-blue-700', active);
            button.classList.toggle('border-transparent', !active);
            button.classList.toggle('text-gray-600', !active);
        });
        const url = new URL(window.location.href);
        url.searchParams.set('workspace', tab);
        history.replaceState({}, '', url);
    };

    root.querySelectorAll('[data-medical-workspace-tab]').forEach(button => {
        button.addEventListener('click', () => showTab(button.dataset.medicalWorkspaceTab));
    });

    root.querySelectorAll('[data-medical-workspace-tab-target]').forEach(button => {
        button.addEventListener('click', () => showTab(button.dataset.medicalWorkspaceTabTarget));
    });

    const initial = new URLSearchParams(window.location.search).get('workspace');
    if (['summary','visits','exams','documents'].includes(initial)) showTab(initial);

    const assistant = document.getElementById('clinical-assistant-panel');
    if (assistant) {
        const question = document.getElementById('clinical-assistant-question');
        const result = document.getElementById('clinical-assistant-result');
        const title = document.getElementById('clinical-assistant-title');
        const summary = document.getElementById('clinical-assistant-summary');
        const items = document.getElementById('clinical-assistant-items');
        const sources = document.getElementById('clinical-assistant-sources');
        const modeLabel = document.getElementById('clinical-assistant-mode');
        const loading = document.getElementById('clinical-assistant-loading');
        const error = document.getElementById('clinical-assistant-error');

        const runAssistant = async mode => {
            error.classList.add('hidden');
            result.classList.add('hidden');
            loading.classList.remove('hidden');

            try {
                const response = await fetch(assistant.dataset.assistantUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        mode,
                        question: mode === 'question' ? question.value.trim() : null,
                    }),
                });

                const payload = await response.json();
                if (!response.ok || !payload.success) {
                    throw new Error(payload.message || 'Impossible d’analyser le dossier.');
                }

                const data = payload.assistant || {};
                title.textContent = data.title || 'Assistant clinique';
                summary.textContent = data.summary || '';
                modeLabel.textContent = data.mode || '';
                items.innerHTML = '';
                (data.items || []).forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'flex gap-2';
                    const dot = document.createElement('span');
                    dot.className = 'text-blue-600 font-bold';
                    dot.textContent = '•';
                    const value = document.createElement('span');
                    value.textContent = item;
                    li.append(dot, value);
                    items.appendChild(li);
                });
                sources.textContent = (data.sources || []).length
                    ? 'Sources du dossier : ' + data.sources.join(' · ')
                    : '';
                result.classList.remove('hidden');
            } catch (e) {
                error.textContent = e.message || 'Erreur de l’assistant clinique.';
                error.classList.remove('hidden');
            } finally {
                loading.classList.add('hidden');
            }
        };

        assistant.querySelectorAll('[data-clinical-assistant-action]').forEach(button => {
            button.addEventListener('click', () => runAssistant(button.dataset.clinicalAssistantAction));
        });
        assistant.querySelectorAll('[data-clinical-assistant-example]').forEach(button => {
            button.addEventListener('click', () => {
                question.value = button.dataset.clinicalAssistantExample;
                question.focus();
            });
        });
    }
});
</script>
@endsection

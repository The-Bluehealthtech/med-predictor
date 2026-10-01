@extends('layouts.app')

@section('title', 'Module spécialisé - Dossier santé')

@section('content')
@php
    $player = $healthRecord->player;
    $moduleLabel = __('medical_sections.'.$module);
    $moduleDescriptions = [
        'dental' => 'Suivi bucco-dentaire et odontologique',
        'scat' => 'Évaluation structurée des traumatismes crâniens et commotions',
        'mapa' => 'Mesure ambulatoire de la pression artérielle',
        'imaging' => 'Imagerie médicale et compte rendu structuré',
        'mri' => 'Imagerie par résonance magnétique',
        'ecg_effort' => 'Épreuve d’effort et données ECG',
        'scintigraphy' => 'Scintigraphie',
        'fmarc' => 'Blessures et suivi F-MARC',
        'illness' => 'Pathologies et épisodes de maladie',
        'biological' => 'Profil biologique longitudinal',
        'laboratory' => 'Analyses biologiques et laboratoire',
    ];
    $ui = config('medical_sections.ui.'.$module, []);
    $controls = $ui['controls'] ?? [];
    $groups = $ui['groups'] ?? [];
    $groupedFields = collect($groups)->flatten()->all();
    $ungroupedFields = array_values(array_diff(array_keys($definition['fields']), $groupedFields));
@endphp

<div class="min-h-screen bg-slate-50">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-7">
        <header class="mb-6">
            <a href="{{ route('health-records.show', ['healthRecord'=>$healthRecord,'workspace'=>'exams']) }}" class="text-sm font-medium text-blue-600 hover:text-blue-800">← Tableau de bord santé</a>
            <div class="mt-4 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-[0.14em] text-blue-700 font-semibold">Module spécialisé</div>
                    <h1 class="mt-2 text-3xl font-bold text-slate-950">{{ $moduleLabel }}</h1>
                    <p class="mt-2 text-slate-600 max-w-2xl">{{ $moduleDescriptions[$module] ?? 'Données spécialisées rattachées au dossier longitudinal.' }}</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-2xl px-4 py-3 min-w-[250px] shadow-sm">
                    <div class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Joueur</div>
                    <div class="mt-1 font-semibold text-slate-950">{{ $player?->full_name ?? $player?->name ?? 'Joueur' }}</div>
                    <div class="text-sm text-slate-500 mt-0.5">{{ $player?->club?->name ?? 'Club non renseigné' }}</div>
                    <div class="text-xs text-slate-400 mt-1">Dossier #{{ $healthRecord->id }}</div>
                </div>
            </div>
        </header>

        @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <div class="font-semibold mb-2">Certaines informations doivent être corrigées.</div>
                <ul class="list-disc ml-5 space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('health-records.modules.store', ['healthRecord'=>$healthRecord,'module'=>$module]) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <input type="hidden" name="capture[{{ $module }}]" value="1">

            <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b bg-slate-50/70">
                    <h2 class="font-semibold text-slate-950">Contexte de l’examen</h2>
                    <p class="text-sm text-slate-500 mt-1">Date et provenance de cette nouvelle entrée.</p>
                </div>
                <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Date de l’examen / événement</label>
                        <input type="date" name="section_dates[{{ $module }}]" value="{{ old('section_dates.'.$module, now()->format('Y-m-d')) }}" required class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">Type de source</label>
                        <select name="section_source[{{ $module }}]" class="w-full rounded-xl border-slate-300 bg-white" @required(in_array($module,['biological','laboratory'],true))>
                            <option value="">Sélectionner…</option>
                            @foreach(['Médecin / service du club','Centre médical','Hôpital / clinique','Centre d’imagerie','Laboratoire','Spécialiste externe','Document transmis','Autre source'] as $source)
                                <option value="{{ $source }}" @selected(old('section_source.'.$module)===$source)>{{ $source }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            @foreach($groups as $groupLabel => $fields)
                <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 border-b bg-slate-50/70">
                        <h2 class="font-semibold text-slate-950">{{ $groupLabel }}</h2>
                    </div>
                    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($fields as $field)
                            @if(array_key_exists($field, $definition['fields']))
                                @include('health-records.partials.module-field', ['field'=>$field,'type'=>$definition['fields'][$field]])
                            @endif
                        @endforeach
                    </div>
                </section>
            @endforeach

            @if($ungroupedFields)
                <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 border-b bg-slate-50/70"><h2 class="font-semibold text-slate-950">Informations complémentaires</h2></div>
                    <div class="p-5 sm:p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                        @foreach($ungroupedFields as $field)
                            @include('health-records.partials.module-field', ['field'=>$field,'type'=>$definition['fields'][$field]])
                        @endforeach
                    </div>
                </section>
            @endif

            @if(in_array($module,['biological','laboratory'],true))
                <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-5 sm:px-6 py-4 border-b bg-slate-50/70">
                        <h2 class="font-semibold text-slate-950">Résultats de laboratoire</h2>
                        <p class="text-sm text-slate-500 mt-1">Valeurs reportées du compte rendu, sans interprétation automatique.</p>
                    </div>
                    <div class="p-5 sm:p-6 space-y-3">
                        @for($i=0;$i<3;$i++)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 border border-slate-200 rounded-xl p-4">
                                @foreach(['analyte','value','unit','reference','method','laboratory','report_id','sample_date'] as $field)
                                    <div>
                                        <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('medical_sections.'.($field==='laboratory'?'laboratory_name':$field)) }}</label>
                                        <input type="{{ $field==='sample_date'?'date':'text' }}" name="lab_rows[{{ $module }}][{{ $i }}][{{ $field }}]" value="{{ old('lab_rows.'.$module.'.'.$i.'.'.$field) }}" class="w-full rounded-lg border-slate-300 text-sm">
                                    </div>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                </section>
            @endif

            <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b bg-slate-50/70"><h2 class="font-semibold text-slate-950">Documents associés</h2></div>
                <div class="p-5 sm:p-6">
                    <input type="file" name="medical_files[{{ $module }}][]" multiple accept=".pdf,.jpg,.jpeg,.png,.dcm" class="block w-full text-sm text-slate-600">
                    <p class="text-xs text-slate-400 mt-2">PDF, image ou DICOM — maximum {{ config('medical_sections.max_file_kb',10240)/1024 }} Mo par fichier.</p>
                </div>
            </section>

            <div class="sticky bottom-0 z-10 bg-white/95 backdrop-blur border border-slate-200 rounded-2xl shadow-lg px-4 sm:px-5 py-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-slate-500">Cette entrée sera ajoutée à l’historique du module dans le dossier existant.</p>
                <div class="flex gap-2">
                    <a href="{{ route('health-records.show', ['healthRecord'=>$healthRecord,'workspace'=>'exams']) }}" class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-semibold">Annuler</a>
                    <button class="px-5 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Enregistrer dans le dossier</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

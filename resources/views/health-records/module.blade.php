@extends('layouts.app')

@section('title', 'Module spécialisé - Dossier santé')

@section('content')
@php
    $player = $healthRecord->player;
    $moduleLabel = __('medical_sections.'.$module);
    $moduleDescriptions = [
        'dental' => 'Suivi bucco-dentaire et odontologique',
        'scat' => 'Évaluation des traumatismes crâniens / commotions',
        'mapa' => 'Mesure ambulatoire de la pression artérielle',
        'imaging' => 'Imagerie médicale et comptes rendus',
        'mri' => 'Imagerie par résonance magnétique',
        'ecg_effort' => 'Épreuve d’effort et données ECG',
        'scintigraphy' => 'Scintigraphie',
        'fmarc' => 'Blessures et suivi F-MARC',
        'illness' => 'Pathologies et épisodes de maladie',
        'biological' => 'Profil biologique',
        'laboratory' => 'Analyses biologiques et laboratoire',
    ];
@endphp

<div class="min-h-screen bg-slate-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-7">
        <div class="mb-6">
            <a href="{{ route('health-records.show', ['healthRecord'=>$healthRecord,'workspace'=>'exams']) }}"
               class="text-sm text-blue-600 hover:text-blue-800">← Tableau de bord santé</a>
            <div class="mt-3 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                <div>
                    <div class="text-xs uppercase tracking-[0.14em] text-blue-700 font-semibold">Module spécialisé</div>
                    <h1 class="mt-2 text-3xl font-bold text-slate-950">{{ $moduleLabel }}</h1>
                    <p class="mt-2 text-slate-600">{{ $moduleDescriptions[$module] ?? 'Données spécialisées rattachées au dossier médical longitudinal.' }}</p>
                </div>
                <div class="bg-white border border-slate-200 rounded-xl px-4 py-3 min-w-[240px]">
                    <div class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Joueur</div>
                    <div class="mt-1 font-semibold text-slate-900">{{ $player?->full_name ?? $player?->name ?? 'Joueur' }}</div>
                    <div class="text-sm text-slate-500 mt-0.5">{{ $player?->club?->name ?? 'Club non renseigné' }}</div>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <div class="font-semibold mb-2">Certaines informations doivent être corrigées.</div>
                <ul class="list-disc ml-5 space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ route('health-records.modules.store', ['healthRecord'=>$healthRecord,'module'=>$module]) }}"
              enctype="multipart/form-data"
              class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            @csrf
            <input type="hidden" name="capture[{{ $module }}]" value="1">

            <div class="px-6 py-4 border-b bg-slate-50">
                <h2 class="font-semibold text-slate-900">Nouvelle entrée</h2>
                <p class="text-sm text-slate-500 mt-1">Cette saisie s’ajoute au dossier existant sans créer un nouveau dossier médical.</p>
            </div>

            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Date de l’examen / événement</label>
                        <input type="date" name="section_dates[{{ $module }}]"
                               value="{{ old('section_dates.'.$module, now()->format('Y-m-d')) }}"
                               required class="w-full rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Source / organisme</label>
                        <input name="section_source[{{ $module }}]"
                               value="{{ old('section_source.'.$module) }}"
                               class="w-full rounded-xl border-slate-300"
                               placeholder="Ex. médecin du club, centre d’imagerie, laboratoire…"
                               @required(in_array($module,['biological','laboratory'],true))>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($definition['fields'] as $field=>$type)
                        @php
                            $saved = old('section_values.'.$module.'.'.$field, $sectionValues[$field] ?? null);
                            if (is_array($saved)) {
                                $saved = implode("\n", array_map(fn($v)=>is_scalar($v)?(string)$v:json_encode($v,JSON_UNESCAPED_UNICODE), $saved));
                            }
                        @endphp
                        @if($type === 'json')
                            @continue
                        @endif

                        <div class="{{ in_array($type,['text','list'],true) ? 'md:col-span-2' : '' }}">
                            <label class="block text-sm font-medium text-slate-700 mb-1">
                                {{ app(AppServicesHealthRecordSections::class)->label($field) }}
                            </label>

                            @if($module === 'fmarc' && $field === 'injury_location')
                                @include('health-records.partials.injury-body-map', [
                                    'inputId' => 'module_injury_location',
                                    'inputName' => 'section_values[fmarc][injury_location]',
                                    'value' => $saved,
                                ])
                            @elseif($type === 'boolean')
                                <select name="section_values[{{ $module }}][{{ $field }}]" class="w-full rounded-xl border-slate-300">
                                    <option value="">Non renseigné</option>
                                    <option value="1" @selected((string)$saved==='1')>Oui</option>
                                    <option value="0" @selected((string)$saved==='0')>Non</option>
                                </select>
                            @elseif(in_array($type,['text','list'],true))
                                <textarea name="section_values[{{ $module }}][{{ $field }}]" rows="3"
                                          class="w-full rounded-xl border-slate-300"
                                          placeholder="Renseigner uniquement les éléments pertinents">{{ $saved }}</textarea>
                            @elseif($type === 'date')
                                <input type="date" name="section_values[{{ $module }}][{{ $field }}]" value="{{ $saved }}" class="w-full rounded-xl border-slate-300">
                            @elseif($type === 'time')
                                <input type="time" name="section_values[{{ $module }}][{{ $field }}]" value="{{ $saved }}" class="w-full rounded-xl border-slate-300">
                            @else
                                <input type="number" step="any" name="section_values[{{ $module }}][{{ $field }}]" value="{{ $saved }}" class="w-full rounded-xl border-slate-300">
                            @endif
                        </div>
                    @endforeach
                </div>

                @if(in_array($module,['biological','laboratory'],true))
                    <div class="border-t pt-5">
                        <h3 class="font-semibold text-slate-900">Résultats de laboratoire</h3>
                        <p class="text-sm text-slate-500 mt-1 mb-4">Ajouter les analytes et valeurs présents sur le compte rendu.</p>
                        <div class="space-y-3">
                            @for($i=0;$i<3;$i++)
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 border border-slate-200 rounded-xl p-4">
                                    @foreach(['analyte','value','unit','reference','method','laboratory','report_id','sample_date'] as $field)
                                        <div>
                                            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('medical_sections.'.($field==='laboratory'?'laboratory_name':$field)) }}</label>
                                            <input type="{{ $field==='sample_date'?'date':'text' }}"
                                                   name="lab_rows[{{ $module }}][{{ $i }}][{{ $field }}]"
                                                   value="{{ old('lab_rows.'.$module.'.'.$i.'.'.$field) }}"
                                                   class="w-full rounded-lg border-slate-300 text-sm">
                                        </div>
                                    @endforeach
                                </div>
                            @endfor
                        </div>
                    </div>
                @endif

                <div class="border-t pt-5">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Documents associés</label>
                    <input type="file" name="medical_files[{{ $module }}][]" multiple
                           accept=".pdf,.jpg,.jpeg,.png,.dcm"
                           class="block w-full text-sm text-slate-600">
                    <p class="text-xs text-slate-400 mt-2">PDF, image ou DICOM — maximum {{ config('medical_sections.max_file_kb',10240)/1024 }} Mo par fichier.</p>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <p class="text-xs text-slate-500">L’entrée sera historisée dans ce module du dossier santé.</p>
                <div class="flex gap-2">
                    <a href="{{ route('health-records.show', ['healthRecord'=>$healthRecord,'workspace'=>'exams']) }}"
                       class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-semibold">Annuler</a>
                    <button class="px-5 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">
                        Enregistrer dans le dossier
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

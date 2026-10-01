@extends('layouts.app')

@section('title', 'Nouvelle visite médicale - Med Predictor')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="mb-6 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <a href="{{ route('health-records.index') }}" class="text-sm text-blue-600 hover:text-blue-800">← Dossiers médicaux</a>
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
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif de consultation</label>
                    <textarea name="chief_complaint" rows="3" autofocus
                              placeholder="Ex. Douleur du genou droit depuis le match de dimanche..."
                              class="w-full border-gray-300 rounded-lg">{{ old('chief_complaint') }}</textarea>
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Histoire / symptômes</label>
                    <textarea name="visit_notes" rows="4" placeholder="Chronologie, contexte, évolution, symptômes associés..." class="w-full border-gray-300 rounded-lg">{{ old('visit_notes') }}</textarea>
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Examen clinique</label>
                    <textarea name="physical_examination" rows="5" placeholder="Inspection, palpation, mobilité, tests spécifiques, examen orienté..." class="w-full border-gray-300 rounded-lg">{{ old('physical_examination') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-semibold text-gray-900">3. Évaluation et plan</h2>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Impression clinique / diagnostic</label>
                    <textarea name="diagnosis" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('diagnosis') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conduite à tenir / traitement</label>
                    <textarea name="treatment_plan" rows="4" class="w-full border-gray-300 rounded-lg">{{ old('treatment_plan') }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prescriptions</label>
                        <textarea name="prescriptions" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('prescriptions') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instructions de suivi</label>
                        <textarea name="follow_up_instructions" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('follow_up_instructions') }}</textarea>
                    </div>
                </div>
                <div class="max-w-xs">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prochain contrôle</label>
                    <input type="date" name="next_checkup_date" class="w-full border-gray-300 rounded-lg">
                </div>
            </div>
        </div>

        <div class="sticky bottom-4 bg-white/95 backdrop-blur border border-gray-200 rounded-xl shadow-lg p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="text-sm text-gray-600">Les examens spécialisés pourront être ajoutés depuis la fiche après enregistrement.</div>
            <div class="flex gap-3">
                <a href="{{ url()->previous() }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">Annuler</a>
                <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Terminer la visite</button>
            </div>
        </div>
    </form>
</div>
@endsection

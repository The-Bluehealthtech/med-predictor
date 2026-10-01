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
        <input type="hidden" name="record_date" value="{{ old('record_date', $healthRecord->record_date?->format('Y-m-d')) }}">

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
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                    <textarea name="chief_complaint" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('chief_complaint', $healthRecord->chief_complaint) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-semibold text-gray-900">Histoire et examen</h2>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Histoire / notes de visite</label>
                    <textarea name="visit_notes" rows="4" class="w-full border-gray-300 rounded-lg">{{ old('visit_notes', $healthRecord->visit_notes) }}</textarea>
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

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Examen clinique</label>
                    <textarea name="physical_examination" rows="5" class="w-full border-gray-300 rounded-lg">{{ old('physical_examination', $healthRecord->physical_examination) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h2 class="font-semibold text-gray-900">Évaluation et plan</h2>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Impression clinique / diagnostic</label>
                    <textarea name="diagnosis" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('diagnosis', $healthRecord->diagnosis) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Conduite à tenir / traitement</label>
                    <textarea name="treatment_plan" rows="4" class="w-full border-gray-300 rounded-lg">{{ old('treatment_plan', $healthRecord->treatment_plan) }}</textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prescriptions</label>
                        <textarea name="prescriptions" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('prescriptions', $healthRecord->prescriptions) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Instructions de suivi</label>
                        <textarea name="follow_up_instructions" rows="3" class="w-full border-gray-300 rounded-lg">{{ old('follow_up_instructions', $healthRecord->follow_up_instructions) }}</textarea>
                    </div>
                </div>
                <div class="max-w-xs">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Prochain contrôle</label>
                    <input type="date" name="next_checkup_date" value="{{ old('next_checkup_date', $healthRecord->next_checkup_date?->format('Y-m-d')) }}" class="w-full border-gray-300 rounded-lg">
                </div>
            </div>
        </div>

        <div class="sticky bottom-4 bg-white/95 backdrop-blur border border-gray-200 rounded-xl shadow-lg p-4 flex justify-end gap-3">
            <a href="{{ route('health-records.show', $healthRecord) }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">Annuler</a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700">Enregistrer</button>
        </div>
    </form>
</div>
@endsection

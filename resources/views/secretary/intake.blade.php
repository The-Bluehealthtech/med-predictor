@extends('layouts.secretary')

@section('title', 'Pré-accueil médical')

@section('content')
@php
    $preIntake = data_get($appointment->visit?->administrative_data, 'pre_intake', []);
    $savedSecretaryNotes = $appointment->visit?->notes;
@endphp
<div class="max-w-5xl mx-auto">
    <div class="mb-6 flex items-start justify-between gap-4">
        <div>
            <a href="{{ route('secretary.dashboard') }}" class="text-sm text-blue-600">← Retour au secrétariat</a>
            <h1 class="text-3xl font-bold text-slate-900 mt-2">Pré-accueil médical</h1>
            <p class="text-slate-600 mt-1">Vérifier le patient, son dossier et les informations utiles avant la consultation.</p>
        </div>
        <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $dossier ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-800' }}">
            {{ $dossier ? 'Dossier existant' : 'Dossier à initialiser' }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
        <form method="POST" action="{{ route('secretary.appointments.check-in', $appointment) }}"
              class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            @csrf
            <div class="p-6 border-b">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center font-semibold text-slate-600">
                        {{ mb_substr($player->full_name ?? $player->name ?? 'P',0,1) }}
                    </div>
                    <div>
                        <div class="font-semibold text-lg text-slate-900">{{ $player->full_name ?? $player->name }}</div>
                        <div class="text-sm text-slate-500">
                            {{ $player->club?->name ?? 'Club non renseigné' }}
                            @if($player->position) · {{ $player->position }} @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Rendez-vous</div>
                        <div class="mt-1 text-sm font-medium text-slate-800">{{ $appointment->appointment_date?->format('d/m/Y H:i') }}</div>
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-wide text-slate-400 font-semibold">Type</div>
                        <div class="mt-1 text-sm font-medium text-slate-800">{{ $appointment->type_label }}</div>
                    </div>
                </div>

                @if($appointment->appointment_type === 'pcma')
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900">
                        Visite PCMA : le médecin réalise l’évaluation médicale pré-compétition et la signe pendant la visite.
                        Le PCMA signé remplit la condition médicale des demandes de licence du joueur ; la visite se clôt à la signature.
                    </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Motif confirmé par le joueur</label>
                    <input name="reason_confirmed" value="{{ old('reason_confirmed', data_get($preIntake, 'reason_confirmed', $appointment->reason)) }}"
                           class="w-full rounded-xl border-slate-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Symptômes / demande exprimée</label>
                    <textarea name="symptoms_summary" rows="3" class="w-full rounded-xl border-slate-300"
                              placeholder="Résumé administratif des éléments rapportés par le joueur, sans interprétation médicale.">{{ old('symptoms_summary', data_get($preIntake, 'symptoms_summary')) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Allergies déclarées</label>
                        <textarea name="patient_reported_allergies" rows="2" class="w-full rounded-xl border-slate-300">{{ old('patient_reported_allergies', data_get($preIntake, 'patient_reported_allergies')) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Traitements déclarés</label>
                        <textarea name="patient_reported_medications" rows="2" class="w-full rounded-xl border-slate-300">{{ old('patient_reported_medications', data_get($preIntake, 'patient_reported_medications')) }}</textarea>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Note administrative</label>
                    <textarea name="secretary_notes" rows="2" class="w-full rounded-xl border-slate-300"
                              placeholder="Pièces manquantes, accompagnant, information logistique…">{{ old('secretary_notes', $savedSecretaryNotes) }}</textarea>
                </div>
            </div>

            <div class="px-6 py-4 bg-slate-50 border-t flex justify-end">
                <button class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold hover:bg-blue-700">
                    Terminer le pré-accueil et placer en attente
                </button>
            </div>
        </form>

        <aside class="space-y-4">
            <div class="bg-white border border-slate-200 rounded-2xl p-5">
                <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Dossier santé</div>
                @if($dossier)
                    <div class="mt-2 font-semibold text-emerald-700">Dossier longitudinal retrouvé</div>
                    <p class="mt-2 text-sm text-slate-600">Le médecin retrouvera le tableau de bord santé existant.</p>
                    <a href="{{ route('health-records.show', $dossier) }}" class="inline-flex mt-4 text-sm font-semibold text-blue-600">Ouvrir le dossier →</a>
                @else
                    <div class="mt-2 font-semibold text-amber-800">Premier passage médical</div>
                    <p class="mt-2 text-sm text-slate-600">La consultation médicale initialisera le dossier de base du joueur.</p>
                @endif
            </div>

            <div class="bg-white border border-slate-200 rounded-2xl p-5">
                <div class="text-xs uppercase tracking-wide font-semibold text-slate-400">Identité clinique</div>
                <p class="mt-2 text-sm text-slate-600">Rapprochement avec les dossiers des établissements (serveur FHIR de FIT).</p>
                <a href="{{ route('secretary.identity', ['player' => $player, 'back' => url()->current()]) }}" class="inline-flex mt-3 text-sm font-semibold text-blue-600">Vérifier l’identité clinique →</a>
            </div>

            @if($appointment->visit)
            <div class="bg-white border border-slate-200 rounded-2xl p-5">
                <div class="text-sm font-semibold text-slate-900">Documents pour le médecin</div>
                <p class="text-xs text-slate-500 mt-1">Pièces reçues à l’accueil et rattachées à cet épisode.</p>
                <form method="POST" action="{{ route('secretary.appointments.documents.store', $appointment) }}" enctype="multipart/form-data" class="mt-4 space-y-3">
                    @csrf
                    <select name="document_type" required class="w-full rounded-lg border-slate-300 text-sm">
                        <option value="medical_report">Rapport médical</option>
                        <option value="lab_result">Résultat laboratoire</option>
                        <option value="radiology">Imagerie</option>
                        <option value="prescription">Ordonnance</option>
                        <option value="referral">Courrier / demande</option>
                        <option value="other">Autre</option>
                    </select>
                    <input type="file" name="document_file" required class="block w-full text-sm">
                    <textarea name="description" rows="2" class="w-full rounded-lg border-slate-300 text-sm" placeholder="Description facultative"></textarea>
                    <button class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm font-semibold hover:bg-slate-50">Ajouter le document</button>
                </form>
            </div>
            @endif
        </aside>
    </div>
</div>
@endsection

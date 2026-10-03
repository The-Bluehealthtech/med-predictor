@extends('layouts.app')

@section('title','Préparation Match Day - FIT')

@section('content')
@php
$statusClass = fn($ready) => $ready
    ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
    : 'border-amber-200 bg-amber-50 text-amber-800';
$medicalReady = in_array($medicalStatus, ['ready','validated'], true);
$sheetReady = in_array($sheetStatus, ['submitted','validated'], true);
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
<x-page-header
    title="Préparation Match Day"
    subtitle="{{ $match->homeTeam?->name ?? 'Équipe domicile' }} vs {{ $match->awayTeam?->name ?? 'Équipe extérieure' }}"
    eyebrow="Gestion des compétitions · Match #{{ $match->id }}"
    :back-href="route('competition-management.matches.index')"
    back-label="Retour aux matchs"
/>

<section class="rounded-2xl border border-slate-200 bg-white p-5">
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
<div>
<p class="text-sm text-slate-600">Cockpit unique de préparation et de clôture du match.</p>
<p class="mt-1 text-sm font-medium text-slate-900">{{ $readyCount }}/{{ $totalCoreChecks }} contrôles principaux prêts</p>
</div>
<div class="w-full lg:w-72">
<div class="h-2 rounded-full bg-slate-100 overflow-hidden">
<div class="h-2 bg-emerald-600" style="width: {{ ($readyCount / max($totalCoreChecks,1)) * 100 }}%"></div>
</div>
</div>
</div>
</section>

<div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
<a href="{{ route('competition-management.matches.match-sheet',$match) }}" class="rounded-2xl border border-slate-200 bg-white p-5 hover:shadow-md transition">
<div class="flex items-start justify-between gap-3">
<div><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Workflow arbitres & équipes</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Feuille de match</h2></div>
<span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass($sheetReady) }}">{{ ucfirst($sheetStatus) }}</span>
</div>
<p class="mt-3 text-sm text-slate-600">Compositions, signatures, événements, rapport et validation réglementaire.</p>
<p class="mt-4 text-sm font-semibold text-blue-700">Ouvrir la feuille de match →</p>
</a>

<a href="{{ route('competition-management.matches.match-sheet',$match) }}#officials" class="rounded-2xl border border-slate-200 bg-white p-5 hover:shadow-md transition">
<div class="flex items-start justify-between gap-3">
<div><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Affectations</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Officiels & arbitres</h2></div>
<span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass($officialsReady) }}">{{ $officialsReady ? 'Complet' : 'À compléter' }}</span>
</div>
<p class="mt-3 text-sm text-slate-600">Arbitre principal, assistants, quatrième officiel et autres officiels du match.</p>
<p class="mt-4 text-sm font-semibold text-blue-700">Gérer les officiels →</p>
</a>

<div class="rounded-2xl border border-slate-200 bg-white p-5">
<div class="flex items-start justify-between gap-3">
<div><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Infrastructure</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Stade & logistique</h2></div>
<span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass($venueReady) }}">{{ $venueReady ? 'Prêt' : 'À vérifier' }}</span>
</div>
<p class="mt-3 text-sm text-slate-600">{{ $match->stadium ?: ($match->venue ?: 'Stade ou lieu à renseigner') }}</p>
<p class="mt-4 text-xs text-slate-500">Les contrôles logistiques avancés seront regroupés ici.</p>
</div>
@if($canOpenMedical)
<a href="{{ route('matches.medical-emergency-plan',$match) }}" class="rounded-2xl border border-rose-200 bg-white p-5 hover:shadow-md transition">
<div class="flex items-start justify-between gap-3">
<div><p class="text-xs font-bold uppercase tracking-wide text-rose-600">Équipe médicale terrain</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Medical Matchday</h2></div>
<span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass($medicalReady) }}">{{ ucfirst($medicalStatus) }}</span>
</div>
<p class="mt-3 text-sm text-slate-600">Plan d’urgence, équipe et matériel, incidents terrain et protocoles FIFA SCA / crânio-cervical.</p>
<p class="mt-4 text-sm font-semibold text-rose-700">Ouvrir Medical Matchday →</p>
</a>
@else
<div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
<div class="flex items-start justify-between gap-3">
<div><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Accès restreint</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Medical Matchday</h2></div>
<span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">Médical</span>
</div>
<p class="mt-3 text-sm text-slate-600">Données médicales accessibles uniquement aux rôles autorisés.</p>
</div>
@endif

<div class="rounded-2xl border border-slate-200 bg-white p-5">
<div class="flex items-start justify-between gap-3">
<div><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Conformité</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Contrôles pré-match</h2></div>
<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">À structurer</span>
</div>
<p class="mt-3 text-sm text-slate-600">Vue consolidée des contrôles réglementaires avant passage en Ready for Match.</p>
</div>

<div class="rounded-2xl border border-slate-200 bg-white p-5">
<div class="flex items-start justify-between gap-3">
<div><p class="text-xs font-bold uppercase tracking-wide text-slate-500">Après-match</p><h2 class="mt-1 text-lg font-semibold text-slate-900">Clôture & bundle documentaire</h2></div>
<span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">À construire</span>
</div>
<p class="mt-3 text-sm text-slate-600">Documents obligatoires, signatures métiers et validation finale fédération/ligue.</p>
</div>
</div>
</div>
@endsection

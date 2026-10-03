@extends('layouts.app')

@section('title','Choisir une feuille de match - FIT')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
<x-page-header
    title="Préparation Match Day"
    subtitle="Choisissez la feuille de match à préparer, suivre ou clôturer."
    eyebrow="Gestion des compétitions"
    :back-href="route('modules.index')"
    back-label="Retour aux modules"
/>

<section class="rounded-2xl border border-slate-200 bg-white p-5">
<form method="GET" action="{{ route('competition-management.matches.index') }}" class="grid gap-4 md:grid-cols-[1fr_220px_auto]">
<label class="text-sm font-medium text-slate-700">
Rechercher une FDM
<input type="search" name="q" value="{{ request('q') }}" placeholder="Compétition, équipe domicile ou extérieure…" class="mt-1 w-full rounded-xl border-slate-300">
</label>
<label class="text-sm font-medium text-slate-700">
Statut
<select name="status" class="mt-1 w-full rounded-xl border-slate-300">
<option value="">Tous les statuts</option>
@foreach(['draft'=>'Brouillon','submitted'=>'Soumise','validated'=>'Validée','rejected'=>'Rejetée'] as $value=>$label)
<option value="{{ $value }}" @selected(request('status')===$value)>{{ $label }}</option>
@endforeach
</select>
</label>
<div class="flex items-end gap-2">
<button class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white">Filtrer</button>
@if(request()->hasAny(['q','status']))
<a href="{{ route('competition-management.matches.index') }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700">Réinitialiser</a>
@endif
</div>
</form>
</section>

<section class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
<div class="border-b border-slate-200 px-5 py-4">
<h2 class="font-semibold text-slate-900">Feuilles de match disponibles</h2>
<p class="mt-1 text-sm text-slate-500">{{ $sheets->total() }} FDM dans votre périmètre</p>
</div>
@if($sheets->count())
<div class="divide-y divide-slate-100">
@foreach($sheets as $sheet)
@php
$match=$sheet->match;
$statusLabel=match($sheet->status){'draft'=>'Brouillon','submitted'=>'Soumise','validated'=>'Validée','rejected'=>'Rejetée',default=>ucfirst((string)$sheet->status)};
$statusClass=match($sheet->status){'validated'=>'bg-emerald-100 text-emerald-800','submitted'=>'bg-blue-100 text-blue-800','rejected'=>'bg-rose-100 text-rose-800',default=>'bg-slate-100 text-slate-700'};
@endphp
<div class="p-5 hover:bg-slate-50">
<div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
<div class="min-w-0">
<div class="flex flex-wrap items-center gap-2">
<span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
<span class="text-xs font-medium text-slate-500">FDM #{{ $sheet->id }}</span>
@if($sheet->match_number)<span class="text-xs text-slate-400">N° {{ $sheet->match_number }}</span>@endif
</div>
<h3 class="mt-2 text-lg font-semibold text-slate-900">
{{ $match->homeTeam?->name ?? 'Équipe domicile' }} <span class="text-slate-400">vs</span> {{ $match->awayTeam?->name ?? 'Équipe extérieure' }}
</h3>
<div class="mt-1 flex flex-wrap gap-x-4 gap-y-1 text-sm text-slate-600">
<span>{{ $match->competition?->name ?? 'Compétition non renseignée' }}</span>
@if($match->match_date)<span>{{ $match->match_date->format('d/m/Y') }}</span>@endif
@if($match->kickoff_time)<span>{{ $match->kickoff_time->format('H:i') }}</span>@endif
@if($match->stadium || $match->venue)<span>{{ $match->stadium ?: $match->venue }}</span>@endif
</div>
</div>
<div class="flex shrink-0 flex-wrap gap-2">
<a href="{{ route('competition-management.matches.match-sheet',$match) }}" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-white">Voir la FDM</a>
<a href="{{ route('competition-management.matches.matchday-preparation',$match) }}" class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Ouvrir le cockpit Match Day</a>
</div>
</div>
</div>
@endforeach
</div>
<div class="border-t border-slate-200 px-5 py-4">{{ $sheets->links() }}</div>
@else
<div class="p-10 text-center">
<p class="font-semibold text-slate-900">Aucune feuille de match trouvée</p>
<p class="mt-1 text-sm text-slate-500">Modifiez les filtres ou créez d’abord une feuille de match pour accéder au cockpit Match Day.</p>
</div>
@endif
</section>
</div>
@endsection

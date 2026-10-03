@extends('layouts.app')

@section('title', 'Données des établissements — ' . ($player->full_name ?? $player->name))

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-4">
    <div>
        <a href="{{ $back && str_starts_with($back, url('/')) ? $back : route('health-records.index') }}" class="text-sm text-blue-600">← Retour au dossier</a>
        <h1 class="text-2xl font-bold text-gray-900 mt-2">Données des établissements — {{ $player->full_name ?? $player->name }}</h1>
        <p class="text-sm text-gray-600 mt-1">Résultats et comptes rendus transmis par les EMR, laboratoires, services d’imagerie et PACS au serveur FHIR de FIT (IHE QEDm). Consultation seule : rien n’est intégré au dossier FIT sans décision médicale.</p>
    </div>

    @if(!$configured)
        <p class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">Serveur FHIR de FIT non installé (prévu avant la mise en production).</p>
    @elseif($linked === [])
        <p class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">L’identité clinique du joueur n’est pas encore transmise au serveur : elle l’est au pré-accueil du secrétariat médical, qui rattache aussi les dossiers des établissements.</p>
    @else
        <nav class="flex flex-wrap gap-2" aria-label="Catégories">
            @foreach($categories as $key => [$label])
                <a href="{{ route('clinical.external-data', ['player' => $player, 'tab' => $key, 'back' => $back]) }}"
                   @if($tab === $key) aria-current="page" @endif
                   class="px-3 py-1.5 rounded-full text-sm font-semibold {{ $tab === $key ? 'bg-slate-900 text-white' : 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50' }}">{{ $label }}</a>
            @endforeach
        </nav>

        @if(session('success'))<p class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">{{ session('success') }}</p>@endif
        @if($errors->has('fhir'))<p class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $errors->first('fhir') }}</p>@endif
        @if($error)
            <p class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">{{ $error }}</p>
        @else
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <caption class="sr-only">{{ $categories[$tab][0] }}</caption>
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-500">
                        <tr><th class="px-4 py-2">Date</th><th class="px-4 py-2">Élément</th><th class="px-4 py-2">Valeur</th><th class="px-4 py-2">Détails</th><th class="px-4 py-2">Source</th></tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($items as $item)
                            <tr class="align-top">
                                <td class="px-4 py-2 whitespace-nowrap text-slate-600">{{ \App\Services\Fhir\ClinicalDataQuery::displayDate($item['date']) }}</td>
                                <td class="px-4 py-2">
                                    <div class="font-medium text-slate-900">{{ $item['label'] }}</div>
                                    @if($item['codes'])<div class="text-xs text-slate-500">{{ implode(', ', $item['codes']) }}</div>@endif
                                    @if($item['status'])<div class="text-xs text-slate-500">Statut : {{ $item['status'] }}</div>@endif
                                </td>
                                <td class="px-4 py-2 text-slate-800">{{ $item['value'] ?? '—' }}</td>
                                <td class="px-4 py-2 text-slate-700">
                                    {{ $item['detail'] ?? '' }}
                                    @if($item['viewer'])<div><a href="{{ $item['viewer'] }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 font-semibold">Ouvrir les images (visionneuse PACS)</a></div>@endif
                                    @if($tab === 'reports')
                                        @if(in_array($item['id'], $integrated, true))
                                            <div class="mt-1 text-xs font-semibold text-emerald-700">Intégré au dossier FIT</div>
                                        @else
                                            <form method="POST" action="{{ route('clinical.external-data.integrate', $player) }}" class="mt-1">@csrf
                                                <input type="hidden" name="report_id" value="{{ $item['id'] }}">
                                                <button class="text-sm font-semibold text-blue-700 hover:underline">Intégrer au dossier</button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-slate-600">{{ $item['source'] ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-8 text-center text-slate-500">Aucune donnée dans cette catégorie.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    @endif
</div>
@endsection

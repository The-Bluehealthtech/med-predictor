@extends('layouts.app')

@php
    $isMedical = $kind === 'medical';
    $title = $isMedical ? 'Passeports médicaux' : 'Passeports de transfert';
    $showRoute = $isMedical ? 'passports.medical.show' : 'passports.transfer.show';
@endphp

@section('title', $title)

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    <x-page-header
        :title="$title"
        :subtitle="$isMedical
            ? 'Résumé médical International Patient Summary (HL7/IHE) à partager lors d’un transfert, d’une sélection nationale ou à la demande du joueur.'
            : 'Passeport joueur : clubs d’enregistrement, statut, transferts et ITC. Aucune donnée médicale.'"
        eyebrow="Passeports"
        :back-href="route('modules.index')"
        back-label="Retour aux modules"
        :count="$players->total()"
        count-label="joueur(s)"
    >
        <x-slot:actions>
            <a href="{{ route($isMedical ? 'passports.transfer.index' : 'passports.medical.index') }}"
               class="inline-flex items-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                {{ $isMedical ? 'Passeports de transfert' : 'Passeports médicaux' }}
            </a>
        </x-slot:actions>
    </x-page-header>

    <form method="GET" class="bg-white rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
        <label class="block text-sm font-medium text-gray-700">Joueur
            <input type="search" name="q" value="{{ $term }}" class="mt-1 block w-72 rounded-lg border-gray-300 shadow-sm text-sm" placeholder="Nom ou prénom">
        </label>
        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700">Rechercher</button>
    </form>

    <section class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500"><tr><th class="px-5 py-2 text-left">Joueur</th><th class="px-5 py-2 text-left">Club</th><th class="px-5 py-2 text-left">Naissance</th><th class="px-5 py-2"></th></tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($players as $player)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-medium text-gray-900">{{ $player->last_name }} {{ $player->first_name }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ str_replace(' (Démo)', '', $player->club->name ?? '—') }}</td>
                        <td class="px-5 py-3 text-gray-700">{{ $player->date_of_birth ? \Illuminate\Support\Carbon::parse($player->date_of_birth)->format('d/m/Y') : '—' }}</td>
                        <td class="px-5 py-3 text-right whitespace-nowrap">
                            <a href="{{ route($showRoute, $player->id) }}" class="font-medium {{ $isMedical ? 'text-red-700 hover:text-red-900' : 'text-slate-700 hover:text-slate-900' }}">Ouvrir</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-6 text-gray-500">Aucun joueur dans votre périmètre.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-5 py-3 border-t">{{ $players->links() }}</div>
    </section>
</div>
@endsection

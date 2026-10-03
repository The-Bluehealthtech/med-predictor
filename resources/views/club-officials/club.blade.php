@extends('layouts.app')

@section('title', 'Dirigeants et staff — ' . $club->name)

@php $d = fn ($v) => $v ? \Illuminate\Support\Carbon::parse($v)->format('d/m/Y') : '—'; @endphp

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-600">Dirigeants et staff · format FIFA Connect</p>
            <h1 class="text-2xl font-bold text-gray-900">{{ str_replace(' (Démo)', '', $club->name) }}</h1>
            <p class="text-sm text-gray-600">OrganisationFIFAId : <span class="font-mono">{{ $club->fifa_connect_id ?: 'non renseigné' }}</span></p>
        </div>
        <div class="flex flex-wrap items-center gap-3 text-sm">
            @if($canManage)
                <form method="POST" action="{{ route('club-officials.sync-connect',$club) }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-lg bg-blue-700 text-white font-semibold hover:bg-blue-800">Synchroniser FIFA Connect</button>
                </form>
                <a href="{{ route('club-officials.create', [$club, 'type' => 'TeamOfficial']) }}" class="px-4 py-2 rounded-lg bg-slate-700 text-white font-semibold hover:bg-slate-800">+ Membre du staff</a>
                <a href="{{ route('club-officials.create', [$club, 'type' => 'OrganisationOfficial']) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">+ Dirigeant</a>
            @endif
            <a href="{{ route('modules.clubs.show', $club) }}" class="text-blue-600 hover:text-blue-800">Fiche club</a>
        </div>
    </div>

    @if(session('status'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>@endif
    @unless($xsdInstalled)
        <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">Le bundle XSD FIFA Connect n'est pas installé : seuls les codes « Coach » et « President » sont confirmés ; les autres codes de rôle restent à valider avant un échange avec FIFA Connect.</div>
    @endunless

    @foreach([['Staff technique', 'TeamOfficial', $staff], ['Dirigeants', 'OrganisationOfficial', $board]] as [$title, $type, $items])
        <section class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-5 py-4 border-b"><h2 class="font-semibold text-gray-900">{{ $title }} <span class="text-xs font-normal text-gray-400 font-mono">{{ $type }}</span></h2></div>
            @if($items->isEmpty())
                <p class="px-5 py-6 text-sm text-gray-500">Aucune fiche.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-xs uppercase tracking-wider text-gray-500"><tr><th class="px-5 py-2 text-left">Nom</th><th class="px-5 py-2 text-left">Rôle</th><th class="px-5 py-2 text-left">PersonFIFAId</th><th class="px-5 py-2 text-left">Nationalité</th><th class="px-5 py-2 text-left">Période</th><th class="px-5 py-2 text-left">Statut</th><th class="px-5 py-2"></th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $o)
                            <tr class="hover:bg-gray-50 {{ $o->isActive() ? '' : 'text-gray-400' }}">
                                <td class="px-5 py-3 font-medium {{ $o->isActive() ? 'text-gray-900' : '' }}">{{ $o->fullName() }}</td>
                                <td class="px-5 py-3">{{ $o->roleLabel() }} <span class="font-mono text-xs text-gray-400">{{ $o->roleCode() }}</span>@if($o->is_head_coach) <span class="ml-1 rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-700">principal</span>@endif @if($o->source==='FIFAConnect')<span class="ml-1 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-semibold text-blue-700">Connect</span>@endif</td>
                                <td class="px-5 py-3 font-mono text-xs">{{ $o->person_fifa_id ?: '—' }}</td>
                                <td class="px-5 py-3">{{ $o->nationality }}</td>
                                <td class="px-5 py-3 whitespace-nowrap">{{ $d($o->registration_valid_from) }} → {{ $o->registration_valid_to ? $d($o->registration_valid_to) : '…' }}</td>
                                <td class="px-5 py-3">{{ $o->isActive() ? 'Actif' : 'Inactif' }}</td>
                                <td class="px-5 py-3 text-right whitespace-nowrap"><a href="{{ route('club-officials.show', [$club, $o]) }}" class="font-medium text-slate-700 hover:text-slate-900">Fiche</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </section>
    @endforeach
</div>
@endsection

@extends('layouts.app')

@section('title', 'IPS — ' . ($player->full_name ?? $player->name))

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8 space-y-4">
    <a href="{{ route('passports.medical.show', ['player' => $player->id, 'purpose' => $purpose, 'ips' => 1]) }}" class="text-sm text-blue-600">← Passeport médical</a>
    <div class="bg-white rounded-lg shadow p-6">
        <p class="text-xs font-semibold uppercase tracking-wide text-red-700">International Patient Summary · HL7 IPS / IHE sIPS</p>
        <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $ips['title'] }}</h1>
        <p class="text-sm text-gray-600 mt-1">
            {{ $player->full_name ?? $player->name }} · document du {{ $ips['date'] ? \Illuminate\Support\Carbon::parse($ips['date'])->format('d/m/Y H:i') : '—' }}
            @if($ips['reference']['author']) · auteur : {{ $ips['reference']['author'] }}@endif
            @if($ips['status']) · statut : {{ $ips['status'] === 'final' ? 'final' : $ips['status'] }}@endif
        </p>
        <p class="mt-3 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-900">Document consulté sur le serveur FHIR de FIT (IHE MHD ITI-68). Son contenu relève de l'établissement auteur ; il n'est pas intégré au dossier FIT sans validation médicale.</p>
        @forelse($ips['sections'] as $section)
            <section class="mt-5">
                <h2 class="font-semibold text-gray-900 border-b pb-1">{{ $section['title'] }}</h2>
                @if($section['items'])
                    <ul class="mt-2 text-sm text-gray-800 list-disc pl-5 space-y-1">@foreach($section['items'] as $item)<li>{{ $item }}</li>@endforeach</ul>
                @elseif($section['text'])
                    <p class="mt-2 text-sm text-gray-700">{{ $section['text'] }}</p>
                @else
                    <p class="mt-2 text-sm italic text-gray-400">Section sans entrée.</p>
                @endif
            </section>
        @empty
            <p class="mt-4 text-sm text-gray-500">Document sans section.</p>
        @endforelse
    </div>
</div>
@endsection

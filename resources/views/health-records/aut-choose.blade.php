@extends('layouts.app')
@section('title',__('medical_aut.title'))
@section('content')
<div class="container mx-auto px-4 py-8">
<h1 class="text-3xl font-bold">{{ __('medical_aut.title') }}</h1>
<p class="my-4">{{ app()->getLocale()==='fr'?'Choisissez le dossier médical du joueur pour préparer une demande AUT.':'Choose the player’s medical record to prepare a TUE application.' }}</p>
<p class="border rounded p-4">{{ __('medical_aut.draft_note') }}</p>
@forelse($records as $record)
<div class="bg-white rounded-lg shadow-md p-4 my-4">
<strong>{{ $record->player?->full_name }} · {{ $record->player?->club?->name ?? '—' }}</strong>
<p>{{ $record->record_date?->format('d/m/Y') ?? '—' }}</p>
<a class="inline-block bg-blue-600 text-white rounded px-4 py-2 mt-2" href="{{ route('medical-aut.create',$record->id) }}">{{ __('medical_aut.title') }} — {{ app()->getLocale()==='fr'?'Ouvrir le formulaire':'Open form' }}</a>
<a class="ml-4" href="{{ route('medical-aut.index',$record->id) }}">{{ app()->getLocale()==='fr'?'Demandes existantes':'Existing applications' }}</a>
</div>
@empty
<p class="my-4">{{ __('medical_module.empty') }}</p>
<a href="{{ route('health-records.create') }}">{{ __('medical_module.create') }}</a>
@endforelse
{{ $records->links() }}
</div>
@endsection

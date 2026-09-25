@extends('layouts.app')

@section('title', 'Recommandations de performance')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-900">Recommandations de performance</h1>
    <p class="mt-4 text-gray-600">
        L'affichage des recommandations enregistrées n'est pas encore disponible.
    </p>
    <a class="mt-6 inline-block text-blue-600" href="{{ route('performances.analytics') }}">
        Voir les données de performance enregistrées
    </a>
</div>
@endsection

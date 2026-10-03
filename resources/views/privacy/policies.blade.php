@extends('layouts.app')

@section('title', 'Politique de confidentialité')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-8 space-y-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Politique de confidentialité — {{ $association?->name }}</h1>
        <p class="text-sm text-gray-600 mt-1">Texte présenté au joueur (ou à son représentant légal) lorsqu’il consent au partage de ses données de santé hors du club (IHE PCF). Chaque publication crée une version nouvelle et immuable ; les consentements déjà signés restent liés à leur version.</p>
    </div>
    @if(session('success'))<p class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900" role="status">{{ session('success') }}</p>@endif
    @if($errors->any())<div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

    @if($associations->isNotEmpty())
        <form method="GET" class="text-sm"><label>Fédération
            <select name="association_id" onchange="this.form.submit()" class="ml-2 rounded border-gray-300">
                @foreach($associations as $a)<option value="{{ $a->id }}" @selected($a->id === $associationId)>{{ $a->name }}</option>@endforeach
            </select></label></form>
    @endif

    <form method="POST" action="{{ route('privacy-policies.store', $associations->isNotEmpty() ? ['association_id' => $associationId] : []) }}" class="bg-white rounded-lg shadow p-5 space-y-3">
        @csrf
        <h2 class="font-semibold text-gray-900">{{ $current ? 'Publier une nouvelle version (actuelle : v' . $current->version . ')' : 'Publier la première version' }}</h2>
        <label class="block text-sm font-medium text-gray-700">Titre
            <input name="title" required maxlength="255" value="{{ old('title', $current?->title ?? 'Politique de confidentialité des données de santé') }}" class="mt-1 w-full rounded-lg border-gray-300"></label>
        <label class="block text-sm font-medium text-gray-700">Texte
            <textarea name="body" required rows="14" class="mt-1 w-full rounded-lg border-gray-300 font-mono text-sm">{{ old('body', $current?->body) }}</textarea></label>
        <button class="px-4 py-2 rounded-lg bg-slate-900 text-white text-sm font-semibold">Publier</button>
    </form>

    <section class="bg-white rounded-lg shadow p-5">
        <h2 class="font-semibold text-gray-900">Versions publiées</h2>
        <ul class="mt-2 divide-y text-sm">
            @forelse($policies as $policy)
                <li class="py-2">v{{ $policy->version }} · {{ $policy->title }} · {{ $policy->published_at->format('d/m/Y H:i') }}
                    <a class="text-blue-600" href="{{ route('privacy-policies.show', $policy) }}">Lire</a></li>
            @empty
                <li class="py-2 text-gray-500">Aucune version : le partage hors du club ne peut pas encore être consenti.</li>
            @endforelse
        </ul>
    </section>
</div>
@endsection

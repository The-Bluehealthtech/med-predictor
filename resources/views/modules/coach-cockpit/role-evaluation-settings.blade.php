@extends('layouts.app')

@section('title', 'Paramétrage externe — Rôle et apport')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
        <div>
            <a href="{{ route('modules.coach-cockpit') }}" class="text-sm text-blue-600 hover:underline">← Cockpit entraîneur</a>
            <h1 class="mt-2 text-3xl font-bold text-gray-900">Paramétrage externe — Rôle et apport</h1>
            <p class="mt-2 text-gray-600 max-w-3xl">
                Les poids du moteur sont stockés en base, versionnés et modifiables sans changer le code.
                Une version publiée devient immuable et peut être utilisée pour recalculer les scores.
            </p>
        </div>
        <div class="rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-600">
            Schéma JSON : <code>fit.role-evaluation-config.v1</code>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-green-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-800">
            <ul class="list-disc pl-5 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6">
        <section class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="text-lg font-semibold text-gray-900">Créer une version</h2>
            <p class="mt-1 text-sm text-gray-500">La nouvelle version démarre avec des poids égaux par dimension.</p>
            <form method="post" action="{{ route('modules.coach-cockpit.role-evaluation.settings.store') }}" class="mt-4 space-y-3">
                @csrf
                <label class="block text-sm font-medium text-gray-700">Libellé
                    <input name="label" required class="mt-1 w-full rounded-lg border-gray-300" placeholder="Ex. Modèle offensif v1">
                </label>
                <label class="block text-sm font-medium text-gray-700">Description
                    <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border-gray-300"></textarea>
                </label>
                <button class="rounded-lg bg-blue-600 px-4 py-2 text-white font-medium">Créer</button>
            </form>
        </section>

        <section class="rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="text-lg font-semibold text-gray-900">Importer un paramétrage JSON</h2>
            <p class="mt-1 text-sm text-gray-500">L'import crée toujours une nouvelle version en brouillon.</p>
            <form method="post" enctype="multipart/form-data" action="{{ route('modules.coach-cockpit.role-evaluation.settings.import') }}" class="mt-4 space-y-3">
                @csrf
                <input type="file" name="config_file" accept=".json,application/json" required class="block w-full text-sm">
                <button class="rounded-lg bg-gray-900 px-4 py-2 text-white font-medium">Importer</button>
            </form>
        </section>
    </div>

    <div class="space-y-6">
        @forelse($versions as $version)
            @php
                $weightMap = $version->weights
                    ->groupBy('position_family')
                    ->map(fn ($rows) => $rows->pluck('weight', 'dimension_key'));
            @endphp
            <section class="rounded-xl border border-gray-200 bg-white overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-200 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-semibold text-gray-900">#{{ $version->id }} — {{ $version->label }}</h2>
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold
                                {{ $version->status === 'published' ? 'bg-green-100 text-green-800' : ($version->status === 'archived' ? 'bg-gray-100 text-gray-700' : 'bg-amber-100 text-amber-800') }}">
                                {{ $version->status }}
                            </span>
                        </div>
                        @if($version->description)<p class="mt-1 text-sm text-gray-500">{{ $version->description }}</p>@endif
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('modules.coach-cockpit.role-evaluation.settings.export', $version) }}"
                           class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium">Exporter JSON</a>
                        @if($version->status === 'draft')
                            <form method="post" action="{{ route('modules.coach-cockpit.role-evaluation.settings.publish', $version) }}">
                                @csrf
                                <button class="rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white"
                                        onclick="return confirm('Publier cette configuration ? Elle deviendra immuable.')">Publier</button>
                            </form>
                        @endif
                        @if($version->status !== 'archived')
                            <form method="post" action="{{ route('modules.coach-cockpit.role-evaluation.settings.archive', $version) }}">
                                @csrf
                                <button class="rounded-lg border border-gray-300 px-3 py-2 text-sm font-medium">Archiver</button>
                            </form>
                        @endif
                    </div>
                </div>
                @if($version->status === 'draft')
                    <form method="post" action="{{ route('modules.coach-cockpit.role-evaluation.settings.update', $version) }}" class="p-5 space-y-5">
                        @csrf
                        @method('PUT')
                        <div class="grid md:grid-cols-2 gap-4">
                            <label class="block text-sm font-medium text-gray-700">Libellé
                                <input name="label" value="{{ $version->label }}" required class="mt-1 w-full rounded-lg border-gray-300">
                            </label>
                            <label class="block text-sm font-medium text-gray-700">Description
                                <input name="description" value="{{ $version->description }}" class="mt-1 w-full rounded-lg border-gray-300">
                            </label>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-gray-50 text-gray-600">
                                    <tr>
                                        <th class="px-3 py-2 text-left">Famille</th>
                                        <th class="px-3 py-2 text-left">Dimension</th>
                                        <th class="px-3 py-2 text-left">Poids</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                @foreach($families as $family)
                                    @php
                                        $keys = mb_strtolower($family) === 'gardien'
                                            ? array_keys($dimensions['goalkeeper'])
                                            : array_keys($dimensions['field']);
                                    @endphp
                                    @foreach($keys as $dimensionKey)
                                        <tr>
                                            <td class="px-3 py-2 font-medium text-gray-900">{{ $family }}</td>
                                            <td class="px-3 py-2"><code>{{ $dimensionKey }}</code></td>
                                            <td class="px-3 py-2">
                                                <input type="number"
                                                       name="weights[{{ $family }}][{{ $dimensionKey }}]"
                                                       step="0.0001"
                                                       min="0"
                                                       max="1"
                                                       required
                                                       value="{{ old('weights.'.$family.'.'.$dimensionKey, $weightMap[$family][$dimensionKey] ?? '') }}"
                                                       class="w-28 rounded-md border-gray-300">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-500">Pour chaque famille, la somme des poids doit être égale à 1,0000.</p>
                        <button class="rounded-lg bg-blue-600 px-4 py-2 text-white font-medium">Enregistrer les poids</button>
                    </form>
                @else
                    <div class="p-5">
                        <div class="grid md:grid-cols-2 xl:grid-cols-4 gap-3">
                            @foreach($weightMap as $family => $weights)
                                <div class="rounded-lg bg-gray-50 border border-gray-200 p-3">
                                    <h3 class="font-semibold text-gray-900">{{ $family }}</h3>
                                    <dl class="mt-2 space-y-1 text-xs">
                                        @foreach($weights as $dimensionKey => $weight)
                                            <div class="flex justify-between gap-3">
                                                <dt class="text-gray-500">{{ $dimensionKey }}</dt>
                                                <dd class="font-mono">{{ number_format((float) $weight, 4) }}</dd>
                                            </div>
                                        @endforeach
                                    </dl>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </section>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white p-6 text-gray-500">Aucune configuration.</div>
        @endforelse
    </div>
</div>
@endsection

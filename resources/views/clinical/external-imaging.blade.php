@extends('layouts.app')

@section('title', 'Imagerie — ' . ($player->full_name ?? $player->name))

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8 space-y-4">
    <a href="{{ route('clinical.external-data', ['player' => $player, 'tab' => 'imaging', 'back' => $back]) }}" class="text-sm text-blue-600">← Données des établissements</a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $item['label'] }}</h1>
        <p class="text-sm text-gray-600 mt-1">{{ $player->full_name ?? $player->name }} · {{ \App\Services\Fhir\ClinicalDataQuery::displayDate($item['date']) }}@if($item['source']) · {{ $item['source'] }}@endif · images lues sur le PACS (DICOMweb), rendues par FIT, non conservées.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[16rem_1fr] gap-4">
        <nav class="bg-white rounded-lg shadow p-2 space-y-1" aria-label="Séries">
            @forelse($series as $s)
                <a href="{{ route('clinical.dicomweb.study', ['player' => $player, 'study' => $study, 'series' => $s['uid'], 'back' => $back]) }}"
                   @if(($current['uid'] ?? null) === $s['uid']) aria-current="true" @endif
                   class="block rounded-md px-3 py-2 text-sm {{ ($current['uid'] ?? null) === $s['uid'] ? 'bg-blue-100 text-blue-900 font-semibold' : 'text-gray-700 hover:bg-gray-50' }}">
                    Série {{ $s['number'] ?? '—' }} · {{ $s['modality'] ?? '' }}
                    <span class="block text-xs font-normal text-gray-500">{{ $s['description'] ?? 'Sans description' }}{{ $s['instances'] ? ' · ' . $s['instances'] . ' image(s)' : '' }}</span>
                </a>
            @empty
                <p class="p-3 text-sm text-gray-500">Aucune série sur le PACS pour cet examen.</p>
            @endforelse
        </nav>

        <div class="bg-gray-950 rounded-lg overflow-hidden">
            @if($images)
                <div id="dw" data-images='@json($images)' data-frame-url="{{ route('clinical.dicomweb.frame', ['player' => $player, 'study' => $study, 'series' => $current['uid'], 'instance' => '__SOP__']) }}">
                    <div class="flex flex-wrap items-center gap-3 px-4 py-2 bg-gray-900 text-gray-100 text-sm">
                        @if(count($images) > 1)
                            <button type="button" data-step="-1" class="px-2 py-1 rounded bg-gray-700 hover:bg-gray-600" aria-label="Image précédente">◀</button>
                            <label class="flex items-center gap-2">Image
                                <input type="range" min="1" max="{{ count($images) }}" value="1" data-role="index" class="w-56" aria-label="Numéro d’image">
                                <output data-role="label">1 / {{ count($images) }}</output>
                            </label>
                            <button type="button" data-step="1" class="px-2 py-1 rounded bg-gray-700 hover:bg-gray-600" aria-label="Image suivante">▶</button>
                        @endif
                        <label class="flex items-center gap-2">Fenêtre
                            <select data-role="window" class="rounded bg-gray-800 border-gray-600 text-gray-100 text-sm">
                                @foreach($windows as $key => $w)<option value="{{ $key }}" data-center="{{ $w['center'] }}" data-width="{{ $w['width'] }}" @selected($key === (isset($windows['header']) ? 'header' : 'auto'))>{{ $w['label'] }}</option>@endforeach
                            </select>
                        </label>
                        <span data-role="status" class="text-gray-400" aria-live="polite"></span>
                    </div>
                    <div class="flex justify-center p-2 min-h-[28rem]"><img data-role="image" alt="Image 1 de la série" class="max-h-[75vh] object-contain"></div>
                </div>
            @else
                <p class="p-6 text-sm text-gray-200">Aucune image dans cette série.</p>
            @endif
        </div>
    </div>
</div>

@if($images)
<script>
(() => {
    const root = document.getElementById('dw');
    const images = JSON.parse(root.dataset.images);
    const img = root.querySelector('[data-role=image]');
    const range = root.querySelector('[data-role=index]');
    const label = root.querySelector('[data-role=label]');
    const select = root.querySelector('[data-role=window]');
    const status = root.querySelector('[data-role=status]');
    let index = 0;
    const load = () => {
        const current = images[index];
        const params = new URLSearchParams({ frame: String(current.frame) });
        const opt = select.selectedOptions[0];
        if (opt && opt.dataset.center !== '' && opt.dataset.width !== '') { params.set('center', opt.dataset.center); params.set('width', opt.dataset.width); }
        status.textContent = 'Chargement depuis le PACS…';
        img.alt = 'Image ' + (index + 1) + ' de la série';
        img.src = root.dataset.frameUrl.replace('__SOP__', encodeURIComponent(current.instance)) + '?' + params.toString();
        if (range) { range.value = String(index + 1); label.textContent = (index + 1) + ' / ' + images.length; }
    };
    img.addEventListener('load', () => { status.textContent = ''; });
    img.addEventListener('error', () => { status.textContent = 'Image non affichable (PACS indisponible ou format non décodable).'; });
    root.querySelectorAll('[data-step]').forEach(b => b.addEventListener('click', () => { index = Math.min(images.length - 1, Math.max(0, index + Number(b.dataset.step))); load(); }));
    if (range) range.addEventListener('change', () => { index = Number(range.value) - 1; load(); });
    select.addEventListener('change', load);
    document.addEventListener('keydown', e => {
        if (images.length < 2 || ['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement?.tagName)) return;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { index = Math.min(images.length - 1, index + 1); load(); }
        if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { index = Math.max(0, index - 1); load(); }
    });
    load();
})();
</script>
@endif
@endsection

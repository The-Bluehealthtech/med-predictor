@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8 space-y-4">
    @if($back)<a href="{{ $back }}" class="text-sm text-blue-600">← Retour</a>@endif
    <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="text-sm text-gray-600 mt-1">
                {{ $file['name'] }} · {{ number_format($file['size'] / 1024, 0, ',', ' ') }} Ko
                @if($meta) · DICOM {{ $meta['modality'] ?: '—' }} · {{ $meta['columns'] }} × {{ $meta['rows'] }} · {{ $meta['frames'] }} image(s) · {{ $meta['transfer_syntax'] }}@endif
            </p>
        </div>
        <a href="{{ $sourceUrl }}" class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-800 hover:bg-gray-50">Fichier source</a>
    </div>

    <div class="bg-gray-950 rounded-lg overflow-hidden">
        @if($kind === 'dicom' && $meta)
            <div id="mv" data-frame-url="{{ $frameUrl }}" data-frames="{{ $meta['frames'] }}" class="space-y-0">
                <div class="flex flex-wrap items-center gap-3 px-4 py-2 bg-gray-900 text-gray-100 text-sm">
                    @if($meta['frames'] > 1)
                        <button type="button" data-step="-1" class="px-2 py-1 rounded bg-gray-700 hover:bg-gray-600" aria-label="Image précédente">◀</button>
                        <label class="flex items-center gap-2">Image
                            <input type="range" min="1" max="{{ $meta['frames'] }}" value="1" data-role="frame" class="w-48" aria-label="Numéro d’image">
                            <output data-role="frame-label">1 / {{ $meta['frames'] }}</output>
                        </label>
                        <button type="button" data-step="1" class="px-2 py-1 rounded bg-gray-700 hover:bg-gray-600" aria-label="Image suivante">▶</button>
                    @endif
                    @if(($meta['samples'] ?? 1) === 1 && $meta['photometric'] !== 'PALETTE COLOR')
                        <label class="flex items-center gap-2">Fenêtre
                            <select data-role="window" class="rounded bg-gray-800 border-gray-600 text-gray-100 text-sm">
                                @foreach($windows as $key => $w)<option value="{{ $key }}" data-center="{{ $w['center'] }}" data-width="{{ $w['width'] }}" @selected($key === (isset($windows['header']) ? 'header' : 'auto'))>{{ $w['label'] }}</option>@endforeach
                            </select>
                        </label>
                    @endif
                    <span data-role="status" class="text-gray-400" aria-live="polite"></span>
                </div>
                <div class="flex justify-center p-2 min-h-[24rem]">
                    <img data-role="image" alt="{{ $title }} — image 1" class="max-h-[75vh] object-contain"
                         src="{{ $frameUrl }}?{{ http_build_query(array_filter(['frame' => 0, 'center' => $windows['header']['center'] ?? null, 'width' => $windows['header']['width'] ?? null], fn ($v) => $v !== null)) }}">
                </div>
            </div>
        @elseif($kind === 'dicom')
            <p class="p-6 text-sm text-amber-200">Ce fichier DICOM ne contient pas d’image lisible par la visionneuse (objet sans pixels ou en-tête invalide). Téléchargez le fichier source pour l’ouvrir sur une station adaptée.</p>
        @elseif($kind === 'image')
            <div class="flex justify-center p-2"><img src="{{ $sourceUrl }}" alt="{{ $title }}" class="max-h-[80vh] object-contain"></div>
        @elseif($kind === 'raster')
            <div class="flex justify-center p-2"><img src="{{ $frameUrl }}" alt="{{ $title }}" class="max-h-[80vh] object-contain"></div>
        @elseif($kind === 'pdf')
            <iframe src="{{ $sourceUrl }}" title="{{ $title }}" class="w-full h-[80vh] bg-white"></iframe>
        @else
            <p class="p-6 text-sm text-gray-200">Format non affichable dans le navigateur. Téléchargez le fichier source.</p>
        @endif
    </div>
    @if($kind === 'dicom' && $meta)
        <p class="text-xs text-gray-500">Rendu côté serveur à partir du fichier DICOM d’origine (aucune donnée ajoutée). Le fenêtrage ne modifie pas le fichier source.</p>
    @endif
</div>

@if($kind === 'dicom' && $meta)
<script>
(() => {
    const root = document.getElementById('mv');
    const img = root.querySelector('[data-role=image]');
    const range = root.querySelector('[data-role=frame]');
    const label = root.querySelector('[data-role=frame-label]');
    const select = root.querySelector('[data-role=window]');
    const status = root.querySelector('[data-role=status]');
    const frames = Number(root.dataset.frames);
    let frame = 0;
    const load = () => {
        const params = new URLSearchParams({ frame: String(frame) });
        const opt = select ? select.selectedOptions[0] : null;
        if (opt && opt.dataset.center !== '' && opt.dataset.width !== '') { params.set('center', opt.dataset.center); params.set('width', opt.dataset.width); }
        status.textContent = 'Chargement…';
        img.alt = img.alt.replace(/image \d+$/, 'image ' + (frame + 1));
        img.src = root.dataset.frameUrl + '?' + params.toString();
        if (range) { range.value = String(frame + 1); label.textContent = (frame + 1) + ' / ' + frames; }
    };
    img.addEventListener('load', () => { status.textContent = ''; });
    img.addEventListener('error', () => { status.textContent = 'Image non décodable par la visionneuse : téléchargez le fichier source.'; });
    root.querySelectorAll('[data-step]').forEach(b => b.addEventListener('click', () => { frame = Math.min(frames - 1, Math.max(0, frame + Number(b.dataset.step))); load(); }));
    if (range) range.addEventListener('change', () => { frame = Number(range.value) - 1; load(); });
    if (select) select.addEventListener('change', load);
    document.addEventListener('keydown', e => {
        if (frames < 2 || ['INPUT', 'SELECT', 'TEXTAREA'].includes(document.activeElement?.tagName)) return;
        if (e.key === 'ArrowRight' || e.key === 'ArrowDown') { frame = Math.min(frames - 1, frame + 1); load(); }
        if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') { frame = Math.max(0, frame - 1); load(); }
    });
})();
</script>
@endif
@endsection

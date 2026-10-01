@php
    $odontogramReadonly = $odontogramReadonly ?? false;
    $odontogramData = $odontogramData ?? old('dental_data', $sectionValues['dental_data'] ?? []);
    if(is_string($odontogramData)) $odontogramData = json_decode($odontogramData, true) ?? [];
@endphp
<div data-fit-odontogram data-readonly="{{ $odontogramReadonly ? 'true' : 'false' }}" data-labels="{{ json_encode(__('odontogram'), JSON_UNESCAPED_UNICODE) }}" data-initial="{{ json_encode((object)$odontogramData, JSON_UNESCAPED_UNICODE) }}" class="my-4">
    @unless($odontogramReadonly)
        <input type="hidden" id="dental_data" name="dental_data" data-dental-value value="{{ json_encode((object)$odontogramData, JSON_UNESCAPED_UNICODE) }}">
    @endunless
</div>
@once
@push('scripts')
    <script src="{{ asset('js/odontogram.js') }}" defer></script>
@endpush
@endonce

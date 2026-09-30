@php
    $locale = app()->getLocale() === 'en' ? 'en' : 'fr';
    $key = $section.'_icd11_selection';
    $history = $pcma->result_json['medical_history'] ?? [];
    $selected = $history[$section.'_icd11_codes'] ?? [];
    if ($section === 'cardiovascular' && !$selected && !empty($history['cardiovascular_icd11'])) {
        $legacy = $history['cardiovascular_icd11'];
        if (preg_match('~/([0-9]+)$~', $legacy['entity_uri'] ?? '', $m)) {
            $selected = [['id'=>$m[1], 'code'=>$legacy['code'], 'label'=>$legacy['label_'.$locale],
                'release'=>$legacy['release'], 'language'=>$locale]];
        }
    }
    $oldChoices = old($key);
    if (is_string($oldChoices)) {
        $decoded = json_decode($oldChoices, true);
        if (is_array($decoded)) $selected = $decoded;
    }
    $selected = array_values(array_filter($selected, fn($x)=>is_array($x) && isset($x['id'], $x['release'], $x['language'])));
@endphp
<div class="pcma-icd11" data-url="{{ route('pcma.icd11.search') }}" data-language="{{ $locale }}"
     data-selected="{{ json_encode($selected) }}" data-messages="{{ json_encode(__('pcma_icd11')) }}">
    <label for="{{ $section }}_icd11_search" class="block text-sm font-medium text-gray-700 mb-2">{{ __($label) }} — CIM-11</label>
    <input id="{{ $section }}_icd11_search" type="search" autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="{{ __('pcma_icd11.search') }}">
    <p data-status role="status" class="text-xs text-gray-500 mt-1"></p>
    <div data-results class="max-h-60 overflow-y-auto"></div>
    <div data-selected-list class="mt-2 space-y-1"></div>
    <input data-selection type="hidden" name="{{ $key }}" value="{{ json_encode(array_map(fn($x)=>['id'=>$x['id'],'release'=>$x['release'],'language'=>$x['language']], $selected)) }}">
    <p class="text-xs text-gray-500 mt-1">{{ __('pcma_icd11.manual') }}</p>
    @if($section === 'surgical')<p class="text-xs text-gray-500">{{ __('pcma_icd11.surgical') }}</p>@endif
    <label for="{{ $textField }}" class="block text-sm font-medium text-gray-700 mt-2">{{ __('pcma_medications.notes') }}</label>
    <textarea id="{{ $textField }}" name="{{ $textField }}" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old($textField, $history[$textField] ?? ($section === 'cardiovascular' ? old('medical_history', '') : '')) }}</textarea>
</div>
@once
<script src="{{ asset('js/pcma-icd11.js') }}" defer></script>
@endonce

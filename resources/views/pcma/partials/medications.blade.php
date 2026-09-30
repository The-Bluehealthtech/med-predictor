@php
    $savedMedicationProducts = $pcma->result_json['medical_history']['medication_products'] ?? [];
    $oldSelection = json_decode(old('medication_selection', 'null'), true);
    if (is_array($oldSelection)) $savedMedicationProducts = array_values(array_filter($oldSelection, fn ($item) => is_array($item) && is_string($item['id'] ?? null)));
    $savedMedicationProducts = app(\App\Services\MedicationCatalogue::class)->forEditing($savedMedicationProducts);
@endphp
<div id="pcma-medication-catalogue" data-endpoint="{{ route('pcma.medications.search') }}"
     data-labels="{{ json_encode(trans('pcma_medications')) }}">
    <label for="medication_search" class="block text-sm font-medium text-gray-700 mb-2">{{ __('pcma.medications_label') }}</label>
    <input id="medication_search" type="search" autocomplete="off" class="w-full px-3 py-2 border border-gray-300 rounded-md"
           placeholder="{{ __('pcma_medications.search') }}">
    <p class="text-xs text-gray-500 mt-1">{{ __('pcma_medications.no_atc') }}</p>
    <div id="medication_results" class="mt-2 space-y-1"></div>
    <div id="medication_selected" class="mt-2 space-y-2"></div>
    <input type="hidden" id="medication_selection" name="medication_selection"
           value="{{ json_encode($savedMedicationProducts) }}">
    <label for="medications" class="block text-sm font-medium text-gray-700 mt-3">{{ __('pcma_medications.notes') }}</label>
    <textarea id="medications" name="medications" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('medications', $pcma->result_json['medical_history']['medications'] ?? '') }}</textarea>
</div>
<script src="{{ asset('js/pcma-medication-catalogue.js') }}" defer></script>

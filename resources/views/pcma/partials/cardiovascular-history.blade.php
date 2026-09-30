<label for="cardiovascular_icd11" class="block text-sm font-medium text-gray-700 mb-2">{{ __('pcma.cardiovascular_history_label') }} — CIM-11</label>
<select id="cardiovascular_icd11" name="cardiovascular_icd11" class="w-full px-3 py-2 border border-gray-300 rounded-md">
    <option value="">{{ __('pcma_medications.unspecified') }}</option>
    @foreach(config('pcma_icd11.cardiovascular', []) as $code => $entry)
        <option value="{{ $code }}" @selected(old('cardiovascular_icd11', $pcma->result_json['medical_history']['cardiovascular_icd11']['code'] ?? '') === $code)>{{ $code }} — {{ app()->getLocale() === 'en' ? $entry['label_en'] : $entry['label_fr'] }}</option>
    @endforeach
</select>
<p class="text-xs text-gray-500 mt-1">{{ __('pcma_workflow.icd11_subset') }}</p>
<label for="cardiovascular_history" class="block text-sm font-medium text-gray-700 mt-2">{{ __('pcma_medications.notes') }}</label>
<textarea id="cardiovascular_history" name="cardiovascular_history" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md">{{ old('cardiovascular_history', old('medical_history', $pcma->result_json['medical_history']['cardiovascular_history'] ?? '')) }}</textarea>

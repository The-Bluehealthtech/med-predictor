<div class="bg-blue-50 border border-blue-200 rounded-lg p-6 my-4" id="medical-icd11"
 data-url="{{ route('health-records.icd11.search') }}" data-language="{{ app()->getLocale()==='fr'?'fr':'en' }}"
 data-error="{{ __('medical_aut.icd_error') }}" data-empty="{{ __('medical_aut.no_results') }}"
 data-existing="{{ json_encode($healthRecord->icd11_diagnoses ?? []) }}">
<h2 class="text-lg font-semibold">{{ __('medical_aut.icd_title') }}</h2>
<p>{{ __('medical_aut.icd_help') }}</p>
<label for="medical-icd-query">{{ __('medical_aut.search') }}</label>
<input id="medical-icd-query" class="w-full border rounded p-2" autocomplete="off" maxlength="100">
<p id="medical-icd-status" role="status" aria-live="polite"></p>
<div id="medical-icd-results"></div>
<ul id="medical-icd-chosen"></ul>
<input type="hidden" name="icd11_selection" id="medical-icd-selection"
 value="{{ old('icd11_selection',json_encode(collect($healthRecord->icd11_diagnoses ?? [])->map(fn($x)=>array_intersect_key($x,array_flip(['id','release','language'])))->values())) }}">
@once
@push('scripts')
<script src="{{ asset('js/medical-icd11.js') }}" defer></script>
@endpush
@endonce
</div>

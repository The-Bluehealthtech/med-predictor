@php
    $all=old('medications',$healthRecord->medications ?? []);
    if(is_string($all)) $all=json_decode($all,true) ?? preg_split('/\r?\n/',$all);
    $all=is_array($all)?$all:[];
    $rx=array_values(array_filter($all,fn($p)=>is_array($p)&&($p['source']??null)==='RxNorm'));
    $notes=array_values(array_filter($all,fn($p)=>!is_array($p)||($p['source']??null)!=='RxNorm'));
@endphp
<div id="pcma-medication-catalogue" data-endpoint="{{ route('pcma.medications.search') }}"
 data-labels="{{ json_encode(trans('pcma_medications')) }}">
<label for="medication_search">{{ __('pcma.medications_label') }}</label>
<input id="medication_search" type="search" autocomplete="off" class="w-full border rounded p-2"
 placeholder="{{ __('pcma_medications.search') }}">
<p>{{ __('pcma_medications.no_atc') }}</p>
<div id="medication_results"></div><div id="medication_selected"></div>
<input type="hidden" id="medication_selection" name="rxnorm_selection"
 value="{{ old('rxnorm_selection',json_encode($rx)) }}">
<label for="medications">{{ __('pcma_medications.notes') }}</label>
<textarea id="medications" name="medications" class="w-full border rounded p-2" rows="3">{{ json_encode($notes,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT) }}</textarea>
</div>
@once @push('scripts')
<script src="{{ asset('js/pcma-medication-catalogue.js') }}" defer></script>
@endpush @endonce

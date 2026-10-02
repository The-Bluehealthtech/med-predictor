{{-- PDF de l'AUT et dépôt dans ADAMS. $autId : demande enregistrée (null avant le premier enregistrement). --}}
<div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 my-4 space-y-3">
<p class="text-sm text-indigo-900">{{ __('medical_aut.adams_note') }}</p>
<div class="flex flex-wrap items-center gap-3">
@if($autId)
<a href="{{ route('medical-aut.pdf',[$healthRecord->id,$autId]) }}" class="inline-flex items-center bg-indigo-700 text-white rounded-lg px-4 py-2 font-semibold">⬇ {{ __('medical_aut.pdf_download') }}</a>
@endif
<a href="{{ config('medical_aut.adams_url') }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center border border-indigo-300 bg-white text-indigo-800 rounded-lg px-4 py-2 font-semibold">{{ __('medical_aut.adams_open') }} ↗</a>
<a href="{{ config('medical_aut.adams_help_url') }}" target="_blank" rel="noopener noreferrer" class="text-sm text-indigo-700 underline">{{ __('medical_aut.adams_help') }}</a>
<a href="{{ route('medical-aut.source',$healthRecord->id) }}" class="text-sm text-gray-600 underline">{{ __('medical_aut.source') }}</a>
</div>
</div>

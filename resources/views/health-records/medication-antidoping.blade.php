@php($alert=$medication['antidoping'] ?? ['status'=>'unresolved'])
@php($hit=($alert['status']??null)==='mentions_found')
<div role="status" class="border rounded-md p-3 mt-2 {{ $hit ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300' }}">
<strong>{{ __('pcma_medications.'.($hit?'alert_title':(($alert['status']??null)==='unavailable'?'alert_unavailable':'alert_unresolved'))) }}</strong>
@if($hit)
<p>{{ __('pcma_medications.alert_note') }}</p>
@foreach($alert['matches'] ?? [] as $match)
<details class="mt-2">
<summary>{{ $match['ingredient']['name'] }} — {{ $match['category'] }} · {{ __('pcma_medications.alert_context') }}</summary>
@foreach($match['context'] ?? [] as $row)<p>{{ $row['text'] }}</p>@endforeach
</details>
@endforeach
@endif
</div>

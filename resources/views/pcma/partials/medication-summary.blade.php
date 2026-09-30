@foreach($pcma->result_json['medical_history']['medication_products'] ?? [] as $medication)
<div class="mt-2 border border-gray-200 rounded-md p-2">
    <strong>{{ $medication['name'] }}</strong>
    <p>{{ implode(', ', $medication['substances'] ?? []) }}</p>
    @if($medication['presentation'] ?? null)<p>{{ $medication['presentation']['name'] }}</p>@endif
    @foreach(['dose','route','frequency'] as $field)
        <p>{{ __('pcma_medications.'.$field) }} : {{ $medication[$field] ?? '—' }}</p>
    @endforeach
    <p class="text-xs text-gray-500">{{ $medication['source'] ?? '—' }} · {{ $medication['rxcui'] ?? $medication['id'] ?? '—' }} · {{ $medication['version'] ?? '—' }}</p>
</div>
@endforeach

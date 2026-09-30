@foreach(['cardiovascular'=>'pcma.cardiovascular_history_label','surgical'=>'pcma.surgical_history_label','allergies'=>'pcma.allergies_label'] as $section=>$label)
    @if(($onlySection ?? $section) === $section && !empty($pcma->result_json['medical_history'][$section.'_icd11_codes']))
        <div class="mt-3">
            <h4 class="font-medium">{{ __($label) }} — CIM-11</h4>
            @foreach($pcma->result_json['medical_history'][$section.'_icd11_codes'] as $entry)
                <p>{{ $entry['code'] }} — {{ $entry['label'] }}</p>
                @if(($onlySection ?? $section) === $section && !empty($entry['coding_note']))<p class="text-sm text-gray-500">{{ $entry['coding_note'] }}</p>@endif
            @endforeach
        </div>
    @endif
@endforeach
